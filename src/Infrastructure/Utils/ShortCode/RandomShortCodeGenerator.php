<?php

namespace Infrastructure\Utils\ShortCode;

use Domain\Utils\ShortCode\ShortCodeGeneratorInterface;

final class RandomShortCodeGenerator implements ShortCodeGeneratorInterface
{
    private const LENGTH = 10;

    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    public function generate(): string
    {
        $lastIndex = strlen(self::ALPHABET) - 1;

        $code = '';

        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::ALPHABET[random_int(0, $lastIndex)];
        }

        return $code;
    }
}
