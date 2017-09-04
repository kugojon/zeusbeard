<?php class Meigee_MeigeewidgetsHarbour_Model_Imagesformat
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'.png', 'label'=>Mage::helper('meigeewidgetsharbour')->__('.png')),
            array('value'=>'.jpg', 'label'=>Mage::helper('meigeewidgetsharbour')->__('.jpg')),
            array('value'=>'.gif', 'label'=>Mage::helper('meigeewidgetsharbour')->__('.gif'))
        );
    }

}