<?php

namespace Domain\Url\Commands;

use Domain\Url\TransferObjects\NewUrlTransferObject;

final readonly class CreateUrlCommand
{
    public function __construct(
        public NewUrlTransferObject $transferObject,
    ) {}
}
