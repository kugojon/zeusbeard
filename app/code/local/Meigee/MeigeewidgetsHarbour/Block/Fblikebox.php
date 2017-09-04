<?php

class Meigee_MeigeewidgetsHarbour_Block_Fblikebox
extends Mage_Core_Block_Html_Link
implements Mage_Widget_Block_Interface
{
    protected function _construct() {
        parent::_construct();
    }
	protected function _toHtml() {
        return parent::_toHtml();  
    }

    public function getContentLikebox()
    {
		$fbcontent = 
        'data-width="300"'
        . 'data-height="' . $this->getData('height') . '"'
        . 'data-href="' . $this->getData('href') . '"'
        . 'data-colorscheme="' . $this->getData('colorscheme') . '"'
        . 'data-show-faces="' . $this->getData('faces') . '"'
        . 'data-header="' . $this->getData('header') . '"'
        . 'data-stream="' . $this->getData('stream') . '"'
        . 'data-show-border="' . $this->getData('border') . '"';
        return $fbcontent;
    }
}