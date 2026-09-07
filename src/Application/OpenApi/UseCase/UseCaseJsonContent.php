<?php

namespace Application\OpenApi\UseCase;

use Attribute;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes\JsonContent;

#[Attribute(Attribute::TARGET_CLASS)]
final class UseCaseJsonContent extends JsonContent
{
    /** @param class-string $useCaseClass */
    public function __construct(string $useCaseClass)
    {
        parent::__construct(
            ref: new Model(type: $useCaseClass),
        );
    }
}
