<?php

namespace Domain\Click\TransferObjects;

use Domain\Url\Entities\Url;
use Symfony\Component\Uid\Uuid;

final class NewClickTransferObject
{
    public function __construct(
        public Uuid $id,
        public Url $url,
    ) {}
}
