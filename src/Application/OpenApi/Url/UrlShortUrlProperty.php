<?php

namespace Application\OpenApi\Url;

use Attribute;
use OpenApi\Attributes\Property;

#[Attribute(
    Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS_CONSTANT | Attribute::IS_REPEATABLE,
)]
final class UrlShortUrlProperty extends Property
{
    public function __construct(bool $readOnly = false)
    {
        parent::__construct(
            description: 'The full short URL to share.',
            type: 'string',
            readOnly: $readOnly,
            example: 'http://localhost/aB3xY9kLmZ',
        );
    }
}
