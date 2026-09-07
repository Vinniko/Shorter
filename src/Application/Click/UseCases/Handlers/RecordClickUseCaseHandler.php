<?php

namespace Application\Click\UseCases\Handlers;

use Application\Click\UseCases\RecordClickUseCase;
use Domain\Click\Commands\RecordClickCommand;
use Domain\Click\TransferObjects\NewClickTransferObject;
use Domain\MessageBus\CommandBusInterface;
use Domain\MessageBus\QueryBusInterface;
use Domain\Url\Entities\Url;
use Domain\Url\Queries\GetUrlByCodeQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final readonly class RecordClickUseCaseHandler
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
    ) {}

    public function __invoke(RecordClickUseCase $useCase): void
    {
        /** @var Url $url */
        $url = $this->queryBus->dispatch(new GetUrlByCodeQuery($useCase->code));

        $transferObject = new NewClickTransferObject(Uuid::v7(), $url);

        $this->commandBus->dispatch(new RecordClickCommand($transferObject));
    }
}
