<?php
namespace jtl\AddSpecialItem;

use jtl\Connector\Plugin\IPlugin;
use jtl\Connector\Event\CustomerOrder\CustomerOrderAfterPullEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Bootstrap implements IPlugin
{
    public function registerListener(EventDispatcher $dispatcher)
    {
        $dispatcher->addListener(CustomerOrderAfterPullEvent::EVENT_NAME, [
            new CustomerOrderListener(),
            'afterPull'
        ]);
    }
}