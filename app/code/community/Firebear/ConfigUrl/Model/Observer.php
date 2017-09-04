<?php


class Firebear_ConfigUrl_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Generates config array to reflect the simple product's ($currentProduct)
     * configuration in its parent configurable product
     *
     * @param Mage_Catalog_Model_Product $parentProduct
     * @param Mage_Catalog_Model_Product $currentProduct
     * @return array array( configoptionid -> value )
     */
    protected function generateConfigData(Mage_Catalog_Model_Product $parentProduct, Mage_Catalog_Model_Product $currentProduct)
    {
        /* @var $typeInstance Mage_Catalog_Model_Product_Type_Configurable */
        $typeInstance = $parentProduct->getTypeInstance();
        if (!$typeInstance instanceof Mage_Catalog_Model_Product_Type_Configurable) {
            return; // not a configurable product
        }
        $configData = array();
        $attributes = $typeInstance->getUsedProductAttributes($parentProduct);

        foreach ($attributes as $code => $data) {

            $data = $currentProduct->getData($data->getAttributeCode());

            if (!empty($data) && $data != 0){
                $configData[$code] = $data;
            }
        }

        return $configData;
    }

    /**
     * Prepare and render configurable product with default values
     *
     * @param $controller
     * @param $productId
     * @param $defaultValues
     *
     * @throws Mage_Core_Exception
     */
    protected function _prepareAndRender($controller, $productId, $defaultValues)
    {
        $params = new Varien_Object();
        $params->setCategoryId(false);
        $params->setConfigureMode(true);
        $buyRequest = new Varien_Object();
        $buyRequest->setSuperAttribute($defaultValues); // example format: array(525 => "99"));

        $params->setBuyRequest($buyRequest);

        // override visibility setting of configurable product
        // in case only simple products should be visible in the catalog
        // TODO: make this behaviour configurable
        $params->setOverrideVisibility(true);

        /* @var $productViewHelper Mage_Catalog_Helper_Product_View */
        $productViewHelper = Mage::helper('catalog/product_view');

        $controller->getRequest()->setDispatched(true);
        // avoid double dispatching
        // @see Mage_Core_Controller_Varien_Action::dispatch()
        $controller->setFlag('', Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH, true);

        $productViewHelper->prepareAndRender($productId, $controller, $params);
    }

    /**
     * Checks if the current product has a super-product assigned
     * Finds the super product
     * @param $observer Varien_Event_Observer $observer
     * @throws Exception
     * @return boolean
     */
    public function forwardToConfigurable($observer)
    {
        if (Mage::getStoreConfig('firebear_configurl/general/enabled')){

            $controller = $observer->getControllerAction();
            $productId = (int)$controller->getRequest()->getParam('id');

            Mage::register('simple_product_id', $productId);

            self::setSid($productId);

            $parentIds = Mage::getModel('catalog/product_type_configurable')
                ->getParentIdsByChild($productId);
			
			$product = Mage::getModel('catalog/product')->load($productId);
			
            if($product instanceof Mage_Catalog_Model_Product && $product->isConfigurable()) {
                
                $allowAttributes = $product->getTypeInstance(true)
                    ->getConfigurableAttributes($product);
                $defaultValues = array();

                foreach ($allowAttributes as $attribute) {
                    $prices = $attribute->getPrices();
                    foreach($prices as $price) {
                        if (isset($price['default_value']) && $price['default_value'] == 1) {
                            $defaultValues[$attribute->getAttributeId()] = $price['value_index'];
                        }
                    }
                }

                if(!empty($defaultValues)) {
                    $this->_prepareAndRender($controller, $productId, $defaultValues);
                }

                return;
            }

            while (count($parentIds) > 0) {
                $parentId = array_shift($parentIds);
                /* @var $parentProduct Mage_Catalog_Model_Product */
                $parentProduct = Mage::getModel('catalog/product');
                $parentProduct->load($parentId);
                if (!$parentProduct->getId()) {
                    throw new Exception(sprintf('Can not load parent product with ID %d', $parentId));
                }

                if ($parentProduct->isVisibleInCatalog()) {
                    break;
                }
                // try to find other products if one parent product is not visible -> loop
            }

            if (isset($parentProduct) && !$parentProduct->isVisibleInCatalog()) {
                Mage::log(sprintf('Not enabled parent for product id %d found.', $productId), Zend_Log::WARN);
                return;
            }

            if (!empty($parentIds)) {
                Mage::log(sprintf('Product with id %d has more than one enabled parent. Choosing first.', $productId), Zend_Log::NOTICE);
            } /* extend ! */


                /* @var $currentProduct Mage_Catalog_Model_Product */
            $currentProduct = Mage::getModel('catalog/product');
            $currentProduct->load($productId);

			if(isset($parentId)) {
	            $this->_prepareAndRender($controller, $parentId, $this->generateConfigData($parentProduct, $currentProduct));
            } else {
	            $this->_prepareAndRender($controller, $productId, false);
            }

        }

        return true;
    }

    public function updateConfigurable($observer){

        $confProduct = $observer->getData('product');

        $simpleProductId = Mage::registry('simple_product_id');

        if (!empty($simpleProductId)){

            $simpleProduct = Mage::getModel('catalog/product')->load($simpleProductId);

            if (Mage::getStoreConfig('firebear_configurl/general/short_description_enabled')){
                $confProduct->setShortDescription($simpleProduct->getShortDescription());
            }

            if (Mage::getStoreConfig('firebear_configurl/general/description_enabled')){
                $confProduct->setDescription($simpleProduct->getDescription());
            }

            if (Mage::getStoreConfig('firebear_configurl/general/title_enabled')){
                $confProduct->setName($simpleProduct->getName());
            }

            $confProduct->setPrice($simpleProduct->getPrice());
            $confProduct->setSpecialPrice($simpleProduct->getSpecialPrice());
            $confProduct->setFinalPrice($simpleProduct->getFinalPrice());
        }

        return true;
    }

}