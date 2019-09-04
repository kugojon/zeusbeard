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
        $this->_objectId = 'pcode';
        $this->_blockGroup = 'jet';
        $this->_controller = 'adminhtml_profile';
        parent::__construct();
        $this->_removeButton('save', 'label', Mage::helper('jet')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('jet')->__('Delete'));
        $this->_addButton(
            'saveandcontinue', array(
            'label'     => Mage::helper('jet')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl('edit').'\')',
            'class'     => 'save',
            ), -100
        );
        $this->_addButton(
            'saveandupload', array(
            'label'     => Mage::helper('jet')->__('Save and Upload Product'),
            'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl('upload').'\')',
            'class'     => 'save',
            ), -100
        );
        /*$this->_formScripts[] = "
      function saveAndContinueEdit(urlTemplate) {
        var url = urlTemplate.replace('{{tab_id}}',vendor_group_tabsJsTabs.activeTab.id);
        editForm.submit(url);
      }
        ";*/
    
         $this->_formScripts[] = "
           function saveAndContinueEdit(urlTemplate) {
           new Insertion.Bottom('edit_form',
           groupProductPpcode_massactionJsObject.fieldTemplate.evaluate(
           {
           name: 'in_profile_products', 
           value: groupProductPpcode_massactionJsObject.checkedString}));
                

        var url = urlTemplate.replace('{{tab_id}}',vendor_group_tabsJsTabs.activeTab.id);
        editForm.submit(url);
      }
        ";

    }
  
    /**
     * @return string
     * 
     * */
  public function getSaveAndContinueUrl($back)
  {
        $pcode = "";
        $profile = Mage::registry('current_profile');
        if($this->getRequest()->getParam('pcode', false))
            $pcode = $this->getRequest()->getParam('pcode', false);
        else if($profile->getId())
            $pcode = $profile->getProfileCode();
        return $this->getUrl(
            '*/*/save', array(
            '_current'   => true,
            'back'       => $back,
            'tab'        => '{{tab_id}}',
            'active_tab' => null,
            'pcode' => $pcode,
            'section'=>'jet_configuration',
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
        if(Mage::registry('current_profile') && Mage::registry('current_profile')->getId()){
            return Mage::helper('jet')->__('Edit Profile "%s" ', $this->escapeHtml(Mage::registry('current_profile')->getProfileName()));
        } else {
            return Mage::helper('jet')->__('Add Jet Profile');
        }
    }
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }
}