<?php
class Ced_Jet_Block_Adminhtml_Prod_Widget_Grid_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction
{

//    public function __construct()
//    {
//        /*parent::__construct();
//        $this->setTemplate('widget/grid/massaction.phtml');
//        $this->setErrorText(Mage::helper('catalog')->jsQuoteEscape(Mage::helper('catalog')->__('Please select items.')));
//        */
//        $this->getSelectedJson();
//    }
    public function getSelectedJson()
    {
        /*$gridIds = $this->getParentBlock()->getCollection()->getAllIds();
        if(!empty($gridIds)) {
            return join(",", $gridIds);
        }*/
//        print_r($this->_getProducts());die;
//        return join(",", [1,2,3]);
        return join(",", $this->_getProducts());
//        return '';
    }
    public function _getProducts($isJson=false)
    {
        if ($this->getRequest()->getParam('in_profile_product') != "") {
            return explode(",", $this->getRequest()->getParam('in_profile_product'));
        }


        $profileId = $this->getRequest()->getParam('id');

        $productIds  = Mage::getModel('jet/profileproducts')->getProfileProducts($profileId);



        /*$cond = "profile_id =" . $profileId;
        $products->joinField(
            'profile_id',
            'jet_profile_products',
            'profile_id',
            'product_id = entity_id',
            $cond);*/
        /*echo '<pre>';
        print_r($products->getData());die;*/

        if (sizeof($productIds) > 0) {
            $products = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToFilter('visibility', array('neq' => 1))
                ->addAttributeToFilter('type_id', array('simple','configurable'))
                ->addFieldToFilter('entity_id', $productIds);
            if ($isJson) {
                $jsonProducts = Array();
                foreach($products as $product)  {
                    $jsonProducts[$product->getEntityId()] = 0;
                }
                return Mage::helper('core')->jsonEncode((object)$jsonProducts);
            } else {
                $jsonProducts = Array();
                foreach($products as $product)  {
                    $jsonProducts[$product->getEntityId()] = $product->getEntityId();
                }
                return $jsonProducts;
            }
        } else {
            if ($isJson) {
                return '{}';
            } else {
                return array();
            }
        }
    }

}