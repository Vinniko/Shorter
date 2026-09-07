<?php

namespace Domain\Click\Queries;

use Domain\Url\Entities\Url;

final readonly class GetClickCountByUrlQuery
{
    public function __construct(
        public Url $url,
    ) {}
}
