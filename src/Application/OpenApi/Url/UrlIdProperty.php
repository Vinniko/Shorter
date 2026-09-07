<?php

namespace Application\OpenApi\Url;

use Attribute;
use OpenApi\Attributes\Property;

#[Attribute(
    Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS_CONSTANT | Attribute::IS_REPEATABLE,
)]
final class UrlIdProperty extends Property
{
    public function __construct(bool $readOnly = false)
    {
        parent::__construct(
            description: 'The unique identifier of the short link.',
            type: 'string',
            readOnly: $readOnly,
            example: '0192f6b2-2b6b-7c3e-8a1a-2f6b6b6b6b6b',
        );
    }
}
