<?php

namespace App\Tests\Helper\Factory;

use App\Products\Domain\Entity\Product;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Product>
 */
final class ProductFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Product::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'title' => self::faker()->url(),
            'price' => self::faker()->randomFloat(),
            'sourceUrl' => self::faker()->url(),
            'imageUrl' => self::faker()->url(),
        ];
    }
}
