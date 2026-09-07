<?php

namespace Application\Click\UseCases\Handlers;

use Application\Click\UseCases\GetClickCountByUrlUseCase;
use Domain\Click\Queries\GetClickCountByUrlQuery;
use Domain\MessageBus\QueryBusInterface;
use Domain\Url\Entities\Url;
use Domain\Url\Queries\GetUrlByCodeQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetClickCountByUrlUseCaseHandler
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {}

    public function __invoke(GetClickCountByUrlUseCase $useCase): int
    {
        /** @var Url $url */
        $url = $this->queryBus->dispatch(new GetUrlByCodeQuery($useCase->code));

        return $this->queryBus->dispatch(new GetClickCountByUrlQuery($url));
    }
}
