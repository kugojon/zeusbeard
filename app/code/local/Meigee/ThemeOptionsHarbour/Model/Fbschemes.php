<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meigeeteam.com <nick@meigeeteam.com>
 * @copyright Copyright (C) 2010 - 2014 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Fbschemes
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'light', 'label'=>Mage::helper('meigeewidgetsharbour')->__('Light')),
            array('value'=>'dark', 'label'=>Mage::helper('meigeewidgetsharbour')->__('Dark')),
        );
    }

}