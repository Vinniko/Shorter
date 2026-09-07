<?php

namespace Infrastructure\MessageBus;

use Domain\MessageBus\QueryBusInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final class QueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(
        readonly MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    /** {@inheritDoc} */
    public function dispatch(object $query): mixed
    {
        try {
            return $this->handle($query);
        } catch (HandlerFailedException $handlerFailedException) {
            if ($handlerFailedException->getPrevious() instanceof Throwable) {
                throw $handlerFailedException->getPrevious();
            }

            throw $handlerFailedException;
        }
    }
}
