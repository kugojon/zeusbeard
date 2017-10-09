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
  
class Ced_Jet_Block_Cedcommerce_Store extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;
	protected $_cedCommerceStoreUrl;
	
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return '<div><div><div id="' . $element->getId() . '">
					<iframe src="'.$this->getCedCommerceStoreUrl().'" name="cedcommerce_store" id="cedcommerce_store" style="width:100%; height:1200px; border:0; margin:0; overflow:hidden" marginheight="0" marginwidth="0" noscroll></iframe>
				</div>
				<input type="hidden" class=" input-text" value="" name="dummy_test123" id="csmarketplace_extensions_groups_extensions" />
				</div>
				</div>
				';
    }
	
	/**
     * Retrieve feed url
     *
     * @return string
     */
    public function getCedCommerceStoreUrl()
    {
        if (is_null($this->_cedCommerceStoreUrl)) {
            $this->_cedCommerceStoreUrl = Mage::app()->getStore()->isCurrentlySecure() ? 'https://cedcommerce.com/store/' : 'http://cedcommerce.com/store/';
        }
        return $this->_cedCommerceStoreUrl;
    }
}
