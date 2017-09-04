<?php
/**
 * @category    Ms
 * @package     Ms_AddressVerification
 * @license     https://markshust.com/eula/
 */

/**
 * Address verification "API" attribute source
 */
class Ms_AddressVerification_Model_Attribute_Source_Api
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const API_USPS = '1';
    const API_SMARTYSTREETS = '2';

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('catalog')->__('USPS'),
                    'value' => self::API_USPS
                ),
                array(
                    'label' => Mage::helper('catalog')->__('Smarty Streets'),
                    'value' => self::API_SMARTYSTREETS
                ),
            );
        }
        return $this->_options;
    }

    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
