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
  
class Ced_Jet_Block_Adminhtml_Catalog_Product_Edit extends Mage_Adminhtml_Block_Catalog_Product_Edit
{
    /**
     * @var Mage_Catalog_Model_Product Product instance
     */
    private $_product;
 
    /**
     * Preparing global layout
     * 
     * @return Ced_Jet_Block_Adminhtml_Catalog_Product_Edit|Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {


        parent::_prepareLayout();
        $product = Mage::registry('current_product');
        $profileProduct = Mage::getModel('jet/profileproducts')->loadByField('product_id', $product->getId());
        Mage::register('profile_products', $profileProduct);


        $showProductEdit = Mage::getStoreConfig('jet_options/ced_jetproductedit/show_jetupload_on_productedit');
        if($profileProduct && $profileProduct->getId()>0 && $showProductEdit){
            $this->_product = $this->getProduct();
            $this->setChild('view_on_front',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Validate and Upload to Jet'),
                    'onclick'   =>  "setLocation('{$this->getUrl('adminhtml/adminhtml_jetedit/jetProductEdit/id/'.$product->getId().'')}')",

                    'title' => Mage::helper('catalog')->__('Validate and Upload to Jet')
                ))
            );
        }
        return $this;
    }
 
    /**
     * Returns duplicate & view on front buttons html
     * 
     * @return string
     */
    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml('duplicate_button') . $this->getChildHtml('view_on_front');
    }
 
    /**
     * Checking product visibility
     * 
     * @return bool
     */
    private function _isVisible()
    {
        return $this->_product->isVisibleInCatalog() && $this->_product->isVisibleInSiteVisibility();
    }
 
}
?>