<?php

namespace Tests\Builders\ORM\Assertion;

use Tests\Builders\ORM\Exception\EntityAttributeNotAllowedException;

final class EntityAttributesAssertion
{
    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $allowedAttributes
     *
     * @throws EntityAttributeNotAllowedException
     */
    public static function assert(array $attributes, array $allowedAttributes): void
    {
        $notAllowedAttributes = array_diff_key($attributes, $allowedAttributes);

        if ($notAllowedAttributes) {
            throw new EntityAttributeNotAllowedException(array_keys($notAllowedAttributes));
        }
    }
}
