<?php

class Firebear_ConfigUrl_Helper_Data extends Mage_Cms_Helper_Data
{
    public function getSimpleJsonConfig($_product)
    {
        $config = array();

        if(Mage::getStoreConfig('firebear_configurl/general/enabled')) {
            $config = array(
                'priceClass' => Mage::getStoreConfig('firebear_configurl/general/price_id'),
                'tierPriceClass' => Mage::getStoreConfig('firebear_configurl/general/tier_price_id')
            );
            if ($_product->getTypeInstance(true)->hasOptions($_product)) {
                $productBlock = Mage::app()->getLayout()->getBlock('product.info');
                $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
                $allProducts = $_product->getTypeInstance(true)
                    ->getUsedProducts(null, $_product);
                foreach ($allProducts as $product) {
                    $product->load($product->getId());

                    $_regularPrice = $product->getPrice();
                    $_finalPrice = $product->getFinalPrice();
                    if ($product->isSaleable() || $skipSaleableCheck) {
                        $tierPrices = $productBlock->getTierPrices($product);
                        $config[$product->getId()] = array(
                            'url' => $product->getProductUrl(),
                            'title' => $product->getName(),
                            'price' => Mage::helper('core')->currency($_finalPrice, false, false),
                            'oldPrice' => Mage::helper('core')->currency($_regularPrice, false, false),
                            'tierPrices' => $tierPrices,
                            'tierPricesHtml' => $productBlock->getTierPriceHtml($product),
                            'priceBlockHtml' => $productBlock->getPriceHtml($product)
                        );
                    }
                }
            }
        }

        return Mage::helper('core')->jsonEncode($config);
    }
}
