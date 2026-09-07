<?php

namespace Domain\Click\Commands\Handlers;

use Domain\Click\Commands\RecordClickCommand;
use Domain\Click\Entities\Click;
use Domain\Click\Repositories\ClickRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RecordClickCommandHandler
{
    public function __construct(
        private ClickRepositoryInterface $clickRepository,
    ) {}

    public function __invoke(RecordClickCommand $command): void
    {
        $click = Click::createByTransferObject($command->transferObject);

        $this->clickRepository->save($click);
    }
}
