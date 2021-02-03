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

class PageHandler implements RequestHandlerInterface
{
    private ?TemplateRendererInterface $template = null;
    private $config;
    private $queryBus;
    private ContentRepository $contentRepository;

    public function __construct(
        QueryBus $queryBus,
        TemplateRendererInterface $template,
        ContentRepository $contentRepository,
        array $config
    ) {
        $this->template          = $template;
        $this->config            = $config;
        $this->queryBus          = $queryBus;
        $this->contentRepository = $contentRepository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $pageSlug = '/' . $request->getAttribute('pageSlug');
        try {
            $page     = $this->contentRepository->getContentBySlug($pageSlug, $request->getAttribute('selectedLanguage'), $request->getAttribute('selectedRegion'));
            $template = $page['templateFile'];
            $data     = ['page' => $page];
        } catch (ContentNotFound $exception) {
            $template = 'error::404';
            $data     = ['error' => 404];
        } catch (Throwable $throwable) {
            $template = 'error::500';
            $data     = ['error' => $throwable->getMessage()];
        }

        return new HtmlResponse($this->template->render($template, $data));
    }
}
