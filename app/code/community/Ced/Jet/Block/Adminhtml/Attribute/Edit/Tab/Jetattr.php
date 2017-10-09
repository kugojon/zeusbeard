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

class Ced_Jet_Block_Adminhtml_Attribute_Edit_Tab_Jetattr
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface 
{    
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('ced/jet/attribute/jetattr.phtml');
         
    }

    public function getTabLabel() {
        return $this->__('Jet Attribute');
    }

    public function getTabTitle() {
        return $this->__('Jet Attribute');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    public function getJetAttr(){
	
		$attribute_id = $this->getRequest()->getParam('attribute_id');
		$jetattribute = Mage::getModel('jet/jetattribute');
		$collection = $jetattribute->getCollection()->addFieldToFilter('magento_attr_id', $attribute_id);

		if(count($collection)){
			return $collection->getFirstItem();
		}
		return false;
			
    }

} 