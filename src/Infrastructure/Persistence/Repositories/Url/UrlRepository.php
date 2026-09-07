<?php

namespace Infrastructure\Persistence\Repositories\Url;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Url\Entities\Url;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class UrlRepository implements UrlRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function save(Url $url): void
    {
        $this->entityManager->persist($url);
        $this->entityManager->flush();
    }

    public function findByCode(string $code): ?Url
    {
        return $this->entityManager
            ->getRepository(Url::class)
            ->findOneBy(['code' => $code]);
    }

    public function findById(Uuid $id): ?Url
    {
        return $this->entityManager->find(Url::class, $id);
    }
}
