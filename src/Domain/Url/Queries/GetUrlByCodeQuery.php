<?php

namespace Domain\Url\Queries;

final readonly class GetUrlByCodeQuery
{
    public function __construct(
        public readonly string $code,
    ) {}
}
