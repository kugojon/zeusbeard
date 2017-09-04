<?php

class Meigee_ThemeOptionsHarbour_Block_Adminhtml_Restore_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'themeoptionsharbour';
        $this->_controller = 'adminhtml_restore';
         
        $this->_updateButton('save', 'label', Mage::helper('ThemeOptionsHarbour')->__('Restore'));
    }
 
    public function getHeaderText()
    {
        return Mage::helper('ThemeOptionsHarbour')->__('Theme Settings Restore');
    }


    


}