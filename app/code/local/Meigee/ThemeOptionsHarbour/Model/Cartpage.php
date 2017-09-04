<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Cartpage
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'cart_standard', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Default Cart')),
            array('value'=>'cart_accordion', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Accordion')),
			array('value'=>'cart_new_default', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('New Default Cart'))
        );
    }

}