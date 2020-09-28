<?php

namespace jtl\CustomerOrderAdditionalData;

use jtl\Connector\Event\CustomerOrder\CustomerOrderAfterPullEvent;
use jtl\Connector\Core\Logger\Logger;
use jtl\Connector\Formatter\ExceptionFormatter;
use jtl\Connector\Model\CustomerOrder;
use jtl\Connector\Model\CustomerOrderAttr;

/**
 * Class CustomerOrderListener
 * @package jtl\CustomerOrderAdditionalData
 */
class CustomerOrderListener
{
    /**
     * @param CustomerOrderAfterPullEvent $event
     */
    public function onCustomerOrderAfterPullAction(CustomerOrderAfterPullEvent $event)
    {
        $order = $event->getCustomerOrder();
        $shippingAddress = $order->getShippingAddress();

        $orderId = $order->getId()->getEndpoint();

        $phone = (string) get_post_meta($orderId, 'your_meta_field_key', true);

        $shippingAddress->setPhone($phone);
    }
}
