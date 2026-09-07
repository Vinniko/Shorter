<?php

namespace Domain\Url\Repositories;

use Domain\Url\Entities\Url;

interface UrlRepositoryInterface
{
    public function save(Url $url): void;

    public function findByCode(string $code): ?Url;
}
