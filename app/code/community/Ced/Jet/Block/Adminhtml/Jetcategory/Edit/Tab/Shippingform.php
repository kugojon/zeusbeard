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

class Ced_Jet_Block_Adminhtml_Jetcategory_Edit_Tab_Shippingform extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('jet_shipping',array('legend'=>Mage::helper('jet')->__('Shipping Exception')));
        

        $fieldset->addField('sku', 'text', array(
          'label'     => Mage::helper('jet')->__('Sku'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'sku',
        ));

        $fieldset->addField('shipping_carrier', 'select', array(
          'label'     => Mage::helper('jet')->__('Shipping Carrier'),
          'name'      => 'shipping_carrier',
          'values'    => Mage::getSingleton('jet/shippingexception')->shippingCarrier(),
        ));

        $fieldset->addField('shipping_method', 'select', array(
          'label'     => Mage::helper('jet')->__('Shipping Method'),
          'name'      => 'shipping_method',
          'values'    => Mage::getSingleton('jet/shippingexception')->shippingMethod(),
        ));

        $fieldset->addField('shipping_override', 'select', array(
          'label'     => Mage::helper('jet')->__('Override Type'),
          'class'     => 'required-entry validate-select',
          'required'  => true,
          'name'      => 'shipping_override',
          'values'    => Mage::getSingleton('jet/shippingexception')->shippingOverride(),
        ));

        $fieldset->addField('shipping_charge', 'text', array(
          'label'     => Mage::helper('jet')->__('Shipping Charge Amount'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'shipping_charge',
        ));

        $fieldset->addField('shipping_excep', 'select', array(
          'label'     => Mage::helper('jet')->__('Shipping Exception'),
          'name'      => 'shipping_excep',
          'class'     => 'required-entry validate-select',
          'required'  => true,
          'values'    => Mage::getSingleton('jet/shippingexception')->shippingExcep(),
        ));
        if(Mage::registry('shipping_data'))
        {
          $form->setValues(Mage::registry('shipping_data')->getData());
        }
        return parent::_prepareForm();
    }
}
