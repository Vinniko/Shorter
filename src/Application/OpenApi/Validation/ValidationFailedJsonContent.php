<?php

namespace Application\OpenApi\Validation;

use Application\View\Validator\ValidationFailedExceptionView;
use Attribute;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes\JsonContent;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class ValidationFailedJsonContent extends JsonContent
{
    public function __construct()
    {
        parent::__construct(
            ref: new Model(type: ValidationFailedExceptionView::class),
        );
    }
}
