<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Imgformat
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'.png', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('.png')),
            array('value'=>'.jpg', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('.jpg')),
            array('value'=>'.gif', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('.gif'))          
        );
    }

}