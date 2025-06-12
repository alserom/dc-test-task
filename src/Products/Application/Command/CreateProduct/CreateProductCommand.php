<?php

declare(strict_types=1);

namespace App\Products\Application\Command\CreateProduct;

use App\Shared\Application\Command\CommandInterface;

readonly class CreateProductCommand implements CommandInterface
{
    /**
     * @param non-empty-string $title
     * @param float $price
     * @param non-empty-string $sourceUrl
     * @param non-empty-string|null $imageUrl
     */
    public function __construct(
        public string $title,
        public float $price,
        public string $sourceUrl,
        public ?string $imageUrl = null,
    ) {
    }
}
