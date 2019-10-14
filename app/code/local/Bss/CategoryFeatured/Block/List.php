<?php
class Bss_CategoryFeatured_Block_List extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface {
    protected $_productCollection;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('categoryfeatured/products-list.phtml');
    }
    /**
     * @return mixed
     */
    protected function _getAllProducts() {
        $limits = $this->getData('num_products');
        if(!$this->getData('num_products')) {
            $limits = 6;
        }
        $categoryId = explode("/", $this->getData('categoryid'));
        $category = Mage::getModel('catalog/category')->load($categoryId[1]);
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addCategoryFilter($category)
            ->addAttributeToFilter('status', 1)
            ->addAttributeToSelect('*')
            ->setPageSize($limits)
            ->setCurPage(1);
        return $productCollection;
    }
}