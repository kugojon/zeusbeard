<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meigeeteam.com <nick@meigeeteam.com>
 * @copyright Copyright (C) 2010 - 2014 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Bgposition
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Left')),
            array('value'=>'1', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Right')),
			array('value'=>'2', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Center')),
			array('value'=>'3', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Fill with stretching'))
        );
    }

}