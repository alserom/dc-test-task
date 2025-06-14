<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\CSV\Repository;

use App\Products\Domain\Entity\Product;
use App\Products\Domain\Repository\ProductWriteOnlyRepositoryInterface;
use App\Products\Domain\ValueObject\ProductId;
use App\Shared\Domain\Exception\Repository\RepositoryException;
use App\Shared\Infrastructure\Persistence\CSV\WriterInterface;

final readonly class ProductRepository implements ProductWriteOnlyRepositoryInterface
{
    public function __construct(private WriterInterface $writer)
    {
    }

    public function add(Product $product): ProductId
    {
        try {
            $this->writer->insertOne(
                [
                    $product->getId()->value,
                    $product->getTitle(),
                    $product->getPrice(),
                    $product->getSourceUrl(),
                    $product->getImageUrl(),
                ]
            );
        } catch (\Throwable $e) {
            throw new RepositoryException(repositoryClassName: static::class, previous: $e);
        }

        return $product->getId();
    }
}
