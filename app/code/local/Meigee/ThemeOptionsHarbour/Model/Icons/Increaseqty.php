<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Icons_Increaseqty
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fa-plus-square-o', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-plus-square-o')),
            array('value'=>'fa-angle-right', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-angle-right')),
            array('value'=>'fa-arrow-right', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-arrow-right')),
            array('value'=>'fa-caret-up', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-caret-up')),
            array('value'=>'fa-step-forward', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-step-forward')),
            array('value'=>'fa-angle-double-right', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-angle-double-right'))
        );
    }

}