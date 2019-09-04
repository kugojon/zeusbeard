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
class Ced_Jet_Block_Adminhtml_Profile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * 
     * @return void
     * */
    public function __construct()
    {
        
                 
        $this->_objectId = 'prfile_id';
        $this->_blockGroup = 'jet';
        $this->_controller = 'adminhtml_profile';
        
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('jet')->__('Save Profile'));
        $this->_removeButton('delete');
        
        $this->_addButton(
            'saveandcontinue', array(
            'label'     => Mage::helper('csgroup')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl().'\')',
            'class'     => 'save',
            ), -100
        );

        $this->_formScripts[] = "
			function saveAndContinueEdit(urlTemplate) {
				var url = urlTemplate.replace('{{tab_id}}',vendor_group_tabsJsTabs.activeTab.id);
				editForm.submit(url);
			}
        ";
    }
    
    /**
     * @return string
     * 
     * */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null,
            'id' => $this->getRequest()->getParam('id', false),
            'section'=>'ced_jet',
            'website' => $this->getRequest()->getParam('website', false),
            )
        );
    }

    /**
     * 
     * getting header text
     * @see Mage_Adminhtml_Block_Widget_Container::getHeaderText()
     */
    public function getHeaderText()
    {
        if(Mage::registry('profile_data') && Mage::registry('profile_data')->getId()) {
            return Mage::helper('jet')->__('Edit Profile "%s" ', $this->escapeHtml(Mage::registry('profile_data')->getName()));
        } else {
            return Mage::helper('jet')->__('Add New Profile');
        }
    }
}