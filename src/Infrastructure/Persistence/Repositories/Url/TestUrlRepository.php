<?php

namespace Infrastructure\Persistence\Repositories\Url;

use Domain\Url\Entities\Url;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class TestUrlRepository implements UrlRepositoryInterface
{
    private bool $useRealRepository = true;

    public function __construct(
        private readonly UrlRepositoryInterface $inMemoryUrlRepository,
        private readonly UrlRepositoryInterface $urlRepository,
    ) {}

    public function useReal(): void
    {
        $this->useRealRepository = true;
    }

    public function useInMemory(): void
    {
        $this->useRealRepository = false;
    }

    public function save(Url $url): void
    {
        if ($this->useRealRepository) {
            $this->urlRepository->save($url);

            return;
        }

        $this->inMemoryUrlRepository->save($url);
    }

    public function findByCode(string $code): ?Url
    {
        if ($this->useRealRepository) {
            return $this->urlRepository->findByCode($code);
        }

        return $this->inMemoryUrlRepository->findByCode($code);
    }

    public function findById(Uuid $id): ?Url
    {
        if ($this->useRealRepository) {
            return $this->urlRepository->findById($id);
        }

        return $this->inMemoryUrlRepository->findById($id);
    }
}
