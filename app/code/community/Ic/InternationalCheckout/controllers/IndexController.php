<?php
class Ic_InternationalCheckout_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
     $this->loadLayout(array('default'));
     $this->renderLayout();
	 //var_dump(Mage::getSingleton('core/layout')->getUpdate()->getHandles());
	 //exit("bailing early at ".__LINE__." in ".__FILE__);
    }
}
?>