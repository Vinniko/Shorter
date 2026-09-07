<?php

namespace Application\Url\UseCases\Handlers;

use Application\Url\UseCases\GetUrlByCodeUseCase;
use Domain\MessageBus\QueryBusInterface;
use Domain\Url\Entities\Url;
use Domain\Url\Queries\GetUrlByCodeQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetUrlByCodeUseCaseHandler
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {}

    public function __invoke(GetUrlByCodeUseCase $useCase): Url
    {
        return $this->queryBus->dispatch(new GetUrlByCodeQuery($useCase->code));
    }
}
