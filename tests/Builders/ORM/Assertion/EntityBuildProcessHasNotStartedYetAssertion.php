<?php

namespace Tests\Builders\ORM\Assertion;

use Tests\Builders\ORM\Exception\EntityBuildProcessHasAlreadyStartedException;

final class EntityBuildProcessHasNotStartedYetAssertion
{
    /**
     * @throws EntityBuildProcessHasAlreadyStartedException
     */
    public static function assert(mixed $entity): void
    {
        if ($entity) {
            throw new EntityBuildProcessHasAlreadyStartedException($entity::class);
        }
    }
}
