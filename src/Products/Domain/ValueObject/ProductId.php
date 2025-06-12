<?php

declare(strict_types=1);

namespace App\Products\Domain\ValueObject;

use App\Products\Domain\Exception\ValueObject\ProductIdIsNotValidException;
use Symfony\Component\Uid\Ulid;

final readonly class ProductId implements \Stringable
{
    private function __construct(public string $value)
    {
    }

    public static function create(?string $value = null): self
    {
        if ($value !== null) {
            self::validate($value);
        }

        $value = $value ?? Ulid::generate();

        return new self($value);
    }

    private static function validate(string $value): void
    {
        if (!Ulid::isValid($value)) {
            throw new ProductIdIsNotValidException('Incompatible value for product ID');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
