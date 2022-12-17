<?php

declare(strict_types=1);

namespace BackendBase\PublicWeb\Contents;

use BackendBase\Domain\Contents\Exception\ContentNotFound;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Shared\Services\MessageBus\Interfaces\QueryBus;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function str_replace;

class PageHandler implements RequestHandlerInterface
{
    private $queryBus;

    public function __construct(
        QueryBus $queryBus,
        private ?\Mezzio\Template\TemplateRendererInterface $template,
        private ContentRepository $contentRepository,
        private array $config
    ) {
        $this->queryBus          = $queryBus;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $pageSlug = '/' . $request->getAttribute('pageSlug');
        try {
            $page = $this->contentRepository->getContentBySlug($pageSlug, $request->getAttribute('selectedLanguage'), $request->getAttribute('selectedRegion'));
            foreach ($page['body'] as $key => $value) {
                $page['body'][$key] = str_replace('{cdnUrl}', $this->config['app']['cdn-url'], $value);
            }

            $template = $page['templateFile'];
            $data     = ['page' => $page];
        } catch (ContentNotFound) {
            $template = 'error::404';
            $data     = ['error' => 404];
        } catch (Throwable $throwable) {
            $template = 'error::500';
            $data     = ['error' => $throwable->getMessage()];
        }

        return new HtmlResponse($this->template->render($template, $data));
    }
}
