<?php

declare(strict_types=1);

namespace App\Products\Application\Service\ProductImport;

use App\Products\Application\DTO\ProductDTO;
use App\Products\Application\Exception\ProductImport\ProductImportExceptionInterface;
use Iterator;

interface ImporterInterface
{
    /**
     * TODO@technical-debt: Replace $limit with some configuration
     * @param int<1, max> $limit
     * @return Iterator<int, ProductDTO|ProductImportExceptionInterface>
     */
    public function import(int $limit): Iterator;

    /**
     * Return unique name of your importer
     */
    public function __toString(): string;
}
