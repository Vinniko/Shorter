<?php

namespace Infrastructure\MessageBus;

use Domain\MessageBus\CommandBusInterface;
use PHPUnit\Framework\Assert;

final class TestCommandBus implements CommandBusInterface
{
    private bool $enableStub = false;

    /** @var array<int, object> */
    private array $dispatchedCommands = [];

    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    /** {@inheritDoc} */
    public function dispatch(object $command): void
    {
        if (!$this->enableStub) {
            $this->commandBus->dispatch($command);
        }

        $this->dispatchedCommands[] = $command;
    }

    public function enableStub(): void
    {
        $this->enableStub = true;
    }

    public function disableStub(): void
    {
        $this->enableStub = false;
    }

    /**
     * @return array<int, object>
     */
    public function getDispatchedCommands(): array
    {
        return $this->dispatchedCommands;
    }

    public function getLastDispatchedCommand(): ?object
    {
        if ($this->dispatchedCommands === []) {
            return null;
        }

        return $this->dispatchedCommands[array_key_last($this->dispatchedCommands)];
    }

    /**
     * @param class-string $commandClassName
     */
    public function assertIsDispatched(string $commandClassName): void
    {
        Assert::assertTrue($this->isDispatched($commandClassName), sprintf('Command %s was not dispatched', $commandClassName));
    }

    /**
     * @param class-string $commandClassName
     */
    public function assertIsNotDispatched(string $commandClassName): void
    {
        Assert::assertFalse($this->isDispatched($commandClassName), sprintf('Command %s was dispatched', $commandClassName));
    }

    /**
     * @param class-string $commandClassName
     */
    private function isDispatched(string $commandClassName): bool
    {
        foreach ($this->dispatchedCommands as $dispatchedCommand) {
            if ($dispatchedCommand instanceof $commandClassName) {
                return true;
            }
        }

        return false;
    }
}
