<?php

namespace App\Tests\Feature\Products\UI\Http\REST\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * TODO@technical-debt: Tests can be improved
 * Here we can test all functionality chain, including the message bus, fetch from db and so on...
 */
class ProductsListControllerTest extends WebTestCase
{
    private const string URL = '/api/products';

    public function testEndpointAvailable(): void
    {
        $client = static::createClient();
        $client->request('GET', self::URL);

        static::assertResponseIsSuccessful();
        static::assertResponseFormatSame('json');
    }

    public function testLimitAcceptPositiveIntegers(): void
    {
        $client = static::createClient();
        $client->request('GET', self::URL . '?limit=5');

        static::assertResponseIsSuccessful();
        static::assertResponseFormatSame('json');
    }

    public function testLimitNotAcceptValue(string $value = 'string'): void
    {
        $client = static::createClient();
        $client->request('GET', self::URL . '?limit=' . $value);

        static::assertResponseStatusCodeSame(422);
    }

    public function testLimitNotAcceptZero(): void
    {
        $this->testLimitNotAcceptValue('0');
    }

    public function testLimitNotAcceptNegative(): void
    {
        $this->testLimitNotAcceptValue('-5');
    }
}
