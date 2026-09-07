<?php

namespace Domain\Click\Commands;

use Domain\Click\TransferObjects\NewClickTransferObject;

final readonly class RecordClickCommand
{
    public function __construct(
        public NewClickTransferObject $transferObject,
    ) {}
}
