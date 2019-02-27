<?php
/**
 * @author Immanuel Klinkenberg <immanuel.klinkenberg@jtl-software.com>
 * @copyright 2010-2019 JTL-Software GmbH
 */
namespace jtl\ConsiderStockLevel;

use jtl\Connector\Plugin\IPlugin;
use jtl\Connector\Event\Product\ProductAfterPushEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Bootstrap implements IPlugin
{
    public function registerListener(EventDispatcher $dispatcher)
    {
        $dispatcher->addListener(ProductAfterPushEvent::EVENT_NAME, [
            new ProductListener(),
            'onProductAfterPushAction'
        ]);
    }
}