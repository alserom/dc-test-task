<?php

declare(strict_types=1);

namespace App\Products\Application\Query\GetProducts;

use App\Products\Application\DTO\ProductDTO;
use App\Products\Domain\Entity\Product;
use App\Products\Domain\Repository\ProductReadOnlyRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Domain\Exception\Repository\RepositoryException;
use Psr\Log\LoggerInterface;

final readonly class GetProductsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private ProductReadOnlyRepositoryInterface $productRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @return ProductDTO[]
     */
    public function __invoke(GetProductsQuery $query): array
    {
        try {
            $products = $this->productRepository->get($query->limit);
        } catch (RepositoryException $e) {
            $this->logger->error(
                'Error in getting the product list',
                [
                    'repository' => $e->getRepositoryClassName(),
                    'exception' => $e
                ]
            );

            return [];
        }

        return array_map(
            static function (Product $product) {
                return new ProductDTO(
                    $product->getTitle(),
                    $product->getPrice(),
                    $product->getSourceUrl(),
                    $product->getImageUrl(),
                );
            },
            $products,
        );
    }
}
