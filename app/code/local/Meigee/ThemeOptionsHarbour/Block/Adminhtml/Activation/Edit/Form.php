<?php
/**
 * @version   1.0 12.0.2012
 * @author    Queldorei http://www.queldorei.com <mail@queldorei.com>
 * @copyright Copyright (C) 2010 - 2012 Queldorei
 */
class Meigee_ThemeOptionsHarbour_Block_Adminhtml_Activation_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $isElementDisabled = false;
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Activate Parameters')));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => $isElementDisabled
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => 0
            ));
        }

        $fieldset->addField('activate_theme', 'checkbox', array(
            'label' => Mage::helper('ThemeOptionsHarbour')->__('Activate Theme'),
            'note' => 'Select this option if you want activate the theme',
            'required' => false,
            'name' => 'activate_theme',
            'value' => 1
        ))->setIsChecked(0);

        $fieldset->addField('setup_pages', 'checkbox', array(
            'label' => Mage::helper('ThemeOptionsHarbour')->__('Import Cms Pages'),
            'note' => 'Create all pages used in the theme',
            'required' => false,
            'name' => 'setup_pages',
            'value' => 1
        ))->setIsChecked(1);

        $fieldset->addField('setup_blocks', 'checkbox', array(
            'label' => Mage::helper('ThemeOptionsHarbour')->__('Import Cms Blocks'),
            'note' => 'Create all blocks used in the theme',
            'required' => false,
            'name' => 'setup_blocks',
            'value' => 1
        ))->setIsChecked(1);

        $form->setAction($this->getUrl('*/*/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
