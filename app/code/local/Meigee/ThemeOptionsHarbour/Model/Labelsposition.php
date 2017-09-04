<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_labelsposition
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'top', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Top')),
            array('value'=>'bottom', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Bottom'))
        );
    }

}