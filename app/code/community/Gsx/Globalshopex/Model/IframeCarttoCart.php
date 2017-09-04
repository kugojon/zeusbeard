<?php 
class Gsx_Globalshopex_Model_IframeCarttoCart {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Iframe Integration')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Cart To Cart Integration')),
        );
    }
}