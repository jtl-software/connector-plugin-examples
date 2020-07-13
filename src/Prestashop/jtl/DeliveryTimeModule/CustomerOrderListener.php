<?php

namespace jtl\DeliveryTimeModule;

use jtl\Connector\Event\CustomerOrder\CustomerOrderAfterPullEvent;
use jtl\Connector\Core\Logger\Logger;
use jtl\Connector\Formatter\ExceptionFormatter;
use jtl\Connector\Model\CustomerOrder;
use jtl\Connector\Model\CustomerOrderAttr;

/**
 * Class CustomerOrderListener
 * @package jtl\DeliveryTimeModule
 */
class CustomerOrderListener
{
    /**
     * @var \Db
     */
    protected $database;

    /**
     * CustomerOrderListener constructor.
     * @param \Db $database
     */
    public function __construct(\Db $database)
    {
        $this->database = $database;
    }

    /**
     * @param CustomerOrderAfterPullEvent $event
     */
    public function onCustomerOrderAfterPullAction(CustomerOrderAfterPullEvent $event)
    {
        try {

            $orderId = $event->getCustomerOrder()->getId()->getEndpoint();

            $deliveryTimeParams = $this->getDeliveryTimeParams($orderId);

            if ($deliveryTimeParams !== false && is_array($deliveryTimeParams)) {
                $this->setDeliveryTimeAttributes($event->getCustomerOrder(), $deliveryTimeParams);
            }

        } catch (\Throwable $e) {
            Logger::write(ExceptionFormatter::format($e), Logger::WARNING, 'plugin');
        }
    }

    /**
     * @param CustomerOrder $customerOrder
     * @param array $deliveryTimeParams
     */
    protected function setDeliveryTimeAttributes(CustomerOrder $customerOrder, array $deliveryTimeParams)
    {
        foreach ($deliveryTimeParams as $key => $value) {

            $customerOrderAttribute = new CustomerOrderAttr();
            $customerOrderAttribute->setCustomerOrderId($customerOrder->getId());
            $customerOrderAttribute->setKey($key);
            $customerOrderAttribute->setValue($value);

            $customerOrder->addAttribute($customerOrderAttribute);
        }
    }

    /**
     * @param $orderId
     * @return bool|\mysqli_result|\PDOStatement|resource
     * @throws \PrestaShopDatabaseException
     */
    protected function getDeliveryTimeParams($orderId)
    {
        return $this->database->getRow(
            sprintf('SELECT 
                            from_time AS delivery_time_from,
                            to_time AS delivery_time_to, 
                            delivery_day AS delivery_time_day 
                        FROM %sdelivery_time_order 
                        WHERE id_cart = %s',
                $this->database->getPrefix(), $orderId
            )
        );
    }
}