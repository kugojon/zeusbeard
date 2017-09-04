<?php
class Gsx_Globalshopex_InvoiceController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();          
		$templateFile='globalshopex/contentInvoice.phtml';
		

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