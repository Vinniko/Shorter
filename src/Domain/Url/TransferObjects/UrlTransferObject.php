<?php

namespace Domain\Url\TransferObjects;

use Symfony\Component\Uid\Uuid;

final class UrlTransferObject
{
    public function __construct(
        public Uuid $id,
        public string $code,
        public string $targetUrl,
    ) {}
}
