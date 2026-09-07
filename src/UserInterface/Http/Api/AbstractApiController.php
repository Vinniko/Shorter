<?php

namespace UserInterface\Http\Api;

use Application\View\Validator\ValidationFailedExceptionViewFactory;
use Application\View\Validator\ViolationViewFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ValidationFailedException;

abstract class AbstractApiController extends AbstractController
{
    final protected function createValidationFailedJsonResponse(ValidationFailedException $validationFailedException): JsonResponse
    {
        $validationFailedExceptionViewFactory = new ValidationFailedExceptionViewFactory(new ViolationViewFactory());

        return new JsonResponse(
            $validationFailedExceptionViewFactory->create($validationFailedException),
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
