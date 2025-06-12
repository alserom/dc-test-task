<?php

namespace App\Tests\Feature\Products\UI\CLI;

use App\Products\Application\Command\CreateProduct\CreateProductCommand;
use App\Products\Application\DTO\ProductDTO;
use App\Products\Application\Exception\ProductImport\NothingToImportException;
use App\Products\Infrastructure\Service\ProductImport\Rozetka\Parser;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class ProductsImportCommandTest extends KernelTestCase
{
    use InteractsWithMessenger;

    public function testCommandSuccess(int $pCount = 10, int $limit = 5, int $resultCount = 5): void
    {
        self::bootKernel();
        $this->transport('async')->queue()->assertEmpty();
        $this->mockParser($pCount, $limit);
        $application = new Application(self::$kernel);
        $command = $application->find('products:import');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--limit' => $limit]);

        $commandTester->assertCommandIsSuccessful();
        static::assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->transport('async')->queue()->assertCount($resultCount);
        $this->transport('async')->queue()->assertContains(CreateProductCommand::class, $resultCount);
    }

    public function testCommandSuccessNotEnoughProducts(): void
    {
        $this->testCommandSuccess(1, 5, 1);
    }

    public function testCommandSuccessNoProducts(): void
    {
        $this->testCommandSuccess(0, 5, 0);
    }

    public function testCommandFailure(): void
    {
        self::bootKernel();
        $this->transport('async')->queue()->assertEmpty();
        $parser = $this->createMock(Parser::class);
        $parser->expects($this->never())->method('import');
        self::getContainer()->set(Parser::class, $parser);
        $application = new Application(self::$kernel);
        $command = $application->find('products:import');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--limit' => 0]);

        static::assertEquals(Command::INVALID, $commandTester->getStatusCode());
        $this->transport('async')->queue()->assertEmpty();
    }

    private function mockParser(int $importedProductsCount = 1, int $limit = 1): void
    {
        $import = static function () use ($importedProductsCount, $limit) {
            for ($i = 1; $i <= $importedProductsCount; $i++) {
                if ($i > $limit) {
                    yield new NothingToImportException(importerName: 'test');
                }

                yield new ProductDTO(
                    'Product #' . $i,
                    9.99,
                    'https://example.com/product' . $i,
                    'https://example.com/product.png',
                );
            }
        };


        $parser = $this->createMock(Parser::class);
        $parser->expects($this->once())
            ->method('import')
            ->willReturn($import());
        self::getContainer()->set(Parser::class, $parser);
    }
}
