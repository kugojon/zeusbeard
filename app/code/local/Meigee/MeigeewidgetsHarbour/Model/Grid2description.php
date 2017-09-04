<?php class Meigee_MeigeewidgetsHarbour_Model_Grid2description
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>Mage::helper('meigeewidgetsharbour')->__('Hide product description of all products')),
            array('value'=>'1', 'label'=>Mage::helper('meigeewidgetsharbour')->__('Show product description of all products')),
            array('value'=>'2', 'label'=>Mage::helper('meigeewidgetsharbour')->__('Show product description of symmetric products')),
			array('value'=>'3', 'label'=>Mage::helper('meigeewidgetsharbour')->__('Show products description of odd-numbered products'))
        );
    }

}