<?php

namespace App\Products\UI\CLI;

use App\Products\Application\Command\CreateProduct\CreateProductCommand;
use App\Products\Application\Exception\ProductImport\ProductImportExceptionInterface;
use App\Products\Application\Service\ProductImport\ImporterInterface;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'products:import',
    description: 'Import products from provided sources',
)]
class ProductsImportCommand extends Command
{
    private const int LIMIT = 200;

    /**
     * @var ImporterInterface[]
     */
    private array $importers;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        ImporterInterface $importer,
        ImporterInterface ...$importers
    ) {
        array_unshift($importers, $importer);
        $this->importers = $importers;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'limit',
            'l',
            InputOption::VALUE_REQUIRED,
            'Limit the number of products to import',
            self::LIMIT,
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $limit = (int)$input->getOption('limit');
        if ($limit <= 0) {
            $io->error('Limit must be greater than 0');
            return Command::INVALID;
        }

        $importer = $this->importers[0];
        if (count($this->importers) > 1) {
            $importer = $io->choice('Choose import source', $this->importers);
        }

        $bar = $io->createProgressBar();
        $bar->setFormat(" Importing... [%memory:6s%] \n -> %message%\n");
        $bar->setMessage('looking for products');
        $bar->start();
        $count = 0;
        foreach ($importer->import($limit) as $productDto) {
            if ($productDto instanceof ProductImportExceptionInterface) {
                $bar->setMessage('nothing to import');
                $bar->advance();
                break;
            }

            $command = new CreateProductCommand(
                $productDto->title,
                $productDto->price,
                $productDto->sourceUrl,
                $productDto->imageUrl,
            );

            $bar->setMessage(sprintf('the product is sent to the bus ( %s )', $productDto->title));
            $bar->advance();
            $this->commandBus->execute($command);
            $count++;
        }

        if ($count > 0) {
            $bar->setMessage(sprintf('complete (products count: %d)', $count));
        }
        $bar->finish();

        $io->success("Command completed");
        return Command::SUCCESS;
    }
}
