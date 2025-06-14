<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\CSV;

use App\Shared\Infrastructure\Persistence\CSV\Exception\WriterException;
use Stringable;

interface WriterInterface
{
    public function getFilePath(): string;

    /**
     * @param array<null|int|float|string|Stringable> $record
     *
     * @throws WriterException
     */
    public function insertOne(array $record): void;
}
