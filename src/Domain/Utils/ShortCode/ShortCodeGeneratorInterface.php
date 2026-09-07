<?php

namespace Domain\Utils\ShortCode;

interface ShortCodeGeneratorInterface
{
    public function generate(): string;
}
