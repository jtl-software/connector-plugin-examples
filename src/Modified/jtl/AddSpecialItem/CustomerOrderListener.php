<?php
namespace jtl\AddSpecialItem;

use jtl\Connector\Core\Database\Mysql;
use jtl\Connector\Event\CustomerOrder\CustomerOrderAfterPullEvent;
use jtl\Connector\Model\CustomerOrderItem;
use jtl\Connector\Model\Identity;

class CustomerOrderListener
{
    public function afterPull(CustomerOrderAfterPullEvent $event)
    {
        $order = $event->getCustomerOrder();
        $orderId = $order->getId()->getEndpoint();
        if(!empty($orderId)) {
            $db = Mysql::getInstance();

            $sql = sprintf('SELECT `orders_total_id`, `title`, `value` FROM `orders_total` WHERE `orders_id` = %d AND class = \'%s\'', $orderId, 'ot_sperrgut');
            $result = $db->query($sql);
            if(is_array($result) && isset($result[0])) {
                $item = (new CustomerOrderItem())
                    ->setType(CustomerOrderItem::TYPE_PRODUCT)
                    ->setId(new Identity($result[0]['orders_total_id']))
                    ->setName($result[0]['title'])
                    ->setPriceGross($result[0]['orders_total_id'])
                    ->setVat(19)
                    ->setQuantity(1)
                ;

                $order->addItem($item);
            }
        }
    }
}

