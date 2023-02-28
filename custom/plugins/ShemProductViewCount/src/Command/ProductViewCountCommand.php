<?php declare(strict_types=1);

namespace Shem\ProductViewCount\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class ProductViewCountCommand extends Command
{
    // Command name
    protected static $defaultName = 'shem:product-view';

    public function __construct(Connection $connection, EntityRepository $productRepository, string $name = null) 
    {
        parent::__construct($name);
        $this->connection = $connection;
        $this->productRepository = $productRepository;

        $this->addOption('start', null, InputOption::VALUE_REQUIRED);
        $this->addOption('end', null, InputOption::VALUE_REQUIRED);
        
    }

    // Provides a description, printed out in bin/console
    protected function configure(): void
    {
        $this->setDescription('Does something very special.');
    }

    public function validISO8601Date($value)
    {
        if (!is_string($value)) {
            return false;
        }

        $dateTime = \DateTime::createFromFormat(\DateTime::ISO8601, $value);

        if ($dateTime) {
            return $dateTime->format(\DateTime::ISO8601) === $value;
        }

        return false;
    }

    // Actual code executed in the command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln('--------------------------------------------------------------------');

        if ($input->hasOption('start') && $input->getOption('start')) {
            $date_start = $this->validISO8601Date($input->getOption('start'));
            if (!$date_start) {
                $output->writeln('incorrect start date value '.$date_start);
                return 0;
            }

        }

        $date_end = $this->validISO8601Date($input->getOption('end'));
        if ($input->hasOption('end') && $input->getOption('end')) {
            if (!$date_end) {
                $output->writeln('incorrect end date value '.$date_end);
                return 0;
            }
        }

        $query = <<<SQL
        SELECT REPLACE(BIN_TO_UUID(`product_id`), '-', '') product_id, COUNT(*) cnt 
        FROM `product_view`
        
        SQL;
        

        $query.= 'GROUP BY `product_id`';
    
        $results = $this->connection->query($query)->fetchAll();

        $context = Context::createDefaultContext();

        foreach ($results as $result) { 

            // better to select all products at once
            $product = $this->productRepository->search(new Criteria([$result['product_id']]), $context)->first();
            $output->writeln($result['product_id'].' : '.$product->getName().' = '.$result['cnt']);
        }


        // Exit code 0 for success
        return 0;
    }
}