<?php declare(strict_types=1);

namespace Shem\ProductViewCount;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class ShemProductViewCount extends Plugin
{

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        // DROP TABLE
        
    }


}