<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Icons_Compare
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fa-signal', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-signal')),
            array('value'=>'fa-compress', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-compress')),
            array('value'=>'fa-exchange', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-exchange')),
            array('value'=>'fa-arrows-alt', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-arrows-alt')),
            array('value'=>'fa-bar-chart-o', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-bar-chart-o')),
            array('value'=>'fa-random', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('fa-random'))
        );
    }

}