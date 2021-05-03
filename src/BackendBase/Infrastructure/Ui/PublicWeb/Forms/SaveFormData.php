<?php

declare(strict_types=1);

namespace BackendBase\PublicWeb\Forms;

use BackendBase\Infrastructure\Persistence\Doctrine\Entity\FormData;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\Middleware\SessionMiddleware;
use BackendBase\Shared\Services\FlashMessages;
use BackendBase\Shared\Services\PayloadSanitizer;
use DateTimeImmutable;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Helper\ServerUrlHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Session\SessionInterface;
use Ramsey\Uuid\Uuid;

class SaveFormData implements RequestHandlerInterface
{
    private ServerUrlHelper $urlHelper;
    private GenericRepository $genericRepository;

    public function __construct(
        ServerUrlHelper $urlHelper,
        GenericRepository $genericRepository
    ) {
        $this->urlHelper         = $urlHelper;
        $this->genericRepository = $genericRepository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestParameters = PayloadSanitizer::sanitize($request->getParsedBody());
        /**
         * @var $session SessionInterface
         */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $captcha = $session->get('__captcha');
        $session->remove('__captcha');
        if (!empty($captcha) && $captcha !== $requestParameters['__captcha']??null) {
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
