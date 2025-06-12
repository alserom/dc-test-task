<?php

declare(strict_types=1);

namespace App\Shared\Application\Query;

interface QueryBusInterface
{
    public function handle(QueryInterface $query): mixed;
}
