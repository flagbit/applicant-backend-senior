<?php declare(strict_types=1);

namespace Shem\ProductViewCount\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1677580832ProductViewCount extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1677580832;
    }

    public function update(Connection $connection): void
    {

        $query = <<<SQL
        CREATE TABLE IF NOT EXISTS `product_view` (
            `product_id`            BINARY(16)  NOT NULL,
            `product_version_id`    BINARY(16)  NOT NULL,
            `date`                  DATETIME    NOT NULL,
            CONSTRAINT  `fk.product_view.product_id` FOREIGN KEY (`product_id`, `product_version_id`) REFERENCES `product` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE 
        )
            ENGINE = InnoDB
            DEFAULT CHARSET = utf8mb4
            COLLATE = utf8mb4_unicode_ci;
        SQL;
        

        $connection->executeStatement($query);
 
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
