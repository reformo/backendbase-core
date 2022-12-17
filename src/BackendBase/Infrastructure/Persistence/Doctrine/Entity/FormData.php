<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'form_data', schema: 'public')]
class FormData
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected string $id;

    #[ORM\Column(name: 'form_id', type: 'uuid')]
    protected string $formId;

    #[ORM\Column(name:'post_data', type: 'json', nullable: true, options: ['jsonb' => true])]
    protected array $postData;

    #[ORM\Column(name: 'client_ip', type: 'string')]
    protected string $clientIp;

    #[ORM\Column(name: 'is_moderated', type: 'integer')]
    protected int $isModerated = 1;

    #[ORM\Column(name: 'created_at', type: 'datetimetz_immutable')]
    protected DateTimeImmutable $createdAt;

    public function setCreatedAt(DateTimeImmutable $datetime): void
    {
        $this->createdAt = $datetime;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setClientIp(string $clientIp): void
    {
        $this->clientIp = $clientIp;
    }

    public function setFormId(string $formId): void
    {
        $this->formId = $formId;
    }

    public function setPostData(array $postData): void
    {
        $this->postData = $postData;
    }

    public function setIsModerated(int $isModerated): void
    {
        $this->isModerated = $isModerated;
    }
}
