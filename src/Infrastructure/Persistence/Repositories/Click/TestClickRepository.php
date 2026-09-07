<?php

namespace Infrastructure\Persistence\Repositories\Click;

use Domain\Click\Entities\Click;
use Domain\Click\Repositories\ClickRepositoryInterface;
use Domain\Url\Entities\Url;

final class TestClickRepository implements ClickRepositoryInterface
{
    private bool $useRealRepository = true;

    public function __construct(
        private readonly ClickRepositoryInterface $inMemoryClickRepository,
        private readonly ClickRepositoryInterface $clickRepository,
    ) {}

    public function useReal(): void
    {
        $this->useRealRepository = true;
    }

    public function useInMemory(): void
    {
        $this->useRealRepository = false;
    }

    public function save(Click $click): void
    {
        if ($this->useRealRepository) {
            $this->clickRepository->save($click);

            return;
        }

        $this->inMemoryClickRepository->save($click);
    }

    public function countByUrl(Url $url): int
    {
        if ($this->useRealRepository) {
            return $this->clickRepository->countByUrl($url);
        }

        return $this->inMemoryClickRepository->countByUrl($url);
    }
}
