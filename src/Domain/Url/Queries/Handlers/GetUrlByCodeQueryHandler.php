<?php

namespace Domain\Url\Queries\Handlers;

use Domain\Url\Entities\Url;
use Domain\Url\Queries\GetUrlByCodeQuery;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetUrlByCodeQueryHandler
{
    public function __construct(
        private readonly UrlRepositoryInterface $urlRepository,
    ) {}

    public function __invoke(GetUrlByCodeQuery $query): ?Url
    {
        return $this->urlRepository->findByCode($query->code);
    }
}
