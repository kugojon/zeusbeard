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

class Ced_Jet_Model_System_Config_Adminhtml_Serialized_Identifier extends Mage_Core_Model_Config_Data
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

        $identifiers = array('UPC' => 'UPC', 'EAN' => 'EAN', 'ASIN' => 'ASIN',
            'ISBN-13' => 'ISBN-13', 'ISBN-10' => 'ISBN-10', 'GTIN-14' => 'GTIN-14');

        $values =array();
        $i = 0;
        foreach ($identifiers as  $code => $value) {
            $magetoAttrCode = '';
            foreach($arr as $val){
                if(isset($val['identifier']) && $val['identifier'] == $code){
                    $magetoAttrCode = $val['magento_attribute_code'];
                    break;
                }
            }

            $values["standard_".$i++] = array('magento_attribute_code' =>$magetoAttrCode, 'identifier'=>$code);

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