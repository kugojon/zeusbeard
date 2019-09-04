<?php 

/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsGroup
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_Jet_Block_Adminhtml_Profile_Edit_Tab_Info extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * 
     * Preparing form
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
     */
    protected function _prepareForm()
    {     
        $form = new Varien_Data_Form();         
        $data = Mage::registry('current_profile');
        
        $fieldset = $form->addFieldset('profile_info', array('legend'=>Mage::helper('jet')->__('Profile Information')));
    
        $fieldset->addField(
            'profile_code', 'text',
            array(
                'name'      => "profile_code",
                'label'     => Mage::helper('jet')->__('Profile Code'),
                'note'      => Mage::helper('eav')->__('For internal use. Must be unique with no spaces. Profile code must start with small letters.'),
                'class'     => 'validate-code',
                'required'  => true,
                'value'     => $data->getData('profile_code'),
            )
        );

        $fieldset->addField(
            'profile_name', 'text',
            array(
                'name'      => "profile_name",
                'label'     => Mage::helper('jet')->__('Profile Name'),
                'class'     => '',
                'required'  => true,
                'value'    => $data->getData('profile_name'),
                'note'      => Mage::helper('jet')->__('Give some name to profile to identify them'),

            )
        );

       /* $fieldset->addField('store_id', 'select', array(
            'name' => 'store_id',
            'value'  => $data->getData('store_id'),
            'label' => Mage::helper('jet')->__('Store View'),
            'title' => Mage::helper('jet')->__('Store View'),
            'required' => true,
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            'note'  	=> Mage::helper('jet')->__('Specific store view information of products will send to jet'),
        ));*/


        $statuses = array('1' => 'Active', '0' => 'Inactive');

        $fieldset->addField(
            'profile_status', 'select', array(
            'name' => 'profile_status',
            'value'  => $data->getData('profile_status'),
            'label' => Mage::helper('jet')->__('Status'),
            'title' => Mage::helper('jet')->__('Status'),
            'required' => true,
            'values' =>  $statuses,
            'note'      => Mage::helper('jet')->__('Make active to enable the profile'),
            )
        );



        
        $fieldset->addField(
            'in_profile_product', 'hidden',
            array(
                'name'  => 'in_profile_product',
                'id'    => 'in_profile_productz',
            )
        );

        $fieldset->addField('in_profile_product_old', 'hidden', array('name' => 'in_profile_product_old'));
        
         if ($data->getId()) {
            $form->getElement('profile_code')->setDisabled(1);
         }

        //print_r($data->getData('in_group_vendor_old'));die;
        //$form->setValues($data->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}