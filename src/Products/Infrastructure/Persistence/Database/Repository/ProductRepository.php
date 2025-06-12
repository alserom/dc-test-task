<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\Database\Repository;

use App\Products\Domain\Entity\Product;
use App\Products\Domain\Repository\ProductRepositoryInterface;
use App\Products\Domain\ValueObject\ProductId;
use App\Shared\Domain\Exception\Repository\MappingException;
use App\Shared\Domain\Exception\Repository\RepositoryException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $product): ProductId
    {
        $em = $this->getEntityManager();

        try {
            $em->persist($product);
            $em->flush();
        } catch (\Throwable $e) {
            throw new RepositoryException(repositoryClassName: self::class, previous: $e);
        }

        return $product->getId();
    }

    public function get(?int $limit = null): array
    {
        $builder = $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC');

        if ($limit !== null) {
            $builder->setMaxResults($limit);
        }

        try {
            return $builder->getQuery()->getResult();
        } catch (ConversionException $e) {
            throw new MappingException(repositoryClassName: self::class, previous: $e);
        } catch (\Throwable $e) {
            throw new RepositoryException(repositoryClassName: self::class, previous: $e);
        }
    }
}
