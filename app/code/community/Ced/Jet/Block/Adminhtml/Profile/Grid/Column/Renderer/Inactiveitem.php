<?php
class Ced_Jet_Block_Adminhtml_Profile_Grid_Column_Renderer_Inactiveitem extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $profileId = $row->getId();
        $productIds = Mage::getModel('jet/profileproducts')->getProfileProducts($profileId);

        $products = Mage::getModel('catalog/product')->getCollection();
        if(count($productIds)>0) {
            $products->addFieldToFilter('entity_id', $productIds);
            $products->addAttributeToFilter('jet_product_status', array('nin' => array('available_for_purchase')));
            $products->addFieldToFilter('type_id', array('simple', 'configurable'))
                ->addAttributeToFilter('visibility', 4);
            $value = count($products);
        }else{
            $value = 0;
        }

        return '<span style="color: red;">' . $value . '</span>';

    }

}