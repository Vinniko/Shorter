<?php

namespace Application\Url\UseCases\Handlers;

use Application\Url\UseCases\CreateUrlUseCase;
use Domain\MessageBus\CommandBusInterface;
use Domain\Url\Commands\CreateUrlCommand;
use Domain\Url\TransferObjects\Factories\NewUrlTransferObjectFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateUrlUseCaseHandler
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private NewUrlTransferObjectFactory $transferObjectFactory,
    ) {}

    public function __invoke(CreateUrlUseCase $useCase): void
    {
        $transferObject = $this->transferObjectFactory->createForIdAndTargetUrl($useCase->id, $useCase->targetUrl);

        $command = new CreateUrlCommand($transferObject);

        $this->commandBus->dispatch($command);
    }
}
