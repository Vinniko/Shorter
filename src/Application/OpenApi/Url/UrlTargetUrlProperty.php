<?php

namespace Application\OpenApi\Url;

use Attribute;
use OpenApi\Attributes\Property;

#[Attribute(
    Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS_CONSTANT | Attribute::IS_REPEATABLE,
)]
final class UrlTargetUrlProperty extends Property
{
    public function __construct(bool $readOnly = false)
    {
        parent::__construct(
            description: 'The original, long URL the short link points to.',
            type: 'string',
            readOnly: $readOnly,
            example: 'https://example.com/some/very/long/path?with=query&params=here',
        );
    }
}
