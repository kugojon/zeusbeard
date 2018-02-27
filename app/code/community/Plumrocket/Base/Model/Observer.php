<?php 

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package    Plumrocket_Base-v1.x.x
@copyright  Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
@license    http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/


class Plumrocket_Base_Model_Observer{protected $_customer=null;protected $_inStock=true;protected $_session=null;public function systemConfigLoad($observer){$controller=$observer->getEvent()->getControllerAction();$section=$this->_getSection($controller);if($section && $section->getName()){try{$resourceLookup='admin/system/config/'.$section->getName();$this->_getSession()->getData('acl')->get($resourceLookup);}catch(Zend_Acl_Exception $e){$this->_refreshAdminAcl();$section=$this->_getSection($controller);}}if($this->_isPlumSection($section)){Mage::getSingleton('plumbase/cronChecker')->check($section->getName());}if($this->_hasS($section)){$current=$section->getName();$_key=$current.'/general/'.strrev('laires');$product=Mage::getModel('plumbase/product')->setPref($current);if(!Mage::getStoreConfig($_key,0)){if($s=$product->loadSession()){$config=Mage::getConfig();$config->saveConfig($_key,$s,'default',0);$config->reinit();Mage::app()->reinitStores();$this->_refreshPage();return;}}else{$product=Mage::getModel('plumbase/product')->load($product->getName());if(!$product->isInStock()||!$product->isCached()){$product->checkStatus();}}if(!$product->isInStock()){$product->disable();}if(!$product->isInStock()){Mage::getSingleton('adminhtml/session')->addError($product->getDescription());}}}public function permissionsCheck($observer){if($this->_getSession()->isLoggedIn()){$controller=$observer->getEvent()->getControllerAction();$request=$controller->getRequest();if($request->getActionName()=='denied'&&!$request->getParam('norefreshpage')){$this->_refreshAdminAcl();$this->_refreshPage(true);return;}}}protected function _getSession(){if(is_null($this->_session)){$this->_session=Mage::getSingleton('admin/session');}return $this->_session;}protected function _refreshPage($addParam=false){$cUrl=Mage::helper('core/url')->getCurrentUrl();if($addParam){$cUrl.=(strpos($cUrl,'?')===false)?'?':'&';$cUrl.='norefreshpage=1';}$c = Mage::app()->getFrontController(); $c->getResponse()->setRedirect($cUrl); $c->getResponse()->sendResponse(); $c->getRequest()->setDispatched(true); $c->setFlag('', Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH, true);}protected function _refreshAdminAcl(){$session=$this->_getSession();if($admin=$session->getUser()){$admin->setReloadAclFlag(true);$session->refreshAcl();}}protected function _getSection($controller){$req=$controller->getRequest();$current=$req->getParam('section');$website=$req->getParam('website');$store=$req->getParam('store');Mage::getSingleton('adminhtml/config_data')->setSection($current)->setWebsite($website)->setStore($store);$configFields=Mage::getSingleton('adminhtml/config');$sections=$configFields->getSections($current);if(!$current){$sections=(array) $sections;usort($sections,array($this,'_sort'));$permissions=$this->_getSession();foreach ($sections as $sec){$code=$sec->getName();if(!$code or trim($code)==""){continue;}if($permissions->isAllowed('system/config/'.$code)){$current=$code;$section=$sec;break;}}}else{$section=$sections->$current;}return $section;}public function customer(){if(empty($this->_customer)){$this->_customer=1;}return 'customer';}public function systemConfigBeforeSave($observer){$controller=$observer->getEvent()->getControllerAction();$section=$controller->getRequest()->getParam('section');if(!$section){return;}$sData=Mage::getSingleton('adminhtml/config')->getSection($section);if($this->_hasS($sData)){$product=Mage::getModel('plumbase/product')->loadByPref($section);$this->_inStock=$product->isInStock();}}public function systemConfigSave($observer){$controller=$observer->getEvent()->getControllerAction();$section=$controller->getRequest()->getParam('section');if(!$section){return;}$sData=Mage::getSingleton('adminhtml/config')->getSection($section);if($this->_hasS($sData)){$product=Mage::getModel('plumbase/product')->loadByPref($section);$product->checkStatus();if(!$product->isInStock()){$product->disable();}else{if(!$this->_inStock){Mage::getSingleton('adminhtml/session')->addSuccess($product->getDescription());}}}}protected function _hasS($section){$i='ser'.strrev('lai');return $section&&($v=$section->groups)&&($v=$v->general)&&($v=$v->fields)&&($v=$v->$i)&&((string) $section->tab=='plum'."rock".'et');}protected function _isPlumSection($section){return $section&&((string) $section->tab=='plu'.'mroc'.'ket');}protected function _sort($a,$b){return (int) $a->sort_order<(int) $b->sort_order?-1:((int) $a->sort_order>(int) $b->sort_order?1:0);}}