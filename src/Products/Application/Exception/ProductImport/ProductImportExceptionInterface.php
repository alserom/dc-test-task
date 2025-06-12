<?php

declare(strict_types=1);

namespace App\Products\Application\Exception\ProductImport;

interface ProductImportExceptionInterface extends \Throwable
{
    public function getImporterName(): string;
}
