<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Collateral
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'collateral_list', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Simple List')),
            array('value'=>'collateral_tabs', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Tabs')),
			array('value'=>'collateral_tabs_vertical', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Vertical Tabs')),
            array('value'=>'collateral_accordion', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Accordion'))          
        );
    }

}