<?php

namespace Tests\Builders\ORM\Exception;

use Exception;

final class EntityBuildProcessHasAlreadyStartedException extends Exception
{
    public function __construct(string $entityClass)
    {
        $message = sprintf('%s Build process has already started yet.', $entityClass);

        parent::__construct($message, 500);
    }
}
