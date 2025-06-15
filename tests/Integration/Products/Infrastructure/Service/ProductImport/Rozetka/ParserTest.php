<?php

namespace App\Tests\Integration\Products\Infrastructure\Service\ProductImport\Rozetka;

use App\Products\Application\DTO\ProductDTO;
use App\Products\Application\Exception\ProductImport\ProductImportExceptionInterface;
use App\Products\Infrastructure\Service\ProductImport\Rozetka\Parser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ParserTest extends KernelTestCase
{
    public function testImport(): void
    {
        self::bootKernel();
        $this->mockHttpClient();
        /** @var Parser $parser */
        $parser = static::getContainer()->get(Parser::class);

        $products = iterator_to_array($parser->import(1000));

        static::assertCount(158, $products);
        static::assertContainsOnlyInstancesOf(ProductDTO::class, $products);
    }

    public function testImportWithInvalidRequest(): void
    {
        self::bootKernel();
        $this->mockHttpClient(true);
        /** @var Parser $parser */
        $parser = static::getContainer()->get(Parser::class);

        $products = iterator_to_array($parser->import(1000));
        $error = $products[array_key_last($products)];

        static::assertCount(61, $products);
        static::assertContainsNotOnlyInstancesOf(ProductDTO::class, $products);
        static::assertInstanceOf(ProductImportExceptionInterface::class, $error);
    }

    private function mockHttpClient(bool $withInvalidSecondPage = false): void
    {
        $responses = [
            new MockResponse((string)file_get_contents('tests/fixtures/Rozetka/page-1.html')), // 60 items per page
            new MockResponse(
                (string)file_get_contents('tests/fixtures/Rozetka/page-2.html'),
                ['http_code' => $withInvalidSecondPage ? 404 : 200]
            ),
            new MockResponse((string)file_get_contents('tests/fixtures/Rozetka/page-last.html')), // 38 items on the last page
        ];

        $client = new MockHttpClient($responses);

        self::getContainer()->set(HttpClientInterface::class, $client);
    }
}
