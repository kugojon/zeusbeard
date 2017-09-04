<?php class Meigee_MeigeewidgetsHarbour_Model_Staticblocks
{
    public function toOptionArray()
    {
        return Mage::getModel('cms/block')->getCollection()->toOptionArray();
    }

}