<?php

namespace Domain\EntityDetails;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait TimestampsDetailsTrait
{
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private DateTimeInterface $updatedAt;

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }
}
