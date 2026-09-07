<?php

namespace Domain\Click\Repositories;

use Domain\Click\Entities\Click;
use Domain\Url\Entities\Url;

interface ClickRepositoryInterface
{
    public function save(Click $click): void;

    public function countByUrl(Url $url): int;
}
