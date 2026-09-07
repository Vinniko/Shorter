<?php

namespace Application\Click\UseCases;

use Symfony\Component\Validator\Constraints as Assert;

final class GetClickCountByUrlUseCase
{
    #[Assert\NotBlank(message: 'The code is required.')]
    public string $code;
}
