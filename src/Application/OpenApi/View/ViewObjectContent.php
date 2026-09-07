<?php

namespace Application\OpenApi\View;

use Attribute;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes\JsonContent;

#[Attribute(Attribute::TARGET_CLASS)]
final class ViewObjectContent extends JsonContent
{
    /** @param class-string $viewObjectClass */
    public function __construct(string $viewObjectClass)
    {
        parent::__construct(
            ref: new Model(type: $viewObjectClass),
        );
    }
}
