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
class Ced_Jet_Block_Adminhtml_Refund_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_removeButton('back');
        $this->addButton(
            'back', array(
            'label'   => $this->__('Back'),
            'onclick' => "setLocation('{$this->getUrl('*/*/refund')}')",
            'class'   => 'back'
            )
        );         
        $this->_objectId = 'id';
        $this->_blockGroup = 'jet';
        $this->_controller = 'adminhtml_refund';
        $this->_removeButton('delete'); 
        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', Mage::helper('jet')->__('Submit Refund'));
        
        if(Mage::registry('refund_data')->getId()){
            $this->_removeButton('save');
        }
        
    }
 
    public function getHeaderText()
    {
        if(Mage::registry('refund_data')&&Mage::registry('refund_data')->getId())
        {
            return 'Refund Fields'.$this->escapeHtml(
                Mage::registry('refund_data')->getTitle()
            ).'<br />';
        }

          $header = Mage::helper('jet')->__('Create New Refund');

        return $header;
    }
    
    
}
