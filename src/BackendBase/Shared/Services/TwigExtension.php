<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services;

use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

use function count;
use function gettext;
use function ngettext;
use function var_dump;

class TwigExtension extends AbstractExtension implements GlobalsInterface, ExtensionInterface
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
            new TwigFunction('translate', [$this, 'translate']),
            new TwigFunction('varDump', [$this, 'varDump']),
        ];
    }

    public function getFilters()
    {
        return [
            //  new TwigFilter('yourFilter', [$this, 'methodName']),
        ];
    }

    public function translate($messageId, ...$args): string
    {
        if (count($args) === 0) {
            return gettext($messageId);
        }

        return ngettext($messageId, $messageId . '-plural', ...$args);
    }

    public function varDump(...$arguments): void
    {
        var_dump($arguments);
    }
}
