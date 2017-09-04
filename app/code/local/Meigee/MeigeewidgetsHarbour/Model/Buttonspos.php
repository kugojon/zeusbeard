<?php class Meigee_MeigeewidgetsHarbour_Model_Buttonspos
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>Mage::helper('meigeewidgetsharbour')->__('Top')),
            array('value'=>'1', 'label'=>Mage::helper('meigeewidgetsharbour')->__('Bottom'))
        );
    }

}