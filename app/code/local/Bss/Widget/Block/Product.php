<?php
class Bss_Widget_Block_Product extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bss_widget/template.phtml');
    }
};