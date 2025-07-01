<?php

namespace App\Command;

use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Pimcore\Model\DataObject\Car\Listing;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCars extends Command
{
    private ApplicationLogger $logger;

    public function __construct(ApplicationLogger $logger)
    {
        parent::__construct();
        $this->logger = $logger;
    }


    protected function configure(): void
    {
        $this
            ->setName('app:delete-cars')
            ->setDescription('delete cars');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $carListing = new Listing();
        $carListing->setUnpublished(true);
        $cars = $carListing->load();

        try {
            foreach ($cars as $car) {
                $output->writeln('Deleting car: ' . $car->getKey());
                $car->delete();
            }
        } catch (\Exception $e) {
            $this->logger->error('Error deleting cars: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln('All cars have been deleted.');
        $this->logger->info('All cars have been deleted.');
        

        return Command::SUCCESS;
    }

    protected function getImportService(): string
    {
        return 'app.delete.cars';
    }
}