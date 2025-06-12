<?php

declare(strict_types=1);

namespace App\Products\Application\Query\GetProducts;

use App\Shared\Application\Query\QueryInterface;

readonly class GetProductsQuery implements QueryInterface
{
    /**
     * @param int<1, max>|null $limit
     */
    public function __construct(public ?int $limit = 5)
    {
    }
}
