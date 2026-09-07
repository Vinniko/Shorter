<?php

namespace Domain\Url\Queries;

use Symfony\Component\Uid\Uuid;

final readonly class GetUrlByIdQuery
{
    public function __construct(
        public Uuid $id,
    ) {}
}
