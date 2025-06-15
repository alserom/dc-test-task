<?php

declare(strict_types=1);

namespace App\Products\Application\EventHandler\ProductSaved;

use App\Products\Application\Event\ProductSavedEvent;
use App\Products\Infrastructure\Persistence\CSV\Repository\ProductRepository;
use App\Shared\Application\Event\EventHandlerInterface;
use App\Shared\Domain\Exception\Repository\RepositoryException;
use Psr\Log\LoggerInterface;

final readonly class AddProductToCsvFile implements EventHandlerInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ProductSavedEvent $event): void
    {
        try {
            $this->productRepository->add($event->getEntity());
        } catch (RepositoryException $e) {
            $this->logger->error(
                'Error on adding product to CSV file',
                [
                    'repository' => $e->getRepositoryClassName(),
                    'exception' => $e
                ]
            );
            throw $e;
        }
    }
}
