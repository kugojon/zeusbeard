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

class Ced_Jet_Model_System_Config_Adminhtml_Serialized_Variantattribute extends Mage_Core_Model_Config_Data
{
    /**
     * Process data after load
     */
    protected function _afterLoad()
    {

        $value = $this->getValue();
        $arr   = @unserialize($value);
        if(!is_array($arr)) $arr = array();
        /*if(!is_array($arr)) return '';

        // some cleanup
        foreach ($arr as $k => $val) {
            if(!is_array($val)) {
                unset($arr[$k]);
                continue;
            }
        }*/

        $systemAttribute = array('exclude_from_fee_adjust', 'jet_product_status', 'manufacturer', 'map_implementation',
            'product_tax_code', 'prop_65', 'ships_alone', 'newegg_condition', 'newegg_shipping', 'jet_productid_type',
            'jet_product_status');

        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFieldToFilter('is_configurable', 1)
            ->addFieldToFilter('is_global', 1)
            ->addFieldToFilter('attribute_code', array('nin' => $systemAttribute))
            ->addFieldToFilter('frontend_input', array('in' => array('select', 'boolean')));

        $values =array();
        foreach ($attributes as $attribute) {
            $jetAttribute = '';
            foreach($arr as $val){
                if(isset($val['magento_attribute_code']) && $val['magento_attribute_code'] == $attribute->getAttributeCode()){
                    $jetAttribute = $val['jet_attribute_id'];
                    break;
                }
            }

            $values[] = array('magento_attribute_code' =>$attribute->getAttributeCode(), 'jet_attribute_id'=>$jetAttribute);
        }



        $this->setValue($values);
    }
    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $values = $this->getValue();
        $value = serialize($values);
        $this->setValue($value);
    }



}