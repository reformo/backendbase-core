<?php

declare(strict_types=1);

namespace BackendBase\PublicWeb\Forms;

use BackendBase\Infrastructure\Persistence\Doctrine\Entity\FormData;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\Services\FlashMessages;
use BackendBase\Shared\Services\PayloadSanitizer;
use DateTimeImmutable;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Helper\ServerUrlHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

use function http_build_query;
use function urlencode;

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
        if (empty($requestParameters['message'])) {
            $flash = $request->getAttribute(FlashMessages::FLASH_MESSAGE_ATTRIBUTE);
            $flash->flash('contactFormData', $requestParameters);
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
