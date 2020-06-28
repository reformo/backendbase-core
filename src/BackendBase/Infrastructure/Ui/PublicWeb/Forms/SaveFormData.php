<?php

declare(strict_types=1);

namespace BackendBase\PublicWeb\Forms;

use BackendBase\Infrastructure\Persistence\Doctrine\Entity\FormData;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
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

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $guard             = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        $requestParameters = $request->getParsedBody();

        $token = $requestParameters['__csrf'] ?? '';
        if (! $guard->validateToken($token)) {
            $uri = $this->urlHelper->generate() . '?r=invalid-csrf-token' .
                '&m=' . urlencode('CSRF Failed: CSRF token missing or incorrect') . '&' . http_build_query($requestParameters);

            return new RedirectResponse($uri, 302);
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
