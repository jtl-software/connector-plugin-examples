<?php
/**
 * @author Immanuel Klinkenberg <immanuel.klinkenberg@jtl-software.com>
 * @copyright 2010-2019 JTL-Software GmbH
 */
namespace jtl\ConsiderStockLevel;

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
        try {
            list($detailId, $articleId) = IdConcatenator::unlink($event->getProduct()->getId()->getEndpoint());

            $product = $event->getProduct();
            $isParent = $product->getIsMasterProduct();
            $isSingle = !$isParent && $product->getMasterProductId()->getHost() === 0;

            foreach ($event->getProduct()->getAttributes() as $attribute) {
                foreach ($attribute->getI18ns() as $i18n) {
                    if ($i18n->getLanguageISO() === LanguageUtil::map(ShopUtil::locale()->getLocale())
                        && $i18n->getName() === 'consider_stock_level' && !empty($i18n->getValue())) {

                        $lastStock = !in_array($i18n->getValue(), ['0', 'false']);

                        /** @var Detail $detail */
                        $detail = ShopUtil::entityManager()->find(Detail::class, $detailId);
                        if ($detail instanceof Detail) {
                            $detail->setLaststock((int)$lastStock);
                            if($isSingle || $isParent) {
                                $article = $detail->getArticle();
                                $article->setLastStock($lastStock);
                                ShopUtil::entityManager()->persist($article);
                            }

                            ShopUtil::entityManager()->persist($detail);
                            ShopUtil::entityManager()->flush();
                        }
                        return;
                    }
                }
            }
        } catch (\Throwable $e) {
            Logger::write(ExceptionFormatter::format($e), Logger::WARNING, 'plugin');
        }
    }
}

