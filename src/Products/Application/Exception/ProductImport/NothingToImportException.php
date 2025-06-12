<?php

declare(strict_types=1);

namespace App\Products\Application\Exception\ProductImport;

class NothingToImportException extends \RuntimeException implements ProductImportExceptionInterface
{
    public function __construct(
        private readonly string $importerName,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getImporterName(): string
    {
        return $this->importerName;
    }
}
