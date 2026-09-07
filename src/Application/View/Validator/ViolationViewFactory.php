<?php

namespace Application\View\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ViolationViewFactory
{
    /**
     * @return array<string, array<string>>
     */
    public function createCollection(ConstraintViolationListInterface $violations): array
    {
        $normalizedViolations = [];

        foreach ($violations as $violation) {
            $property = self::toSnakeCase($violation->getPropertyPath());

            $normalizedViolations[$property][] = $violation->getMessage();
        }

        return $normalizedViolations;
    }

    private static function toSnakeCase(string $propertyPath): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $propertyPath));
    }
}
