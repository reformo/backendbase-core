<?php

declare(strict_types=1);

namespace BackendBase\PublicWeb\Forms;

use BackendBase\Infrastructure\Persistence\Doctrine\Entity\FormData;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\Middleware\SessionMiddleware;
use BackendBase\Shared\Services\FlashMessages;
use DateTimeImmutable;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Helper\ServerUrlHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;
use Selami\Stdlib\Arrays\PayloadSanitizer;

class SaveFormData implements RequestHandlerInterface
{
    public function __construct(private ServerUrlHelper $urlHelper, private GenericRepository $genericRepository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestParameters = PayloadSanitizer::sanitize($request->getParsedBody());
        /**
         * @var $session SessionInterface
         */
        $session        = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $captcha        = $session->get('__captcha');
        $requestCaptcha = $requestParameters['__captcha'] ?? null;
        $session->remove('__captcha');
        if (empty($captcha) || empty($requestCaptcha) || $captcha !== $requestCaptcha) {
            $flash = $request->getAttribute(FlashMessages::FLASH_MESSAGE_ATTRIBUTE);
            $flash->flash('formData', $requestParameters);

            return new RedirectResponse($this->urlHelper->generate() . '?r=error&m=captcha', 302);
        }

        if (empty($requestParameters['message'])) {
            $flash = $request->getAttribute(FlashMessages::FLASH_MESSAGE_ATTRIBUTE);
            $flash->flash('formData', $requestParameters);

            return new RedirectResponse($this->urlHelper->generate() . '?r=error', 302);
        }

        $formData = new FormData();
        $formData->setId(Uuid::uuid4()->toString());
        $formData->setFormId($requestParameters['form_id']);
        $formData->setCreatedAt(new DateTimeImmutable());
        $formData->setIsModerated(0);
        $formData->setPostData($requestParameters);
        $formData->setClientIp($request->getAttribute('Client-Ip'));
        $this->genericRepository->persistGeneric($formData);

        return new RedirectResponse($this->urlHelper->generate() . '?r=success', 302);
    }
}
