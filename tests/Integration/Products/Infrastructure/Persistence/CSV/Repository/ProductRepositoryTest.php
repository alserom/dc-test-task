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
    private const string FILE_PATH = ProductRepository::DEFAULT_FILE_PATH . '__test.csv';

    public function tearDown(): void
    {
        if (file_exists(self::FILE_PATH)) {
            unlink(self::FILE_PATH);
        }
        parent::tearDown();
    }

    public function testAdd(): void
    {
        static::bootKernel();
        $factory = static::getContainer()->get(DomainProductFactory::class);
        /** @var ProductRepository $repository */
        $repository = static::getContainer()->get(ProductRepository::class);


        $product = $factory->create(
            'Test product',
            9.99,
            'https://example.com',
            'https://example.com/img.png',
        );

        $repository->withFilePath(self::FILE_PATH)->add($product);

        static::assertFileExists($repository->getFilePath());
    }
}
