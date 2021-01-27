<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Forms;

use BackendBase\Shared\Factory\RequestHandlerFactory;
use BackendBase\Shared\Interfaces\MezzioHandlerConfigProvider;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider implements MezzioHandlerConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies'  => $this->getDependencies(),
        ];
    }

    public function registerRoutes(Application $app, MiddlewareFactory $factory): void
    {
        $app->get('/forms', Handler\GetForms::class, 'forms.list');
        $app->get('/forms/{formId}', Handler\GetFormsData::class, 'forms.data');
        $app->patch('/forms/{formId}', Handler\ChangeFormsData::class, 'forms.update');
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'factories'  => [
                Handler\GetForms::class => RequestHandlerFactory::class,
                Handler\GetFormsData::class => RequestHandlerFactory::class,
                Handler\ChangeFormsData::class => RequestHandlerFactory::class,
            ],
        ];
    }
}
