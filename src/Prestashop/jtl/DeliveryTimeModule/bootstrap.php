<?php

namespace jtl\DeliveryTimeModule;

use jtl\Connector\Event\CustomerOrder\CustomerOrderAfterPullEvent;
use jtl\Connector\Plugin\IPlugin;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Bootstrap
 * @package jtl\DeliveryTimeModule
 */
class Bootstrap implements IPlugin
{
    /**
     * @param EventDispatcher $dispatcher
     * @throws \PrestaShopDatabaseException
     */
    public function registerListener(EventDispatcher $dispatcher)
    {
        $database = \Db::getInstance();

        if ($this->isDeliveryModuleActive($database)) {
            $dispatcher->addListener(CustomerOrderAfterPullEvent::EVENT_NAME, [
                new CustomerOrderListener($database),
                'onCustomerOrderAfterPullAction'
            ]);
        }
    }

    /**
     * @param \Db $database
     * @return bool
     * @throws \PrestaShopDatabaseException
     */
    protected function isDeliveryModuleActive(\Db $database)
    {
        $result = $database->query(
            sprintf('SELECT COUNT(1) FROM %smodule WHERE name = "%s"', $database->getPrefix(), 'deliverytime')
        );

        return (bool)$result->rowCount();
    }
}