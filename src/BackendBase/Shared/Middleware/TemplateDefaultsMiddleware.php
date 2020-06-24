<?php
declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Template\TemplateRendererInterface;

class TemplateDefaultsMiddleware implements MiddlewareInterface
{
    /** @var TemplateRendererInterface */
    private $templateRenderer;

    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        // Inject the current user, or null if there isn't one.
        $this->templateRenderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'baseUrl', // This is named security so it will not interfere with your user admin pages
            $request->getAttribute('base-url')
        );

        return $handler->handle($request);
    }
}