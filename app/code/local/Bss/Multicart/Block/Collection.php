<?php
class Bss_Multicart_Block_Collection extends Mage_Core_Block_Template
{
    public function getStoreId(){
        return Mage::app()->getStore()->getStoreId();    
    }
    public function getCateIds(){
        return explode(',', $this->_helper()->getCategory());
    }

    public function getTitleCate($category_id){
        return Mage::getModel('catalog/category')->load($category_id)->getName();
    }

    public function getCollectionPro($category_id){
        $products = Mage::getModel('catalog/category')->load($category_id)
        ->getProductCollection()
        ->addAttributeToSelect('*') // add all attributes - optional
        ->addAttributeToFilter('status', 1) // enabled
        ->addAttributeToFilter('visibility', 4) //visibility in catalog,search
        ->setOrder($this->_helper()->getSortbyAttribute(), $this->_helper()->getSortbyType()); 
        return $products;
    }

    public function _helper(){
        return Mage::helper("multicart");
    }

}