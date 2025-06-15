<?php

namespace App\Tests\Integration\Products\Infrastructure\Persistence\CSV\Repository;

use App\Products\Domain\Factory\ProductFactory as DomainProductFactory;
use App\Products\Infrastructure\Persistence\CSV\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * TODO@technical-debt: Tests can be improved
 * Check file content, compare with entity, and so on...
 */
class ProductRepositoryTest extends KernelTestCase
{
    public function tearDown(): void
    {
        $filePath = static::getContainer()->getParameter('app.products.csv.file_path');
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        parent::tearDown();
    }

    public function testAdd(): void
    {
        static::bootKernel();
        $filePath = static::getContainer()->getParameter('app.products.csv.file_path');
        $factory = static::getContainer()->get(DomainProductFactory::class);
        /** @var ProductRepository $repository */
        $repository = static::getContainer()->get(ProductRepository::class);


        $product = $factory->create(
            'Test product',
            9.99,
            'https://example.com',
            'https://example.com/img.png',
        );

        $repository->add($product);

        static::assertFileExists($filePath);
    }
}
