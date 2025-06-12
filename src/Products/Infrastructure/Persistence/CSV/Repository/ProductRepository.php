<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\CSV\Repository;

use App\Products\Domain\Entity\Product;
use App\Products\Domain\Repository\ProductWriteOnlyRepositoryInterface;
use App\Products\Domain\ValueObject\ProductId;
use App\Shared\Domain\Exception\Repository\RepositoryException;
use League\Csv\Writer;

/**
 * TODO@technical-debt: Make writer configurable
 */
class ProductRepository implements ProductWriteOnlyRepositoryInterface
{
    public const string DEFAULT_FILE_PATH = '/app/products.csv';

    protected const string OPEN_MODE = 'a+';

    protected string $filePath = self::DEFAULT_FILE_PATH;

    public function add(Product $product): ProductId
    {
        try {
            $this->getWriter()->insertOne(
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

    private function getWriter(): Writer
    {
        return Writer::createFromPath($this->getFilePath(), static::OPEN_MODE);
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function withFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }
}
