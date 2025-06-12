<?php

declare(strict_types=1);

namespace App\Products\Application\Command\CreateProduct;

use App\Products\Application\Event\ProductSavedEvent;
use App\Products\Domain\Factory\ProductFactory;
use App\Products\Domain\Repository\ProductWriteOnlyRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Exception\Repository\RepositoryException;
use Psr\Log\LoggerInterface;

final readonly class CreateProductCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ProductWriteOnlyRepositoryInterface $productRepository,
        private ProductFactory $productFactory,
        private EventBusInterface $eventBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreateProductCommand $command): void
    {
        $product = $this->productFactory->create(
            $command->title,
            $command->price,
            $command->sourceUrl,
            $command->imageUrl,
        );

        try {
            $this->productRepository->add($product);
        } catch (RepositoryException $e) {
            $this->logger->error(
                'Error on adding product to DB',
                [
                    'repository' => $e->getRepositoryClassName(),
                    'exception' => $e
                ]
            );
            throw $e;
        }

        $this->eventBus->dispatch(new ProductSavedEvent($product));
    }
}
