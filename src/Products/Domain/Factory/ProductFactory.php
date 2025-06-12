<?php

declare(strict_types=1);

namespace App\Products\Domain\Factory;

use App\Products\Domain\Entity\Product;

class ProductFactory
{
    /**
     * @param non-empty-string $title
     * @param float $price
     * @param non-empty-string|null $imageUrl
     * @param non-empty-string $sourceUrl
     * @return Product
     */
    public function create(
        string $title,
        float $price,
        string $sourceUrl,
        ?string $imageUrl = null,
    ): Product {
        return new Product($title, $price, $sourceUrl, $imageUrl);
    }
}
