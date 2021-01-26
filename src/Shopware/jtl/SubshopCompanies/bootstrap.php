<?php
/**
 * @copyright 2010-2013 JTL-Software GmbH
 */

namespace jtl\SubshopCompanies;

use jtl\Connector\Event\CustomerOrder\CustomerOrderAfterPullEvent;
use jtl\Connector\Plugin\IPlugin;
use Symfony\Component\EventDispatcher\EventDispatcher;
use jtl\SubshopCompanies\Listener\CustomerOrderListener;

class Bootstrap implements IPlugin
{
    public function registerListener(EventDispatcher $dispatcher)
    {
        $dispatcher->addListener(CustomerOrderAfterPullEvent::EVENT_NAME, [
            new CustomerOrderListener(),
            'onCustomerOrderAfterPullAction'
        ]);
    }
}
