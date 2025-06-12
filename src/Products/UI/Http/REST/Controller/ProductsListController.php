<?php

declare(strict_types=1);

namespace App\Products\UI\Http\REST\Controller;

use App\Products\Application\Query\GetProducts\GetProductsQuery;
use App\Products\UI\Http\REST\DTO\ProductsListQueryParameters;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class ProductsListController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function __invoke(
        QueryBusInterface $queryBus,
        #[MapQueryString(
            validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY
        )] ProductsListQueryParameters $queryParameters = new ProductsListQueryParameters()
    ): JsonResponse {
        $products = $queryBus->handle(new GetProductsQuery($queryParameters->limit));

        return $this->json($products);
    }
}
