<?php

namespace jtl\CustomerOrderAdditionalData;

use jtl\Connector\Event\CustomerOrder\CustomerOrderAfterPullEvent;
use jtl\Connector\Plugin\IPlugin;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Bootstrap
 * @package jtl\CustomerOrderAdditionalData
 */
class Bootstrap implements IPlugin
{
    /**
     * @param EventDispatcher $dispatcher
     */
    public function registerListener(EventDispatcher $dispatcher)
    {
        $dispatcher->addListener(CustomerOrderAfterPullEvent::EVENT_NAME, [
            new CustomerOrderListener(),
            'onCustomerOrderAfterPullAction'
        ]);
    }
}
