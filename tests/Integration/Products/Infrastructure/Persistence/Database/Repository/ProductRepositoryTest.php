<?php

namespace App\Tests\Integration\Products\Infrastructure\Persistence\Database\Repository;

use App\Products\Domain\Factory\ProductFactory as DomainProductFactory;
use App\Products\Infrastructure\Persistence\Database\Repository\ProductRepository;
use App\Tests\Helper\Factory\ProductFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductRepositoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    public function testList(): void
    {
        static::bootKernel();
        ProductFactory::createMany(10);

        $repository = static::getContainer()->get(ProductRepository::class);
        $products = $repository->get();

        static::assertCount(10, $products);
    }

    public function testLimitList(): void
    {
        static::bootKernel();
        ProductFactory::createMany(10);

        $repository = static::getContainer()->get(ProductRepository::class);
        $products = $repository->get(3);

        static::assertCount(3, $products);
    }

    public function testAdd(): void
    {
        static::bootKernel();
        $factory = static::getContainer()->get(DomainProductFactory::class);
        $repository = static::getContainer()->get(ProductRepository::class);

        $product = $factory->create(
            'Test product',
            9.99,
            'https://example.com',
            'https://example.com/img.png',
        );

        $repository->add($product);
        $products = $repository->get(1);
        static::assertEquals($product->getId()->value, $products[0]->getId()->value);
        static::assertEquals($product->getTitle(), $products[0]->getTitle());
        static::assertEquals($product->getPrice(), $products[0]->getPrice());
        static::assertEquals($product->getSourceUrl(), $products[0]->getSourceUrl());
        static::assertEquals($product->getImageUrl(), $products[0]->getImageUrl());
    }
}
