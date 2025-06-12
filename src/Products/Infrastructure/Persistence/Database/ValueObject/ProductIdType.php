<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\Database\ValueObject;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;
use App\Products\Domain\ValueObject\ProductId;

final class ProductIdType extends StringType
{
    public const string NAME = 'product_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(
            [
                'length' => 26,
                'fixed' => true,
            ]
        );
    }

    /** @throws ConversionException */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ProductId) {
            return parent::convertToDatabaseValue((string)$value, $platform);
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', ProductId::class],
        );
    }

    /** @throws ConversionException */
    public function convertToPHPValue($value, AbstractPlatform $platform): ProductId
    {
        /** @var string|null $value */
        $value = parent::convertToPHPValue($value, $platform);

        try {
            return ProductId::create($value);
        } catch (\Throwable $e) {
            throw ConversionException::conversionFailed(
                $value,
                $this->getName(),
                $e,
            );
        }
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
