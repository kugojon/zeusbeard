<?php  
class Bss_Multicart_IndexController extends Mage_Core_Controller_Front_Action{

  public function indexAction()
  {
    $helper = Mage::helper('multicart');
    if($helper->isActive()){
      $this->loadLayout(array('default'));
      $this->renderLayout();  
    }else{
      $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
      $this->getResponse()->setHeader('Status','404 File not found');
      $this->_forward('defaultNoRoute');
    }
  }

  public function addmultiplecartAction()
  {
    $productIds = $this->getRequest()->getParam('products');

    if (!is_array($productIds)) {
      $this->_redirect('*/*/');
    }
    $cart = Mage::getSingleton('checkout/cart');
    foreach($productIds as $productId) {
          try {
              $qty = $this->getRequest()->getPost('qty_' . $productId, 0);
              if ($qty <= 0) continue; // nothing to add
              $product = Mage::getModel('catalog/product')
                  ->setStoreId(Mage::app()->getStore()->getId())
                  ->load($productId);
              $params = array(
                  'product' => $productId,
                  'bundle_option' => $this->getRequest()->getPost('bundle_option_' . $productId),
                  'bundle_option_qty'=>$this->getRequest()->getPost('bundle_option_qty_' . $productId),
                  'super_attribute' => $this->getRequest()->getPost('super_attribute_' . $productId),
                  'options' => $this->getRequest()->getPost('options'),
                  'links' => $this->getRequest()->getPost('links'),
                  'qty' => $qty
              );    
              $eventArgs = array(
                  'product' => $product,
                  'qty' => $qty,
                  'request' => $this->getRequest(),
                  'response' => $this->getResponse(),
              );

              $cart->addProduct($product, $params);
              Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

              Mage::dispatchEvent('checkout_cart_add_product_complete',$eventArgs);

              $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());    
              Mage::getSingleton('core/session')->addSuccess($message);
          }
          catch (Mage_Core_Exception $e) {
              if (Mage::getSingleton('core/session')->getUseNotice(true)) {
                  Mage::getSingleton('core/session')->addNotice($product->getName() . ': ' . $e->getMessage());
              }
              else {
                  Mage::getSingleton('core/session')->addError($product->getName() . ': ' . $e->getMessage());
              }
          }
          catch (Exception $e) {
              Mage::getSingleton('core/session')->addException($e, $this->__('Can not add item to shopping cart'));
          }

      }
      $cart->save();
      $this->_redirect('*/*/');
} 

}