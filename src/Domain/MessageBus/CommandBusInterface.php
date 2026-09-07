<?php

namespace Domain\MessageBus;

use Throwable;

interface CommandBusInterface
{
    /** @throws Throwable */
    public function dispatch(object $command): void;
}
