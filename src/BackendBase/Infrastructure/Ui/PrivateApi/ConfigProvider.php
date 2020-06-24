<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi;

use BackendBase\PrivateApi\Collections\ConfigProvider as CollectionsConfigProvider;
use BackendBase\PrivateApi\Contents\ConfigProvider as ContentsConfigProvider;
use BackendBase\PrivateApi\IdentityAndAccess\ConfigProvider as IdentityAndAccessConfigProvider;
use BackendBase\PrivateApi\Forms\ConfigProvider as FormsConfigProvider;

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use BackendBase\Shared\Interfaces\MezzioHandlerConfigProvider;
use function array_merge_recursive;

class ConfigProvider
{
    /** @var MezzioHandlerConfigProvider[] */
    private $modules = [];

    public function __construct()
    {
        $this->addConfigProviders(new IdentityAndAccessConfigProvider());
        $this->addConfigProviders(new CollectionsConfigProvider());
        $this->addConfigProviders(new ContentsConfigProvider());
        $this->addConfigProviders(new FormsConfigProvider());
    }

    private function addConfigProviders(MezzioHandlerConfigProvider $configProvider) : void
    {
        $this->modules[] = $configProvider;
    }

    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'module-name'  => 'PrivateApi',
        ];
    }

    public function registerRoutes(Application $app, MiddlewareFactory $factory) : void
    {
        foreach ($this->modules as $module) {
            $module->registerRoutes($app, $factory);
        }
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        $dependencies = [];
        foreach ($this->modules as $module) {
            $dependencies = array_merge_recursive($dependencies, $module->getDependencies());
        }

        return $dependencies;
    }
}
