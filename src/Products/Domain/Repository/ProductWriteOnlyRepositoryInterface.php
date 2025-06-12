<?php

declare(strict_types=1);

namespace App\Products\Domain\Repository;

use App\Products\Domain\Entity\Product;
use App\Products\Domain\ValueObject\ProductId;
use App\Shared\Domain\Exception\Repository\RepositoryException;

interface ProductWriteOnlyRepositoryInterface
{
    /**
     * @throws RepositoryException
     */
    public function add(Product $product): ProductId;
}
