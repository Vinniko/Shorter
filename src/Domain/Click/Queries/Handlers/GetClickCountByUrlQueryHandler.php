<?php

namespace Domain\Click\Queries\Handlers;

use Domain\Click\Queries\GetClickCountByUrlQuery;
use Domain\Click\Repositories\ClickRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetClickCountByUrlQueryHandler
{
    public function __construct(
        private ClickRepositoryInterface $clickRepository,
    ) {}

    public function __invoke(GetClickCountByUrlQuery $query): int
    {
        return $this->clickRepository->countByUrl($query->url);
    }
}
