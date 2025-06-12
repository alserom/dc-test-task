<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception\Repository;

class RepositoryException extends \RuntimeException
{
    public function __construct(
        private readonly string $repositoryClassName,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getRepositoryClassName(): string
    {
        return $this->repositoryClassName;
    }
}
