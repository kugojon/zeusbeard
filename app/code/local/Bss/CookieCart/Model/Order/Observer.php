<?php
class Bss_CookieCart_Model_Order_Observer
{
    public function addcartNew($observer){
		$domain = md5($_SERVER['HTTP_HOST']);
 		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
    		unset($_COOKIE['cookiecart_' . $domain]);
			setcookie('cookiecart_' . $domain, 'bss_cookiecart', time() + (86400 * 60), '/');
    	}
    	else{
	    	$quote = Mage::getSingleton('checkout/session')->getQuote();
			$items = $quote->getAllVisibleItems();
			$isProductInCart = false;
			foreach($items as $_item) {
			    if($_item->getProductId()){
			        $isProductInCart = true;
			        break;
			    }
			}
			$cart = Mage::getSingleton('checkout/cart'); 
			$cart->init();
			$productRemove = '';
			$flag = 0;
	        if(($_COOKIE['cookiecart_' . $domain] != 'bss_cookiecart') && !$isProductInCart) {

	        	$data_unserialize = unserialize($_COOKIE['cookiecart_' . $domain]);	
	        	foreach ($data_unserialize as $data) {
	        		$product = Mage::getModel('catalog/product')->load($data['product']);
					if(!$product){
						$flag = 1;
						continue;
					}
	        		$qty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();					
					
					if($product->getTypeId() == 'simple'){
						if ($product->getStockItem()->getIsInStock() && $qty > $data['qty']) {
							$cart->addProduct($data['product'], $data['qty']);
						}
						else{
							$flag = 1;
						}
					}
					if($product->getTypeId() == 'configurable'){
						$child = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($data['super_attribute'], $product);

						$childProduct = Mage::getModel('cataloginventory/stock_item')->loadByProduct($child);
						$childProductQty = (int)$childProduct->getQty();
						if($child->getStockItem()->getIsInStock() && $childProductQty > $data['qty']) {
							$cart->addProduct($product, $data);
						}
						else{
							$flag = 1;
						}					
					}
					if($product->getTypeId() == 'bundle'){
						
						$cart->addProduct($product, $data);
					}	
				}		
				Mage::getSingleton('customer/session')->setCartWasUpdated(true); 
				$cart->save(); 	
				if($flag == 1){
					echo '<script>window.onload = function () { alert("There has been a change to one or more items in your cart  ") }</script>';	
		    	}
	        }  
	    }
    }

    public function saveCart($observer){
    	if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
	    	$quote = Mage::getSingleton('checkout/session')->getQuote();
			$items = $quote->getAllVisibleItems();
			$isProductInCart = false;
			foreach($items as $_item) {
			    if($_item->getProductId()){
			        $isProductInCart = true;
			        break;
			    }
			}
			
			$domain = md5($_SERVER['HTTP_HOST']);
			if($isProductInCart){
		    	$data_serialize = '';
				$cart = Mage::getModel('checkout/cart')->getQuote()->getAllVisibleItems();
				$data = array();				
				foreach ($cart as $item) {
				    $productId = $item->getProductId();
				    $productQty = $item->getQty();
				 	$product = Mage::getModel('catalog/product')->load($productId); 
				 	
				 	if($product->getTypeId() == 'simple'){
						$info_buyRequest = array('product'=> $productId, "qty"=> $productQty);
					}
					if($product->getTypeId() == 'configurable'){
						$info = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
						$info_buyRequest = $info['info_buyRequest'];
						$info_buyRequest['qty'] = $productQty;
						unset($info_buyRequest['uenc']);
					    unset($info_buyRequest['form_key']);
					}
					if($product->getTypeId() == "bundle"){
						$info = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
						$info_buyRequest = $info['info_buyRequest'];
						$info_buyRequest['qty'] = $productQty;
					}
					
				    $data[] = $info_buyRequest;
				}
				$data_serialize = serialize($data);	
				setcookie("cookiecart_" . $domain, $data_serialize, time() + (86400 * 60), "/");
				
	    	}
			else{
				unset($_COOKIE['cookiecart_' . $domain]);
				setcookie('cookiecart_' . $domain, 'bss_cookiecart', time() + (86400 * 60), '/');
			}
		}
    }

    public function removeItemCookie($observer){
    	$domain = md5($_SERVER['HTTP_HOST']);
    	unset($_COOKIE['cookiecart_' . $domain]);
		setcookie('cookiecart_' . $domain, 'bss_cookiecart', time() + (86400 * 60), '/');
    }
}