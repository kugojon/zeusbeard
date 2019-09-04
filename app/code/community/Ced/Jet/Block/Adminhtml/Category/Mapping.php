<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_Jet
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
  class Ced_Jet_Block_Adminhtml_Category_Mapping extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ced/jet/mapping.phtml');
    }

    public function getJetCategoryId()
    {
      $category_id=Mage::app()->getRequest()->getParam('id');
      $value= Mage::getModel('jet/jetcategory')->getCollection()->addFieldToFilter('magento_cat_id', $category_id)->getFirstItem();
      $jet_mapped_id=$value->getData('jet_cate_id');
      $jet_mapped_id=($jet_mapped_id==0?'':$jet_mapped_id);

      return $jet_mapped_id;
    }

    public function getFilteredJetCollection($level)
    {
      return Mage::getModel('jet/catlist')->getCollection()->addFieldToFilter('level', $level)->addFieldToFilter('status', '1');
    }

  }
 