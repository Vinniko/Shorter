<?php

namespace Domain\EntityDetails;

use DateTimeInterface;

interface HasTimestampsDetailsInterface
{
    public function getCreatedAt(): DateTimeInterface;

    public function getUpdatedAt(): DateTimeInterface;
}
