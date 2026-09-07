<?php

namespace Infrastructure\Persistence\Repositories\Click;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Click\Entities\Click;
use Domain\Click\Repositories\ClickRepositoryInterface;
use Domain\Url\Entities\Url;

final class ClickRepository implements ClickRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function save(Click $click): void
    {
        $this->entityManager->persist($click);
        $this->entityManager->flush();
    }

    public function countByUrl(Url $url): int
    {
        return (int) $this->entityManager
            ->getRepository(Click::class)
            ->count(['url' => $url]);
    }
}
