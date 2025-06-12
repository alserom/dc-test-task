<?php

declare(strict_types=1);

namespace App\Products\UI\Http\REST\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductsListQueryParameters
{
    /**
     * @param int<1, max> $limit
     */
    public function __construct(
        #[Assert\GreaterThan(0)]
        public int $limit = 5,
    ) {
    }
}
