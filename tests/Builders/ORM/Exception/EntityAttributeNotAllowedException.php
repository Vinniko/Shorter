<?php

namespace Tests\Builders\ORM\Exception;

use Exception;

final class EntityAttributeNotAllowedException extends Exception
{
    /**
     * @param array<int, string> $notAllowedAttributes
     */
    public function __construct(array $notAllowedAttributes)
    {
        $message = sprintf('Attributes %s not allowed.', implode(', ', $notAllowedAttributes));

        parent::__construct($message, 500);
    }
}
