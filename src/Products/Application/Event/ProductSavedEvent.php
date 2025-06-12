<?php

declare(strict_types=1);

namespace App\Products\Application\Event;

use App\Products\Domain\Entity\Product;
use App\Shared\Application\Event\EventInterface;

final readonly class ProductSavedEvent implements EventInterface
{
    public function __construct(private Product $product)
    {
    }

    public function getEntity(): Product
    {
        return $this->product;
    }
}
