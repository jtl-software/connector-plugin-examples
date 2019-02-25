<?php
/**
 * @author Immanuel Klinkenberg <immanuel.klinkenberg@jtl-software.com>
 * @author Daniel Böhmer <daniel.böhmer@jtl-software.com>
 * @copyright 2010-2019 JTL-Software GmbH
 */
namespace jtl\MaxOrderQuantity;

use jtl\Connector\Event\Product\ProductAfterPushEvent;
use jtl\Connector\Shopware\Utilities\IdConcatenator;
use jtl\Connector\Shopware\Utilities\Shop as ShopUtil;
use jtl\Connector\Core\Logger\Logger;
use jtl\Connector\Formatter\ExceptionFormatter;
use jtl\Connector\Core\Utilities\Language as LanguageUtil;
use Shopware\Models\Article\Detail;

class ProductListener
{
    public function onProductAfterPushAction(ProductAfterPushEvent $event)
    {
        if (strlen($event->getProduct()->getId()->getEndpoint()) == 0) {
            return;
        }

        try {
            foreach ($event->getProduct()->getAttributes() as $attribute) {
                foreach ($attribute->getI18ns() as $i18n) {
                    if ($i18n->getLanguageISO() === LanguageUtil::map(Shopware()->Shop()->getLocale()->getLocale())
                        && $i18n->getName() === 'max_order_quantity') {

                        list($detailId, $productId) = IdConcatenator::unlink($event->getProduct()->getId()->getEndpoint());

                        $detailSW = Shopware()->Models()->find(Detail::class, $detailId);
                        if ($detailSW !== null) {
                            $detailSW->setMaxPurchase((int) $i18n->getValue());

                            ShopUtil::entityManager()->persist($detailSW);
                            ShopUtil::entityManager()->flush($detailSW);
                        }
                        break;
                    }
                }
            }
        } catch (\Throwable $ex) {
            Logger::write(ExceptionFormatter::format($ex), Logger::WARNING, 'plugin');
        }
    }
}