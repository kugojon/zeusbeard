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


class Ced_Jet_Block_Adminhtml_Rejected_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
	{
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'jet';
        $this->_controller = 'adminhtml_rejected';
        $this->_mode = 'edit';

        $jFile = Mage::registry('errorfile_collection');
        if($jFile->getStatus() =='Processed with errors'){
            $this->_updateButton('save', 'label', Mage::helper('jet')->__('Resubmit File'));
        }else{
            $this->_removeButton('save');
        }


		$this->_removeButton('delete');
		$this->_removeButton('reset');
		$this->_removeButton('back');
        $data = array(
        'label' =>  'Back',
        'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/rejected') . '\')',
        'class'     =>  'back'
		);
		$this->addButton ('back', $data, 0, 100,  'header');     
 
           
    }
 
    public function getHeaderText()
    {
        if (Mage::registry('errorfile_collection') && Mage::registry('errorfile_collection')->getId())
        {
            return Mage::helper('jet')->__('Error Files Information');
        } else {
            return Mage::helper('jet')->__('Error Files Information');
        }
    }
}