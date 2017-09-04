<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Presets
{
    public function toOptionArray()
    {
        return array(
            array('value'=>99, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Manual configuration')),
            array('value'=>1, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Restore Defaults')),
            array('value'=>2, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Default store layout')),
			array('value'=>3, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Store with boxed layout')),
			array('value'=>4, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Store with image in header')),
			array('value'=>5, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Store with Massive header')),
			array('value'=>6, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Store with parallax effect')),
			array('value'=>7, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Store with white header')),
			array('value'=>8, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Store with white massive header'))
        );
    }

}