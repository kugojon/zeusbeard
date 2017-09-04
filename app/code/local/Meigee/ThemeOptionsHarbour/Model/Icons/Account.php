<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Icons_Account
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fa-user', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-user')),
            array('value'=>'fa-check-square-o', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-check-square-o')),
            array('value'=>'fa-info', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-info')),
            array('value'=>'fa-smile-o', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-smile-o')),
            array('value'=>'fa-male', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-male')),
            array('value'=>'fa-home', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-home'))  
        );
    }

}