<?php
class Bss_PreSelectShippingPaypal_Model_Observer
{
	public function predispatchPaypalExpressReview($observer)
    {
        $address = Mage::getModel('checkout/cart')->getQuote()->getShippingAddress();
        if($address->getShippingMethod() != '') return;

        $groups = $address->getGroupedAllShippingRates();

        $shipping_price = array();
        if ($groups && $address) {
            foreach ($groups as $code => $rates) {
                foreach ($rates as $rate) {
                	// if($rate->getCode() == 'freeshipping_freeshipping') continue;
                    if($rate->getMethodTitle() == 'Free Shipping' || $rate->getMethodTitle() == 'Standard Shipping') {
                    	$shipping_price[$rate->getCode()] = $rate->getPrice();
                    }
                }
            }
        }
        if(count($shipping_price) > 0) {
	        $method = array_search(min($shipping_price),$shipping_price);
	        if($method != '') {
	            $address->setShippingMethod($method)->setCollectShippingRates(true)->save();
	        }
        }
    }
}
