<?php declare(strict_types=1);

namespace Shem\ProductViewCount\Subscriber;

//use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Content\Product\ProductEvents;
use Doctrine\DBAL\Connection;

class ProductView implements EventSubscriberInterface
{
    protected $triggered_count = 0;
    public function __construct(Connection $connection) 
    {
        $this->connection = $connection;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductEvents::PRODUCT_LOADED_EVENT => 'onProductsLoaded'
        ];
    }

    public function onProductsLoaded(EntityLoadedEvent $event)
    {        
        //print_r($event->getEntities());

        if ($this->triggered_count == 0) {
            $this->triggered_count++;
        

            foreach ($event->getEntities() as $product) {
                //print $product->getId().'-'.$product->getVersionId().' ';
                
                $query = <<<SQL
                INSERT INTO `product_view`
                SET `product_id` = UUID_TO_BIN('{$product->getId()}'),
                    `product_version_id` = UUID_TO_BIN('{$product->getVersionId()}'),
                    `date` = NOW() 
                SQL;
            

                $this->connection->executeStatement($query);

            }
        }

        //$this->connection->

    }


}