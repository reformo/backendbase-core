<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Locale;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use const LC_ALL;
use function bind_textdomain_codeset;
use function bindtextdomain;
use function copy;
use function file_exists;
use function filemtime;
use function glob;
use function putenv;
use function setlocale;
use function textdomain;
use function unlink;

final class LanguageSelectorMiddleware implements MiddlewareInterface
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $uri = $request->getUri();
        $url = $uri->getPath();
        $selectedLanguage = $this->config['multilingual']['default-language'];
        if ($url !== '/') {
            $urlParts = explode('/', trim($url, '/'));
            $lang = array_shift($urlParts);
            if (in_array($lang, $this->config['multilingual']['valid-languages'])) {
                $selectedLanguage = $lang;
                $request = $request->withUri($uri->withPath('/'. implode('/', $urlParts)));
            }
        }
        $this->setLocale($selectedLanguage, $request->getAttribute('moduleName'));

        return $handler->handle($request->withAttribute('selectedLanguage', $selectedLanguage));
    }

    private function setLocale(string $locale, string $domain) : void
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
        $domain .='_' . $modifiedTime;
        $lang    = $locale . '.UTF8';
        putenv("LANG={$lang}");
        putenv("LANGUAGE={$lang}");
        setlocale(LC_ALL, $lang);
        Locale::setDefault($locale . '.UTF-8');
        bindtextdomain($domain, 'data/cache/locale');
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
    }
}
