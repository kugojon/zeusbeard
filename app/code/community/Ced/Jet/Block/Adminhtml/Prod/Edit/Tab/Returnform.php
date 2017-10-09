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


class Ced_Jet_Block_Adminhtml_Prod_Edit_Tab_Returnform extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('jet_return',array('legend'=>Mage::helper('jet')->__('Return Exception')));
        if(Mage::registry('return_data'))
        {
          
        }else{
                $fieldset->setHeaderBar('<script type="text/javascript">function showreturndiv(f){
                          container=document.getElementById(f);
                         container.style.display = "block";
                          var tagNames = ["INPUT", "SELECT", "TEXTAREA" ,"BUTTON"];
                                for (var i = 0; i < tagNames.length; i++) {
                                  var elems = container.getElementsByTagName(tagNames[i]);
                                  for (var j = 0; j < elems.length; j++) {
                                    elems[j].disabled = false;
                                  }
                          }
                    }</script><script type="text/javascript">document.addEventListener("DOMContentLoaded", 
                                      function(event) {
                                        container=document.getElementById("jet_return");
                                        container.style.display = "none";
                                          var tagNames = ["INPUT", "SELECT", "TEXTAREA" ,"BUTTON"];
                                          for (var i = 0; i < tagNames.length; i++) {
                                            var elems = container.getElementsByTagName(tagNames[i]);
                                            for (var j = 0; j < elems.length; j++) {
                                              elems[j].disabled = true;
                                            }
                                          }
            });</script><button type="button" onclick="showreturndiv(\'jet_return\');">Add Exception</button>');
        }
        
      $fieldset->addField('time_to_return', 'text', array(
          'label'     => Mage::helper('jet')->__('Time to return'),
         
          'name'      => 'time_to_return',
          'note'      =>'The number of days after purchase a customer can return the item (Maximum value is 30).',
        ));
        $fieldset->addField('locations', 'text', array(
          'label'     => Mage::helper('jet')->__('Return Location Ids'),
          
          'name'      => 'locations',
          'note'      =>'This is a required field.',
        ));
        $locations = $form->getElement('locations');

        $locations->setRenderer(
            $this->getLayout()->createBlock('jet/adminhtml_exception_edit_renderer_locations')
        );

        $fieldset->addField('ship_methods', 'text', array(
          'label'     => Mage::helper('jet')->__('Return Shipping Methods'),
          'note'      =>'This is a required field.',
          'name'      => 'ship_methods',
        ));
        $ship_methods = $form->getElement('ship_methods');

        $ship_methods->setRenderer(
            $this->getLayout()->createBlock('jet/adminhtml_exception_edit_renderer_shipmethods')
        );

        
		
        if(Mage::registry('return_data'))
        {
          $form->setValues(Mage::registry('return_data'));
        }
        return parent::_prepareForm();
    }
}
