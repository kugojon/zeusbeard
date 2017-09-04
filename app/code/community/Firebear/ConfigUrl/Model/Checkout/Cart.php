<?php
class Firebear_ConfigUrl_Model_Checkout_Cart extends Mage_Checkout_Model_Cart
{
    /**
     * Add product to shopping cart (quote)
     *
     * @param   int|Mage_Catalog_Model_Product $productInfo
     * @param   mixed $requestInfo
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProduct($productInfo, $requestInfo=null)
    {
        if($productInfo instanceof Mage_Catalog_Model_Product && isset($requestInfo['super_attribute'])) {
            $productInfo = $productInfo->getTypeInstance(true)->getProductByAttributes($requestInfo['super_attribute'], $productInfo);
        }
        
        return parent::addProduct($productInfo, $requestInfo);
    }
}