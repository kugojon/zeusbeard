<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Headertype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Header 1')),
            array('value'=>1, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Header 2')),
			array('value'=>2, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Header 3')),
			array('value'=>3, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Header 4')),
			array('value'=>4, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Header 5'))
        );
    }

}