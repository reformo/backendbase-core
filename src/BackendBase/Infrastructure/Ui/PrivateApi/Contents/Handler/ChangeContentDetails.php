<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use BackendBase\Domain\Collections\Interfaces\CollectionQuery;
use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\ContentDetail;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use Selami\Stdlib\Arrays\PayloadSanitizer;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_key_exists;
use function array_keys;
use function array_values;
use function implode;
use function json_decode;
use function str_contains;
use function str_replace;
use function strip_tags;

use const JSON_OBJECT_AS_ARRAY;
use const JSON_THROW_ON_ERROR;

class ChangeContentDetails implements RequestHandlerInterface
{
    private ContentRepository $contentsRepository;
    private GenericRepository $genericRepository;
    private CollectionQuery $collectionQuery;
    private array $config;

    public function __construct(
        ContentRepository $contentsRepository,
        GenericRepository $genericRepository,
        CollectionQuery $collectionQuery,
        array $config
    ) {
        $this->contentsRepository = $contentsRepository;
        $this->genericRepository  = $genericRepository;
        $this->collectionQuery    = $collectionQuery;
        $this->config             = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Contents::CMS_EDIT) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to add new content');
        }

        $loggedUserId    = $request->getAttribute('loggedUserId');
        $contentDetailId = $request->getAttribute('contentDetailId');
        $contentId       = $request->getAttribute('contentId');

        $contentMetadata = $this->contentsRepository->getContentById($contentId);
        $template        = $this->collectionQuery->findByKey($contentMetadata['template']);
        $model           = json_decode($template->metadata()['itemData']['_model'], (bool) JSON_OBJECT_AS_ARRAY, 512, JSON_THROW_ON_ERROR);
        $bodyInputs      = [];
        foreach ($model as $group) {
            foreach ($group['inputs'] as $input) {
                $bodyInputs[$input['name']] = $input['type'];
            }
        }

        $allowHtml  = [
            '$.description' => ['allowedTags' => 'b;strong;'],
        ];
        $parsedBody = $request->getParsedBody();
        // return new JsonResponse($parsedBody);
        $payload = PayloadSanitizer::sanitize($parsedBody, $allowHtml);
        unset($payload['body'], $payload['bodyFulltext']);

        if (array_key_exists('body', $parsedBody) && ! empty($parsedBody['body'])) {
            $keys             = array_keys($parsedBody['body']);
            $allowHtmlForBody = [];
            foreach ($keys as $key) {
                if (! array_key_exists($key, $bodyInputs) || ! str_contains($bodyInputs[$key], 'html')) {
                    continue;
                }

                $allowHtmlForBody['$.' . $key] = [
                    'allowedTags' => 'a|href,class,style;img|src,class,style,alt,srcset;ul;ol;li;p;h1;h2;h3;h4;h5;quote;b;strong;br;div|class;table;tr;td;figure|class;oembed|url,class;figcaption',
                    'urlPrefixes' => 'http;https',
                ];
            }

            $payload['body'] = PayloadSanitizer::sanitize($parsedBody['body'], $allowHtmlForBody);
        }

        foreach ($payload['body'] as $key => $value) {
            $payload['body'][$key] = str_replace(
                [
                    $this->config['app']['cdn-url'],
                ],
                ['{cdnUrl}'],
                $value
            );
        }

        //return new JsonResponse($payload);
        /**
         * @var $content ContentDetail
         */
        $content = $this->genericRepository->findGeneric(ContentDetail::class, $contentDetailId);
        $content->setIsActive($payload['isActive'] ?? $content->isActive());
        $content->setTitle($payload['title'] ?? $content->title());
        $content->setSlug($payload['slug'] ?? $content->slug());
        $content->setSerpTitle($payload['serpTitle'] ?? $content->serpTitle());
        $content->setDescription($payload['description'] ?? $content->description());
        $content->setKeywords($payload['keywords'] ?? $content->keywords());
        $content->setBody($payload['body'] ?? $content->body());
        $content->setBodyFulltext(
            array_key_exists('body', $payload) ?
                strip_tags(implode(' ', array_values($payload['body'])))
                : $content->bodyFulltext()
        );

        $this->genericRepository->persistGeneric($content);

        return new EmptyResponse(204);
    }
}
