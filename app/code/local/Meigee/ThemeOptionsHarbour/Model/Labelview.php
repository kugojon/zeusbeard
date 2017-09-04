<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Labelview
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'label-type-1', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('')),
            array('value'=>'label-type-2', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('')),
			array('value'=>'label-type-4', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('')),
			array('value'=>'label-type-5', 'label'=>Mage::helper('ThemeOptionsHarbour')->__(''))
        );
    }

}