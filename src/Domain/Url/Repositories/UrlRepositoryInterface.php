<?php

namespace Domain\Url\Repositories;

use Domain\Url\Entities\Url;
use Symfony\Component\Uid\Uuid;

interface UrlRepositoryInterface
{
    public function save(Url $url): void;

    public function findByCode(string $code): ?Url;

    public function findById(Uuid $id): ?Url;
}
