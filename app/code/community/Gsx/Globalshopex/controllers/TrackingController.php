<?php
class Gsx_Globalshopex_TrackingController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();          
		$templateFile='globalshopex/contentTracking.phtml';

		$block = $this->getLayout()->createBlock(
			'internationalcheckout/international',
			'internationalcheckout.international',
			array('template' => $templateFile)
		);
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
		$this->getLayout()->getBlock('content')->append($block);
		$this->_initLayoutMessages('core/session'); 
		$this->renderLayout();

    }
}
?>