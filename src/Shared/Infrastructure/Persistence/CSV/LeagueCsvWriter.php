<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\CSV;

use App\Shared\Infrastructure\Persistence\CSV\Exception\WriterException;
use League\Csv\UnavailableStream;
use League\Csv\Writer;

final readonly class LeagueCsvWriter implements WriterInterface
{
    public function __construct(
        private string $filePath,
        private string $openMode,
    ) {
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function insertOne(array $record): void
    {
        try {
            $this->getWriter()->insertOne($record);
        } catch (\Throwable $e) {
            throw new WriterException(previous: $e);
        }
    }

    /**
     * @throws UnavailableStream
     */
    private function getWriter(): Writer
    {
        return Writer::createFromPath($this->filePath, $this->openMode);
    }
}
