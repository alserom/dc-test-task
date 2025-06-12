<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Service\ProductImport\Rozetka;

use App\Products\Application\DTO\ProductDTO;
use App\Products\Application\Exception\ProductImport\NothingToImportException;
use App\Products\Application\Exception\ProductImport\ProductImportExceptionInterface;
use App\Products\Application\Service\ProductImport\ImporterInterface;
use Generator;
use Iterator;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\UriResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class Parser implements ImporterInterface
{
    private const string URL = 'https://hard.rozetka.com.ua/ua/monitors/c80089/';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface $logger,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @param int<1, max> $limit
     * @return Iterator<int, ProductDTO>
     */
    public function import(int $limit): Iterator
    {
        $parser = $this->parse(self::URL);
        foreach ($parser as $product) {
            yield $product;
            if ($product instanceof ProductImportExceptionInterface) {
                return;
            }
            $limit--;
            if ($limit === 0) {
                $parser->send('stop');
            }
        }
    }

    private function parse(string $url): Generator
    {
        try {
            $crawler = $this->createCrawler($url);
            $products = $this->getProducts($crawler);
        } catch (Throwable $e) {
            $this->logger->warning('Can not import products', ['exception' => $e, 'url' => $url, 'parser' => $this->__toString()]);
            yield new NothingToImportException(importerName: $this->__toString(), previous: $e);
            return;
        }

        foreach ($products as $product) {
            $input = yield $product;
            if ($input === 'stop') {
                return;
            }
        }

        $nextPage = $this->getNextPage($crawler);
        if ($nextPage === null) {
            return;
        }

        yield from $this->parse($nextPage);
    }

    public function __toString(): string
    {
        return 'Rozetka | Комп\'ютери та ноутбуки / Монітори';
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function createCrawler(string $url): Crawler
    {
        $content = $this->client
            ->request('GET', $url, ['max_redirects' => 0])
            ->getContent();

        return new Crawler($content);
    }

    private function getNextPage(Crawler $crawler): ?string
    {
        $node = $crawler
            ->filterXPath(
                '//rz-paginator/div[@class="paginator"]/div[contains(@class, "list")]/a[contains(@class, "active")]/following-sibling::a[1]'
            );

        try {
            $url = $node->attr('href');
        } catch (Throwable) {
            return null;
        }

        $nextUrl = UriResolver::resolve($url ?? '', self::URL);
        if ($nextUrl === self::URL) {
            return null;
        }

        return $nextUrl;
    }

    /**
     * @return ProductDTO[]
     */
    private function getProducts(Crawler $crawler): array
    {
        return $crawler->filterXPath('//rz-product-tile/div[@class="content"]')->each(function (Crawler $productNode) {
            $titleNode = $productNode->filterXPath('node()/a[contains(@class, "tile-title")]');
            $imageNode = $productNode->filterXPath('node()/a[@class="tile-image-host"]/img');
            $priceNode = $productNode->filterXPath('node()/div[@class="price-wrap"]//div[contains(@class, "price")]/text()');

            $data = [
                'title' => $titleNode->text(),
                'price' => (float)str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $priceNode->text()) ?? ''),
                'imageUrl' => $imageNode->attr('src'),
                'sourceUrl' => $titleNode->attr('href'),
            ];

            $this->validateData($data);

            /** @phpstan-ignore argument.type, argument.type, argument.type (data validated) */
            return new ProductDTO(...$data);
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    private function validateData(array $data): void
    {
        $constraint = new Assert\Collection([
            'title' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
            ],
            'price' => [
                new Assert\NotBlank(),
                new Assert\Type('float'),
                new Assert\Positive(),
            ],
            'imageUrl' => [
                new Assert\NotBlank(allowNull: true),
                new Assert\Url(),
            ],
            'sourceUrl' => [
                new Assert\NotBlank(),
                new Assert\Url(),
            ],
        ]);

        $violations = $this->validator->validate($data, $constraint);

        if ($violations->count() > 0) {
            throw new ValidationFailedException($data, $violations);
        }
    }
}
