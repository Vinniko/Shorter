<?php

namespace Domain\Url\Exceptions;

use DomainException;
use Symfony\Component\Uid\Uuid;

final class UrlNotFoundException extends DomainException
{
    public static function withId(Uuid $id): self
    {
        return new self(
            sprintf('Url with ID "%s" not found', $id->toString()),
        );
    }

    public static function withCode(string $code): self
    {
        return new self(
            sprintf('Url with code "%s" not found', $code),
        );
    }
}
