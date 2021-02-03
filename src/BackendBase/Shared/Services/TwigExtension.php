<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;
use Twig\Extension\ExtensionInterface;
use function ngettext;
use function var_dump;

class TwigExtension extends AbstractExtension implements GlobalsInterface,ExtensionInterface
{
    private array $globals;

    public function __construct(?array $globals = [])
    {
        $this->globals = $globals ?? [];
    }

    public function getGlobals(): array
    {
        return $this->globals;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('translate', 'gettext'),
            new TwigFunction('varDump', [$this, 'varDump']),
            new TwigFunction('plural', [$this, 'translatePlural']),
         //   new TwigFunction('yourFunction', [$this, 'methodName']),
        ];
    }

    public function getFilters()
    {
        return [
            //  new TwigFilter('yourFilter', [$this, 'methodName']),
        ];
    }

    public function translatePlural($messageId, $number): string
    {
        return ngettext($messageId, $messageId . '_PLURAL', $number);
    }

    public function varDump(...$arguments): void
    {
        var_dump($arguments);
    }
}
