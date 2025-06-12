<?php

declare(strict_types=1);

namespace App\Products\Domain\Entity;

use App\Products\Domain\ValueObject\ProductId;

/**
 * TODO@technical-debt: Replace scalars with validatable ValueObjects
 */
class Product
{
    private ProductId $id;

    /**
     * @param non-empty-string $title
     * @param float $price
     * @param non-empty-string $sourceUrl
     * @param non-empty-string|null $imageUrl
     */
    public function __construct(
        private string $title,
        private float $price,
        private string $sourceUrl,
        private ?string $imageUrl = null,
    ) {
        $this->id = ProductId::create();
    }

    public function getId(): ProductId
    {
        return $this->id;
    }

    /**
     * @return non-empty-string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return non-empty-string
     */
    public function getSourceUrl(): string
    {
        return $this->sourceUrl;
    }

    /**
     * @return non-empty-string|null
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }
}
