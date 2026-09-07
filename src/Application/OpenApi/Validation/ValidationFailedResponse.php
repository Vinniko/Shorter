<?php

namespace Application\OpenApi\Validation;

use Attribute;
use OpenApi\Attributes\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class ValidationFailedResponse extends Response
{
    public function __construct()
    {
        parent::__construct(
            response: JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            description: 'Validation failed.',
            content: new ValidationFailedJsonContent(),
        );
    }
}
