<?php

namespace Domain\Click\Entities;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Domain\Click\TransferObjects\NewClickTransferObject;
use Domain\Url\Entities\Url;
use Symfony\Component\Uid\Uuid as SymfonyUuid;

/** @final */
#[ORM\Table(name: 'click')]
#[ORM\Entity]
class Click
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private SymfonyUuid $id;

    #[ORM\ManyToOne(targetEntity: Url::class)]
    #[ORM\JoinColumn(name: 'url_id', referencedColumnName: 'id', nullable: false)]
    private Url $url;

    #[ORM\Column(name: 'clicked_at', type: 'datetime_immutable')]
    private DateTimeInterface $clickedAt;

    public function getId(): SymfonyUuid
    {
        return $this->id;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function getClickedAt(): DateTimeInterface
    {
        return $this->clickedAt;
    }

    public static function createByTransferObject(NewClickTransferObject $transferObject): self
    {
        $click = new self();
        $click->id = SymfonyUuid::fromString($transferObject->id->toString());
        $click->url = $transferObject->url;
        $click->clickedAt = new DateTimeImmutable();

        return $click;
    }
}
