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

class Ced_Jet_Block_Adminhtml_Prod_Edit_Tab_Shippingform extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('jet_shipping', array('legend'=>Mage::helper('jet')->__('Shipping Exception')));

        $current_product = Mage::registry('current_product');

    $loadData=Mage::getModel('jet/jetshippingexcep')->load($current_product->getSku(), 'sku');

        if(count($loadData->getData())>0)
        {
        }else{
                 $fieldset->setHeaderBar(
                     '<script type="text/javascript">function showshippingdiv(f){
                        container=document.getElementById(f);
                        container.style.display = "block";
                        var tagNames = ["INPUT", "SELECT", "TEXTAREA"];
                              for (var i = 0; i < tagNames.length; i++) {
                                var elems = container.getElementsByTagName(tagNames[i]);
                                for (var j = 0; j < elems.length; j++) {
                                  elems[j].disabled = false;
                                }
                        }
                  }</script><script type="text/javascript">document.addEventListener("DOMContentLoaded", 
                                    function(event) {
                                      container=document.getElementById("jet_shipping");
                                      container.style.display = "none";
                                        var tagNames = ["INPUT", "SELECT", "TEXTAREA"];
                                        for (var i = 0; i < tagNames.length; i++) {
                                          var elems = container.getElementsByTagName(tagNames[i]);
                                          for (var j = 0; j < elems.length; j++) {
                                            elems[j].disabled = true;
                                          }
                                        }
            });</script><button type="button" onclick="showshippingdiv(\'jet_shipping\');">Add Exception</button>'
                 );
        }

       

       /* $fieldset->addField('sku', 'text', array(
          'label'     => Mage::helper('jet')->__('Sku'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'sku',
        ));*/

        $fieldset->addField(
            'shipping_carrier', 'select', array(
            'label'     => Mage::helper('jet')->__('Shipping Level'),
            'name'      => 'shipping_carrier',
            'values'    => Mage::helper('jet')->shippingCarrier(),
            'note'    => 'For removing shipping exception for this product do not select any option in this and save product', 
            )
        );
        
        /*
        $fieldset->addField('shipping_method', 'select', array(
          'label'     => Mage::helper('jet')->__('Shipping Method'),
          'name'      => 'shipping_method',
          'values'    => Mage::getSingleton('jet/shippingexception')->shippingMethod(),
        ));
		*/
        
        $fieldset->addField(
            'shipping_method', 'text', array(
            'label'     => Mage::helper('jet')->__('Shipping Method'),
            'name'      => 'shipping_method',
              'note'    => 'A specific shipping method e.g. UPS Ground, UPS Next Day Air, FedEx Home, Freight',             
            )
        );

        $fieldset->addField(
            'shipping_override', 'select', array(
            'label'     => Mage::helper('jet')->__('Override Type'),
          
            'note'      =>'This is a required field.',
            'name'      => 'shipping_override',
            'values'    => Mage::helper('jet')->shippingOverride(),
            )
        );

        $fieldset->addField(
            'shipping_charge', 'text', array(
            'label'     => Mage::helper('jet')->__('Shipping Charge Amount'),
            'note'      =>'This is a required field.',
            'name'      => 'shipping_charge',
            )
        );

        $fieldset->addField(
            'shipping_excep', 'select', array(
            'label'     => Mage::helper('jet')->__('Shipping Exception'),
            'name'      => 'shipping_excep',
          
            'note'      =>'This is a required field.',
            'values'    => Mage::helper('jet')->shippingExcep(),
            )
        );

 
        if($loadData)
        {
          $form->setValues($loadData->getData());
        }

        return parent::_prepareForm();
    }
}
