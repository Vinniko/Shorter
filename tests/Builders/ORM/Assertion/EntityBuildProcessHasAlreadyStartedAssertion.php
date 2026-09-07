<?php

namespace Tests\Builders\ORM\Assertion;

use Tests\Builders\ORM\Exception\EntityBuildProcessHasNotStartedYetException;

final class EntityBuildProcessHasAlreadyStartedAssertion
{
    /**
     * @throws EntityBuildProcessHasNotStartedYetException
     */
    public static function assert(string $entityClass, mixed $entity): void
    {
        if (!$entity) {
            throw new EntityBuildProcessHasNotStartedYetException($entityClass);
        }
    }
}
