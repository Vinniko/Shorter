<?php

namespace Domain\MessageBus;

use Throwable;

interface QueryBusInterface
{
    /** @throws Throwable */
    public function dispatch(object $query): mixed;
}
