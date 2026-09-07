<?php

namespace UserInterface\Http\Api\Url;

use Application\OpenApi\UseCase\UseCaseJsonContent;
use Application\OpenApi\Validation\ValidationFailedResponse;
use Application\OpenApi\View\ViewObjectContent;
use Application\ParameterBag\ParameterBag;
use Application\Url\UseCases\CreateUrlUseCase;
use Application\Url\UseCases\GetUrlByIdUseCase;
use Application\View\Url\UrlView;
use Application\View\Url\UrlViewFactory;
use Domain\MessageBus\CommandBusInterface;
use Domain\MessageBus\QueryBusInterface;
use Domain\Url\Entities\Url;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use UserInterface\Http\Api\AbstractApiController;

#[Route(path: '/urls', name: 'api_create_url', methods: ['POST'])]
#[OA\Tag(name: 'Urls')]
#[OA\RequestBody(content: new UseCaseJsonContent(CreateUrlUseCase::class))]
#[OA\Response(
    response: Response::HTTP_CREATED,
    description: 'Short link created.',
    content: new ViewObjectContent(UrlView::class),
)]
#[ValidationFailedResponse]
final class CreateUrlAction extends AbstractApiController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly UrlViewFactory $viewFactory,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $url = $this->createUrl($request);
        } catch (ValidationFailedException $validationFailedException) {
            return $this->createValidationFailedJsonResponse($validationFailedException);
        }

        return new JsonResponse($this->viewFactory->create($url), Response::HTTP_CREATED);
    }

    private function createUrl(Request $request): Url
    {
        $payload = ParameterBag::createFromJson($request->getContent());

        $useCase = new CreateUrlUseCase();
        $useCase->id = Uuid::v7();
        $useCase->targetUrl = $payload->getString('target_url');

        $this->commandBus->dispatch($useCase);

        return $this->getUrlById($useCase->id);
    }

    private function getUrlById(Uuid $id): Url
    {
        $useCase = new GetUrlByIdUseCase();
        $useCase->id = $id->toString();

        return $this->queryBus->dispatch($useCase);
    }
}
