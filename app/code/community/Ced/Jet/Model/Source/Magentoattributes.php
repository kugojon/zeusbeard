<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_Jet
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

class Ced_Jet_Model_Source_Magentoattributes
{
	public function toOptionArray()
    {

            $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->getItems();
            $_options = array();
            foreach ($attributes as $attribute){
                    $_options[] =array(
                                    'label' => $attribute->getAttributeCode(),
                                    'value' => $attribute->getAttributeCode()
                                ) ;
            }
        return $_options;
    }
	
}