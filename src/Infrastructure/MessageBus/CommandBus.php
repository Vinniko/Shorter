<?php

namespace Infrastructure\MessageBus;

use Domain\MessageBus\CommandBusInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final class CommandBus implements CommandBusInterface
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    /** {@inheritDoc} */
    public function dispatch(object $command): void
    {
        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $handlerFailedException) {
            if ($handlerFailedException->getPrevious() instanceof Throwable) {
                throw $handlerFailedException->getPrevious();
            }

            throw $handlerFailedException;
        }
    }
}
