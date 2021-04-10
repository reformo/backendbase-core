<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Selami\Stdlib\Git\Version;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use PSR7Sessions\Storageless\Session\SessionInterface;
use function stripos;

class TemplateDefaultsMiddleware implements MiddlewareInterface
{
    private TemplateRendererInterface $templateRenderer;
    private array $config;

    public function __construct(TemplateRendererInterface $templateRenderer, array $config)
    {
        $this->templateRenderer = $templateRenderer;
        $this->config           = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->templateRenderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'baseUrl',
            $request->getAttribute('base-url')
        );
        /**
         * @var $session SessionInterface;
         */
        $session     = $request->getAttribute('session');

        $sessionData = $session->jsonSerialize();
        $this->templateRenderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'sessionData',
            $sessionData
        );

        $request = $request->withAttribute('cdn-url', $this->config['app']['cdn-url']);

        $userAgent = $request->getHeaderLine('Accept');
        $isWebP    = 0;
        if (stripos($userAgent, 'webp') !== false) {
            $isWebP = 1;
        }

        $this->templateRenderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'isWebP',
            $isWebP
        );
        $config                     = $this->config;
        $config['selectedLanguage'] = $request->getAttribute('selectedLanguage');
        $config['selectedRegion']   = $request->getAttribute('selectedRegion');
        $config['appData']          = $request->getAttribute('appData');
        $config['requestAttributes'] = $request->getAttributes();

        $this->templateRenderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'config',
            $config
        );
        $userAgent    = $request->getHeaderLine('user-agent');
        $isLightHouse = 0;
        if (stripos($userAgent, 'lighthouse') !== false) {
            $isLightHouse = 1;
        }

        $this->templateRenderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'isLightHouse',
            $isLightHouse
        );
        $gitVersion = Version::short();
        $this->templateRenderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'gitVersion',
            $gitVersion
        );

        return $handler->handle($request);
    }
}
