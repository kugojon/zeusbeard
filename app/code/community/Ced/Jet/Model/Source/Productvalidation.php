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

class Ced_Jet_Model_Source_Productvalidation extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
  
    public function getAllOptions()
    {
    
        if (is_null($this->_options)) {
            $this->_options = array(

                array(
                    'label' => 'Valid',
                    'value' => 'valid'
                ),
                 array(
                    'label' =>'Invalid',
                    'value' =>'invalid'
                ),
                array(
                    'label' => 'Not Validated',
                    'value' =>'not_validated'
                ),

            );
        }

        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }

        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return false;
    }

   
}
