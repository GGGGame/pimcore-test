<?php

namespace App\Command;

use App\DataObjects\Cars;
use App\DataObjects\Helper\Normalizer;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCarsCommand extends Command
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
            ->setName('app:import-cars')
            ->setDescription('Import car data')
            ->addArgument('PathCsvFile', InputArgument::REQUIRED, 'Path to csv file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csv = $input->getArgument('PathCsvFile');

        if (!file_exists($csv) || !is_readable($csv)) {
            $output->writeln('Cannot found or read the current file');
            return Command::FAILURE;
        }

        if (($file = fopen($csv, 'r')) !== false) {
            $this->logger->info('Import cars started', [
                'fileObject' => $csv
            ]);
            $header = null;
            while (($row = fgetcsv($file, 0, ';')) !== false) {
                try {
                    if (!$header) {
                        // this normalizer fix many problems of 
                        // data conversion from CSV to CamelCase requirements
                        $header = Normalizer::normalize($row);
                        continue;
                    }

                    $data = array_combine($header, $row);

                    // transform strings to int value where required
                    $data = Normalizer::normalizeType($data);

                    // Linked to src\DataObjects\Cars.php, i prefer this way to 
                    // mantain everything readable and easy to maintain.
                    $cars = new Cars($data, $header);

                    $output->writeln('Importing: ' . $cars->getCar()->getId());
                } catch (\Exception $e) {
                    $output->writeln("Errore importazione ID {$data['ID']} " . $e->getMessage());
                    $this->logger->error('Errore importazione auto: ' . $e->getMessage(), [
                        'relatedObject' => $data
                    ]);
                }

            }
            $this->logger->info('Import cars finished', [
                'fileObject' => $csv
            ]);
            fclose($file);
        }

        return Command::SUCCESS;
    }

    protected function getImportService(): string
    {
        return 'app.import.cars';
    }
}