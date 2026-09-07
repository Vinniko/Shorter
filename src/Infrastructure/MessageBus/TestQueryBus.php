<?php

namespace Infrastructure\MessageBus;

use Domain\MessageBus\QueryBusInterface;
use Throwable;

final class TestQueryBus implements QueryBusInterface
{
    private bool $enableStub = false;

    /** @var array<int, mixed> */
    private array $queryResults = [];

    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    /** {@inheritDoc} */
    public function dispatch(object $query): mixed
    {
        if (!$this->enableStub) {
            return $this->queryBus->dispatch($query);
        }

        $result = array_shift($this->queryResults);

        if ($result instanceof Throwable) {
            throw $result;
        }

        return $result;
    }

    public function enableStub(): void
    {
        $this->enableStub = true;
    }

    public function disableStub(): void
    {
        $this->enableStub = false;
    }

    public function addQueryResult(mixed $result): void
    {
        $this->queryResults[] = $result;
    }
}
