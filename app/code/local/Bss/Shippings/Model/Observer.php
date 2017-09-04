<?php
class Bss_Shippings_Model_Observer
{

			public function saveShipping(Varien_Event_Observer $observer)
			{ 	
				$session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
				$request = $observer->getEvent()->getRequest();
		            $quote =  $observer->getEvent()->getQuote();
		            $carrier = $request->getPost('shipping_arrival_carrier');
	            // if (isset($carrier) && !empty($carrier)){
	                $quote->setShippingArrivalCarrier($request->getPost('shipping_arrival_carrier'));
	                $quote->setShippingArrivalAccountNumber($request->getPost('shipping_arrival_account_number'));
	                $quote->save();
	                $session->setData("shipping_arrival_carrier", $request->getPost('shipping_arrival_carrier'));
	                $session->setData("shipping_arrival_account_number", $request->getPost('shipping_arrival_account_number'));

	            // }
		        return $this;
			}
		
}
