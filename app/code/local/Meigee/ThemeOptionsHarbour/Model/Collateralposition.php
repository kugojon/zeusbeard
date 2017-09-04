<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Collateralposition
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'details', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Product Details Col')),
            array('value'=>'underdetails', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Under Product Details Col'))          
        );
    }

}