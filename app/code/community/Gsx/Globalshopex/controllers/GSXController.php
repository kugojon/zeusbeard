<?php
class Gsx_Globalshopex_GSXController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();          
		$templateFile='globalshopex/formiframe.phtml';

		$block = $this->getLayout()->createBlock(
			'globalshopex/internationallogic',
			'globalshopex.internationallogic',
			array('template' => $templateFile)
		);
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
		$this->getLayout()->getBlock('content')->append($block);
		$this->_initLayoutMessages('core/session'); 
		$this->renderLayout();

    }
}
?>