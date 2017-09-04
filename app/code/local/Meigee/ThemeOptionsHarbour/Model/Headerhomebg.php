<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Headerhomebg
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Transparent header')),
            array('value'=>1, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Part of 1st banner')),
			array('value'=>2, 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Header image'))
        );
    }

}