<?php

class Ced_Jet_Block_Adminhtml_Profile_Edit_Tab_Categorymapping extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {

        parent::__construct();
        $this->setTemplate('ced/jet/profile/categorymapping.phtml');
    }

    public function getJetCategoryId()
    {
        $category_id = Mage::app()->getRequest()->getParam('id');
        $value = Mage::getModel('jet/jetcategory')
            ->getCollection()
            ->addFieldToFilter('magento_cat_id', $category_id)
            ->getFirstItem();
        $jet_mapped_id = $value->getData('jet_cate_id');
        $jet_mapped_id = ($jet_mapped_id == 0 ? '' : $jet_mapped_id);

        return $jet_mapped_id;
    }

    public function getFilteredJetCollection($level)
    {
        return  Mage::getModel('jet/catlist')
           ->getCollection()
           ->addFieldToFilter('level', $level)
           ->addFieldToFilter('status', array(array('null'=> true),array('eq'=>'1'),array('eq'=>'')));
    }
}