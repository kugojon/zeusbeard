<?php class Meigee_MeigeewidgetsHarbour_Model_Fbschemes
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'light', 'label'=>Mage::helper('meigeewidgetsharbour')->__('Light')),
            array('value'=>'dark', 'label'=>Mage::helper('meigeewidgetsharbour')->__('Dark')),
        );
    }

}