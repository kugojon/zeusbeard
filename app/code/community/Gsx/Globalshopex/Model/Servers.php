<?php 
class Gsx_Globalshopex_Model_Servers {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Review')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Enable')),
        );
    }
}