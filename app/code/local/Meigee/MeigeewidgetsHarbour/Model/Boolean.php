<?php class Meigee_MeigeewidgetsHarbour_Model_Boolean
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'1', 'label'=>Mage::helper('meigeewidgetsharbour')->__('True')),
            array('value'=>'0', 'label'=>Mage::helper('meigeewidgetsharbour')->__('False'))
        );
    }

}