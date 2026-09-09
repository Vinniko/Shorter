<?php

namespace Application\OpenApi\LinkStats;

use Attribute;
use OpenApi\Attributes\Property;

#[Attribute(
    Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS_CONSTANT | Attribute::IS_REPEATABLE,
)]
final class UrlClickQtyProperty extends Property
{
    public function __construct(bool $readOnly = false)
    {
        parent::__construct(
            description: 'Total number of times the short link was visited.',
            type: 'integer',
            readOnly: $readOnly,
            example: 43,
        );
    }
}
