<?php

declare(strict_types=1);

namespace BackendBase\PublicWeb\Contents;

use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Shared\Services\MessageBus\Interfaces\QueryBus;
use Keiko\Uuid\Shortener\Dictionary;
use Keiko\Uuid\Shortener\Shortener;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function nl2br;
use function strip_tags;

class PageHandler implements RequestHandlerInterface
{
    /** @var TemplateRendererInterface|null */
    private $template;
    private $config;
    private $queryBus;
    private ContentRepository $contentRepository;

    public function __construct(
        QueryBus $queryBus,
        TemplateRendererInterface $template,
        ContentRepository $contentRepository,
        array $config
    ) {
        $this->template                 = $template;
        $this->config                   = $config;
        $this->queryBus                 = $queryBus;
        $this->contentRepository        = $contentRepository;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $guard            = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        $token            = $guard->generateToken();
        $pageSlug = $request->getAttribute('pageSlug');


        $shortener = Shortener::make(
            Dictionary::createUnmistakable() // or pass your own characters set
        );
        $page      = $this->contentRepository->getContentBySlugForClient($pageSlug);



        $data = [
            'page' => $page,
        ];

        return new HtmlResponse($this->template->render('app::default-page', $data));
    }
}
