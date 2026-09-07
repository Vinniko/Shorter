<?php

namespace Application\Url\UseCases\Handlers;

use Application\Url\UseCases\GetUrlByIdUseCase;
use Domain\MessageBus\QueryBusInterface;
use Domain\Url\Entities\Url;
use Domain\Url\Queries\GetUrlByIdQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final readonly class GetUrlByIdUseCaseHandler
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {}

    public function __invoke(GetUrlByIdUseCase $useCase): Url
    {
        return $this->queryBus->dispatch(new GetUrlByIdQuery(Uuid::fromString($useCase->id)));
    }
}
