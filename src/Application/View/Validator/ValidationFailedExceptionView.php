<?php

namespace Application\View\Validator;

use OpenApi\Attributes as OA;

final class ValidationFailedExceptionView
{
    #[OA\Property(description: 'Exception message.', example: 'Validation Failed')]
    public string $message;

    /** @var array<string, array<string>> */
    #[OA\Property(
        description: 'Validation errors grouped by field.',
        type: 'object',
        example: ['target_url' => ['The target URL is not a valid URL.']],
        additionalProperties: new OA\AdditionalProperties(
            type: 'array',
            items: new OA\Items(type: 'string'),
        ),
    )]
    public array $violations;
}
