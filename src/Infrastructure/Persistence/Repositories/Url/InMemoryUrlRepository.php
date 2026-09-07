<?php

namespace Infrastructure\Persistence\Repositories\Url;

use Domain\Url\Entities\Url;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class InMemoryUrlRepository implements UrlRepositoryInterface
{
    /** @var array<string, Url>  */
    private array $urls = [];

    public function save(Url $url): void
    {
        $this->urls[$url->getId()->toString()] = $url;
    }

    public function findByCode(string $code): ?Url
    {
        foreach ($this->urls as $url) {
            if ($url->getCode() !== $code) {
                continue;
            }

            return $url;
        }

        return null;
    }

    public function findById(Uuid $id): ?Url
    {
        return $this->urls[$id->toString()] ?? null;
    }
}
