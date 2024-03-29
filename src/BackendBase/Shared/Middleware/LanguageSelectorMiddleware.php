<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Locale;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_shift;
use function bind_textdomain_codeset;
use function bindtextdomain;
use function copy;
use function explode;
use function file_exists;
use function filemtime;
use function glob;
use function implode;
use function in_array;
use function putenv;
use function setlocale;
use function strtoupper;
use function textdomain;
use function trim;
use function unlink;

use const LC_MESSAGES;

final class LanguageSelectorMiddleware implements MiddlewareInterface
{
    public function __construct(private array $config)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri              = $request->getUri();
        $url              = $uri->getPath();
        $selectedLanguage = $this->config['i18n']['default-language'];
        $selectedRegion   = $this->config['i18n']['default-region'];
        if ($url !== '/') {
            $urlParts = explode('/', trim($url, '/'));
            $lang     = array_shift($urlParts);
            if (in_array($lang, $this->config['i18n']['valid-languages'])) {
                $selectedLanguage = $lang;
                $request          = $request->withUri($uri->withPath('/' . implode('/', $urlParts)));
            }

            if (in_array($lang, $this->config['i18n']['valid-regions'])) {
                $selectedRegion = $lang;
            }
        }

        $this->setLocale($selectedLanguage, $request->getAttribute('moduleName'));
        $request = $request->withAttribute('selectedLanguage', $selectedLanguage)
            ->withAttribute('selectedRegion', $selectedRegion);

        return $handler->handle($request);
    }

    private function setLocale(string $locale, string $domain): void
    {
        $localeFile = 'data/cache/locale/' . $locale . '/LC_MESSAGES/' . $domain . '.mo';
        if (! file_exists($localeFile)) {
            return;
        }

        $modifiedTime      = filemtime($localeFile);
        $localeFileRuntime = 'data/cache/locale/' . $locale . '/LC_MESSAGES/' . $domain . '_' . $modifiedTime . '.mo';
        if (! file_exists($localeFileRuntime)) {
            $dir = glob('data/cache/locale/' . $locale . '/LC_MESSAGES/' . $domain . '_*.mo');
            foreach ($dir as $file) {
                unlink($file);
            }

            copy($localeFile, $localeFileRuntime);
        }

        $domain .= '_' . $modifiedTime;
        $lang    = $locale . '_' . strtoupper($locale === 'en' ?  'US' : $locale);
        putenv("LANGUAGE={$lang}");
        setlocale(LC_MESSAGES, $lang . '.UTF-8');
        Locale::setDefault($lang);
        bindtextdomain($domain, 'data/cache/locale');
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
    }
}
