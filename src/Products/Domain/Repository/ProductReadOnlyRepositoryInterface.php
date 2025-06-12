<?php

declare(strict_types=1);

namespace App\Products\Domain\Repository;

use App\Products\Domain\Entity\Product;
use App\Shared\Domain\Exception\Repository\RepositoryException;

interface ProductReadOnlyRepositoryInterface
{
    /**
     * @param int<1, max>|null $limit
     * @return Product[]
     *
     * @throws RepositoryException
     */
    public function get(?int $limit = null): array;
}
