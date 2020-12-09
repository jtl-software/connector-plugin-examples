<?php
/**
 * @copyright 2010-2013 JTL-Software GmbH
 */

namespace jtl\SubshopCompanies\Listener;

use jtl\Connector\Core\Logger\Logger;
use jtl\Connector\Event\CustomerOrder\CustomerOrderAfterPullEvent;
use jtl\Connector\Formatter\ExceptionFormatter;
use jtl\Connector\Model\CustomerOrderAttr;
use jtl\Connector\Model\Identity;
use jtl\Connector\Shopware\Utilities\IdConcatenator;
use jtl\SubshopCompanies\Loader\CompanyLoader;

class CustomerOrderListener
{
    public function onCustomerOrderAfterPullAction(CustomerOrderAfterPullEvent $event)
    {
        // Get subshop id from customer order
        $subshop_id = Shopware()->Db()->fetchOne(
            'SELECT subshopID FROM s_order WHERE id = ?',
            [$event->getCustomerOrder()->getId()->getEndpoint()]
        );
    
        if ($subshop_id === false) {
            return;
        }
        
        try {
            // Get company with subshop id from customer order
            $company = CompanyLoader::get((int)$subshop_id);
            
            if (!is_null($company) && isset($company['company'])) {
                
                // Add attribute with company name
                $event->getCustomerOrder()->addAttribute(
                    (new CustomerOrderAttr())->setId(
                        new Identity(IdConcatenator::link([
                            'company',
                            $event->getCustomerOrder()->getId()->getEndpoint()
                        ]))
                    )
                        ->setCustomerOrderId($event->getCustomerOrder()->getId())
                        ->setKey('company')
                        ->setValue($company['company'])
                );
            }
        } catch (\Exception $e) {
            Logger::write(ExceptionFormatter::format($e), Logger::ERROR, 'plugin');
        }
    }
}
