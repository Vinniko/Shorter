<?php

namespace Infrastructure\Persistence\Repositories\Click;

use Domain\Click\Entities\Click;
use Domain\Click\Repositories\ClickRepositoryInterface;
use Domain\Url\Entities\Url;

final class InMemoryClickRepository implements ClickRepositoryInterface
{
    /** @var array<string, Click> */
    private array $clicks = [];

    public function save(Click $click): void
    {
        $this->clicks[$click->getId()->toString()] = $click;
    }

    public function countByUrl(Url $url): int
    {
        $count = 0;

        foreach ($this->clicks as $click) {
            if ($click->getUrl()->getId()->toString() !== $url->getId()->toString()) {
                continue;
            }

            $count++;
        }

        return $count;
    }
}
