<?php

namespace Tests\Builders\ORM\Exception;

use Exception;

final class EntityBuildProcessHasNotStartedYetException extends Exception
{
    public function __construct(string $entityClass)
    {
        $message = sprintf('%s Build process has not started yet.', $entityClass);

        parent::__construct($message, 500);
    }
}
