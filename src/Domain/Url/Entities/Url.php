<?php

namespace Domain\Url\Entities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Domain\EntityDetails\HasTimestampsDetailsInterface;
use Domain\EntityDetails\TimestampsDetailsTrait;
use Domain\Url\TransferObjects\UrlTransferObject;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\Uuid as SymfonyUuid;

/** @final */
#[ORM\Table(name: 'url')]
#[ORM\Entity]
class Url implements HasTimestampsDetailsInterface
{
    use TimestampsDetailsTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private SymfonyUuid $id;

    #[ORM\Column(type: 'string', length: 10, nullable: false, unique: true)]
    private string $code;

    #[ORM\Column(type: 'string', length: 254, nullable: false, unique: false)]
    private string $target_url;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getTargetUrl(): string
    {
        return $this->target_url;
    }

    public static function createByTransferObject(UrlTransferObject $transferObject): self
    {
        $createdAt = new DateTimeImmutable();

        $url = new self();
        $url->id = SymfonyUuid::fromString($transferObject->id->toString());
        $url->code = $transferObject->code;
        $url->target_url = $transferObject->targetUrl;
        $url->createdAt = $createdAt;
        $url->updatedAt = $createdAt;

        return $url;
    }
}
