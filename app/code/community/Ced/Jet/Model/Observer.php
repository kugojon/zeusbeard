<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_Jet
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

class Ced_Jet_Model_Observer
{

    /**
     * Predispath admin action controller
     *
     * @param Varien_Event_Observer $observer
     */
    public function preDispatch(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $feedModel = Mage::getModel('jet/feed');
            $feedModel->checkUpdate();
        }
    }

        public function adminhtmlWidgetContainerHtmlBefore($event) {
            $block = $event->getBlock();

            if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
				$id=Mage::app()->getRequest()->getParam('order_id');
				$increment_id = Mage::getModel('sales/order')->load($id)->getIncrementId();

				$ifexist=count(Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('magento_order_id',$increment_id));

				if($ifexist){
					$block->removeButton('order_ship');   // Remove tab by id
					$block->removeButton('order_invoice');
					$block->removeButton('order_creditmemo');
				}
            }
        }

        public function shipbyjet($observer)
        {

        $conName = Mage::app()->getRequest()->getControllerName();       

         if(( $conName != 'auctane' ) && ($conName != '') ) {
             $shipment = $observer->getEvent()->getShipment();
             $flag = false;
             $magento_order_id = $shipment->getOrder()->getIncrementId();
             $magento_order_data = Mage::getModel('jet/jetorder')->getCollection()->getData(); 
             $ses_var = Mage::getSingleton('core/session')->getShip_by_jet();

             foreach ($magento_order_data as $key => $value) {
                    if($value['magento_order_id'] == $magento_order_id)$flag = true;
                }
                if($flag == true && $ses_var == true)Mage::getSingleton('core/session')->unsShip_by_jet();                
                elseif($flag == true && !$ses_var)
                {
                     Mage::getSingleton('core/session')->unsShip_by_jet();
                     Mage::getSingleton('core/session')->addError('This Order is Jet Order create shipment by jet');
                    Mage::app()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
                    Mage::app()->getResponse()->sendResponse();
                     exit;
                }
            }            
        }
	public function checkEnabled()
    {
        $helper = Mage::helper('jet');
        if (!$helper->isEnabled()) {
            Mage::app()->getFrontController()->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            Mage::app()->getFrontController()->getResponse()->setHeader('Status', '404 File not found');
            $request = Mage::app()->getRequest();
            $request->initForward()
                ->setControllerName('indexController')
                ->setModuleName('Mage_Cms')
                ->setActionName('defaultNoRoute')
                ->setDispatched(false);
            return;
        }
    }
	/*
	*   this observer created for getting listing direct cancel orders
	*/
	public function directCancel(){
        $orderdata = Mage::helper('jet')->CGetRequest('/orders/directedCancel');
        //$response = json_decode($orderdata, true);

		$this->createOrder();


		/*
        //Mage::log($response, null, 'mylogfile.log');

        $autoReject=false;

        if (isset($response['order_urls']) && count($response['order_urls']) > 0) {
            foreach ($response['order_urls'] as $jetorderurl) {
                $result = Mage::helper('jet')->CGetRequest($jetorderurl);
                $resultObject = json_decode($result);
                $resultarray = json_decode($result, true);
                $orderitems_count = count($resultarray['order_items']);
                $recursive_itemcount = 0;
                $reject_items_arr = array();
                if (sizeof($resultarray) > 0 && isset($resultarray['merchant_order_id']) && ($resultarray['status']=='acknowledged')) {
                    foreach($resultarray['order_items'] as $arr){
                        $updatedQty=$arr['request_order_quantity'] - $arr['request_order_cancel_qty'];
                        $uniqui_id = mt_rand(10, 10000124);
                        $shipment_arr[] = array(
                            'merchant_sku' => $arr['merchant_sku'],
                            'response_shipment_sku_quantity' => (int)$updatedQty,
                            'response_shipment_cancel_qty' => (int)$arr['request_order_cancel_qty']
                        );

                        if($arr['request_order_quantity']==$arr['request_order_cancel_qty']){
                            $recursive_itemcount++;
                            $autoReject=true;
                            $reject_items_arr[] = array('order_item_acknowledgement_status' => 'nonfulfillable - invalid merchant SKU',
                                'order_item_id' => $arr['order_item_id']);
                        }
                    }

                    $merchantOrderid = $resultarray['merchant_order_id'];
                    $jetOrder = Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('merchant_order_id', $merchantOrderid)->getFirstItem();
                    $id=$jetOrder->getId();
                    $mage_id=$jetOrder->getData('magento_order_id');
                    if ($autoReject && ($recursive_itemcount == $orderitems_count)) {
                        $order_id =$merchantOrderid;
                        $offset_end = Mage::helper('jet/jet')->getStandardOffsetUTC();
                        if (empty($offset_end) || trim($offset_end) == '') {
                            $offset = '.0000000-00:00';
                        } else {
                            $offset = '.0000000' . trim($offset_end);
                        }
                        $shipTodate = date("Y-m-d");
                        $shipTotime = date("h:i:s");
                        $storeId = Mage::getStoreConfig('jet_options/ced_jet/jet_storeid');
                        $website = Mage::getModel('core/store')->load($storeId);
                        $websiteId = $website->getWebsiteId();
                        $zip = "01001";
                        $tommorow = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y"));
                        $exptime = date("Y-m-d", $tommorow);
                        $Ship_todate = $shipTodate . 'T' . $shipTotime . $offset;
                        $Exp_delivery = date("Y-m-d", $exptime) . 'T' . $shipTotime . $offset;
                        $Carr_pickdate = date("Y-m-d", $exptime) . 'T' . $shipTotime . $offset;
                        $carrier = "FedEx";
                        $inc_id = mt_rand(10, 100001244);
                        $data_ship = array();
                        $data_ship['shipments'][] = array(
                            'alt_shipment_id' => $inc_id,
                            'shipment_tracking_number' => "$inc_id",
                            'response_shipment_date' => $Ship_todate,
                            'response_shipment_method' => '',
                            'expected_delivery_date' => $Exp_delivery,
                            'ship_from_zip_code' => "$zip",
                            'carrier_pick_up_date' => $Carr_pickdate,
                            'carrier' => "$carrier",
                            'shipment_items' => $shipment_arr
                        );

                        if ($data_ship) {
                            $data = Mage::helper('jet')->CPutRequest('/orders/' . $order_id . '/shipped', json_encode($data_ship));
                            $responsedata = json_decode($data);

                            if($responsedata == NULL){
                                $saveJetorder=Mage::getModel('jet/jetorder')->load($id);
                                $saveJetorder->setData('status','cancelled');
                                $saveJetorder->save();

                                $orderModel = Mage::getModel('sales/order')->loadByIncrementId($mage_id);
                                if($orderModel->canCancel()){
                                    $orderModel->cancel();
                                    $orderModel->setStatus('canceled');
                                    $orderModel->save();
                                }
                            }
                        }
                    }

                    echo $id.'<br/>';
                    if(strlen($id)){
                    $updateJetOrderData=Mage::getModel('jet/jetorder')->load($id);
                    $updateJetOrderData->setData('order_data',serialize($resultObject));
                    $updateJetOrderData->setData('customer_cancelled',1);
                    $updateJetOrderData->save();
                    }
                }
            }
        } */
	}

    public function cancelJetorder($observer)
    {
        $order = $observer->getOrder();
        $url=Mage::helper('core/url')->getCurrentUrl();
        $inc = $order->getData('increment_id');
        $reject=Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('magento_order_id', $inc)->getFirstItem();

        if (($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED) && (preg_match("/\bcancel\b/i", $url)) && ($reject->getId()!='')) {
            $order_id =$reject->getData('merchant_order_id');
            $offset_end = Mage::helper('jet/jet')->getStandardOffsetUTC();
            if (empty($offset_end) || trim($offset_end) == '') {
                $offset = '.0000000-00:00';
            } else {
                $offset = '.0000000' . trim($offset_end);
            }
            $shipTodate = date("Y-m-d");
            $shipTotime = date("h:i:s");
            $storeId = Mage::getStoreConfig('jet_options/ced_jet/jet_storeid');
            $website = Mage::getModel('core/store')->load($storeId);
            $websiteId = $website->getWebsiteId();
            $zip = "01001";
            $tommorow = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y"));
            $exptime = date("Y-m-d", $tommorow);
            $Ship_todate = $shipTodate . 'T' . $shipTotime . $offset;
            $Exp_delivery = date("Y-m-d", $exptime) . 'T' . $shipTotime . $offset;
            $Carr_pickdate = date("Y-m-d", $exptime) . 'T' . $shipTotime . $offset;

            $inc_id = $order->getData("increment_id");

            $temp_carrier = unserialize($reject->getData('order_data'));
            $carrier = $temp_carrier->order_detail->request_shipping_carrier;
            if(($carrier == '') || ($carrier == null)){$carrier = 'Fedex';}

            $shipment_arr = array();
            $items = $order->getAllItems();
            foreach ($items as $i) {

                $uniqui_id = mt_rand(10, 10000124);
                $shipment_arr[] = array(
                    'merchant_sku' => $i->getSku(),
                    'response_shipment_sku_quantity' => 0,
                    'response_shipment_cancel_qty' => (int)$i->getData('qty_ordered')
                );
            }

            $data_ship = array();
            $data_ship['shipments'][] = array(
                'alt_shipment_id' => $inc_id,
                'shipment_tracking_number' => "$inc_id",
                'response_shipment_date' => $Ship_todate,
                'response_shipment_method' => '',
                'expected_delivery_date' => $Exp_delivery,
                'ship_from_zip_code' => "$zip",
                'carrier_pick_up_date' => $Carr_pickdate,
                'carrier' => "$carrier",
                'shipment_items' => $shipment_arr
            );

            if ($data_ship) {
                $data = Mage::helper('jet')->CPutRequest('/orders/' . $order_id . '/shipped', json_encode($data_ship));
                $responsedata = json_decode($data);

                if($responsedata == NULL){
                    $this->saveJetOrder($reject->getId());
                    /*$jetId=$reject->getId();
                    $saveJetorder=Mage::getModel('jet/jetorder')->load($jetId);
                    $saveJetorder->setData('status','rejected');
                    $saveJetorder->save();*/
                }

             }
        }
    }

    public function updateProduct()
    {
       $collection = Mage::getModel('jet/fileinfo')->getCollection()->addFieldToFilter('Status', 'Acknowledged')->addFieldToSelect('jet_file_id')->addFieldToSelect('id');

        foreach ($collection as $jFile) {
            $jFile = Mage::getModel('jet/fileinfo')->load($jFile->getId());
            if ($jFile->getJetFileId() && $jFile->getJetFileId() != null) {
                Mage::helper('jet')->updateLogFileStatus($jFile);
            }
        }
        if(count($collection)>0){
            Mage::getSingleton('adminhtml/session')->addSuccess('Rejected files list has been updated.');
        }else{
            Mage::getSingleton('adminhtml/session')->addError('No Acknowledged Files available to update. Please upload the products to jet first then retry');
        }
    }



    /**
     * @Jet Orders Synchronisation
     * Create Available Jet Orders in Magento
     */
    public function createOrder()
    {
               
        $storeId = Mage::getStoreConfig('jet_options/ced_jet/jet_storeid');
        $website = Mage::getModel('core/store')->load($storeId);
        $websiteId = $website->getWebsiteId();
        $store = Mage::app()->getStore($website->getCode());

        $orderdata = Mage::helper('jet')->CGetRequest('/orders/ready');
        $response = json_decode($orderdata, true);


        if (isset($response['order_urls']) && count($response['order_urls']) > 0) {
            $count = 0;

            foreach ($response['order_urls'] as $jetorderurl) {
				$result = Mage::helper('jet')->CGetRequest($jetorderurl);
                $resultObject = json_decode($result);
                $result = json_decode($result, true);

				$email=$resultObject->hash_email;

				if($email=='' && $email ==NULL){
					$email ='customer@jet.com';
				}

				$customer = Mage::getModel('customer/customer')
							->setWebsiteId($websiteId)
							->loadByEmail($email);

                if (sizeof($result) > 0 && isset($result['merchant_order_id'])) {

                    $merchantOrderid = $result['merchant_order_id'];
                    $resultdata = Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('merchant_order_id', $merchantOrderid);

                    if (count($resultdata) <= 0) {
                        $customer = $this->_assignCustomer($result, $customer, $websiteId, $store, $email);
                        if (!$customer) {
                            return;
                        } else {
                            $this->prepareQuote($result, $customer, $websiteId, $store, $email, $resultObject);
                        }
                    }
                }
                Mage::unregister('attributeClear');
            }

        }
    }
public function jetfilesDelete()
    {

        $url = Mage::getBaseDir();
        $path = $url.'/var/jetupload/';
        $handle = opendir($path);
         if ($handle = opendir($path))
         { 
            while (false !== ($file = readdir($handle)))
              { 
                 $filelastmodified = filemtime($path . $file);
                 //24 hours in a day * 3600 seconds per hour
                //if((time() - $filelastmodified) > 24*3600 && is_file($file))
                 //{
                    unlink($path . $file);
                //}

             }
            closedir($handle); 
        }
    }


    public function _assignCustomer($result, $customer, $websiteId, $store, $email)
    {
        if (is_object($customer) && $customer->getId() == NULL && $customer->getId() == '') {
            try {
                $Cname = $result['buyer']['name'];
                if (trim($Cname) == '' || $Cname == null) {
                    $Cname = $result['shipping_to']['recipient']['name'];
                }
                $Cname = preg_replace('/\s+/', ' ', $Cname);
                $customer_name = explode(' ', $Cname);

                if (!isset($customer_name[1]) || $customer_name[1] == '') {
                    $customer_name[1] = $customer_name[0];
                }

                $customer = Mage::getModel('customer/customer');
                $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($customer_name[0])
                    ->setLastname($customer_name[1])
                    ->setEmail($email)
                    ->setPassword("password");
                $customer->save();


                return $customer;

            } catch (Exception $e) {
                //$message = "please check the customer Email Id either email format or enter email properly into jet setting!";
                $message = $e->getMessage();
                $jetOrderError = Mage::getModel('jet/orderimport');
                $jetOrderError->setMerchantOrderId($result['merchant_order_id']);
				$jetOrderError->setReferenceNumber($result['reference_order_id']);
                $jetOrderError->setReason($message);
                $jetOrderError->save();
                return false;
            }
        } else {
            return $customer;
        }

     }

    public function prepareQuote($result, $customer, $websiteId, $store, $email, $resultObject)
    {
        /*$productexist_config = Mage::getStoreConfig('jet_options/acknowledge_options_options/exist');
        $productoutofstock_config = Mage::getStoreConfig('jet_options/acknowledge_options/outofstock');
        $productdisabled_config = Mage::getStoreConfig('jet_options/acknowledge_options/pdisabled');
        $Quote_execute = false;
        */


        $shippingMethod = 'shipjetcom';
        $paymentMethod= Mage::getStoreConfig('jet_options/ced_jet/jet_default_payment');
        if($paymentMethod== NULL || $paymentMethod==""){
            $paymentMethod = 'payjetcom';
        }
        $productArray = array();
        $baseCurrency = $store->getBaseCurrencyCode();
        $items_array = $result['order_items'];
        $baseprice = 0;
        $shippingcost = 0;
        $tax = 0;
        $storeId = $store->getId();
        $autoReject = false;
        $reject_items_arr = array();
        $order_items_qty_arr = array();
        $reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
         $order_prefix = Mage::getStoreConfig('jet_options/ced_jet/jet_order_prefix');
         if($order_prefix == null || $order_prefix == '')
         {
            $order_prefix = 'JET-';
         }
        $order = Mage::getModel('sales/order')
            ->setIncrementId($order_prefix.$reservedOrderId)
            ->setStoreId($storeId)
            ->setQuoteId(0)
            ->setGlobal_currency_code($baseCurrency)
            ->setBase_currency_code($baseCurrency)
            ->setStore_currency_code($baseCurrency)
            ->setOrder_currency_code($baseCurrency);

        foreach ($items_array as $item) {
            $message = '';
            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item['merchant_sku']);
            if ($product) {
                $product = Mage::getModel('catalog/product')->load($product->getEntityId());
                if ($product->getStatus() == '1') {
                    $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                    if (($stock->getQty() > 0) && ($stock->getIsInStock() == '1') && ($stock->getQty() >= $item['request_order_quantity']) && ($item['request_order_quantity'] != $item['request_order_cancel_qty'])) {

                        /*
                        for inventory mail code start
                        */
                        if($stock->getQty() < 3)
                        {

                            $order_items_qty_arr[$item['merchant_sku']] = $stock->getQty();
                        }

                        /*
                        for inventory mail code end
                        */
                        $productArray[] = array('id' => $product->getEntityId(), 'qty' => $item['request_order_quantity']);
                        $price = $item['item_price']['base_price'];
                        $qty = $item['request_order_quantity'];
                        $cancelqty= $item['request_order_cancel_qty'];
                        //if($cancelqty!=0)$qty=$qty - $cancelqty;
                        $baseprice += $qty * $price;
                        $shippingcost += ($item['item_price']['item_shipping_cost'] * $qty) + ($item['item_price']['item_shipping_tax'] * $qty);
                        $tax += $item['item_price']['item_tax'];

                        $rowTotal = $price * $qty;
                        $orderItem = Mage::getModel('sales/order_item')
                            ->setStoreId($storeId)
                            ->setQuoteItemId(0)
                            ->setQuoteParentItemId(NULL)
                            ->setProductId($product->getEntityId())
                            ->setProductType($product->getTypeId())
                            ->setQtyBackordered(NULL)
                            ->setTotalQtyOrdered($qty)
                            ->setQtyOrdered($qty)
                            ->setName($product->getName())
                            ->setSku($product->getSku())
                            ->setPrice($price)
                            ->setBasePrice($price)
                            ->setOriginalPrice($price)
                            ->setRowTotal($rowTotal)
                            ->setBaseRowTotal($rowTotal);

                        //$subTotal += $rowTotal;
                        $order->addItem($orderItem);
                         Mage::getSingleton('cataloginventory/stock')->registerItemSale($orderItem);
                        $Quote_execute = True;
                         $reject_items_arr[] = array('order_item_acknowledgement_status' => 'fulfillable',
                                'order_item_id' => $item['order_item_id']);

                    } else {

                        /*if ($productoutofstock_config) {*/
                            $autoReject = true;
                            $reject_items_arr[] = array('order_item_acknowledgement_status' => 'nonfulfillable - invalid merchant SKU',
                                'order_item_id' => $item['order_item_id']);
                        /*}*/
                        $message = "Product " . $item['merchant_sku'] . " is Out Of Stock";
                        $jetOrderError = Mage::getModel('jet/orderimport');
                        $jetOrderError->setMerchantOrderId($result['merchant_order_id']);
						$jetOrderError->setReferenceNumber($result['reference_order_id']);
                        $jetOrderError->setReason($message);
                        $jetOrderError->setOrderItemId($item['order_item_id']);
                        $jetOrderError->save();
                        $Quote_execute = False;
                       
                    }
                } else {


                    /*if ($productdisabled_config) {*/
                        $autoReject = true;
                        $reject_items_arr[] = array('order_item_acknowledgement_status' => 'nonfulfillable - invalid merchant SKU',
                            'order_item_id' => $item['order_item_id']);
                    /*}*/
                    $message = "Product " . $item['merchant_sku'] . " is Not Enabled!";
                    $jetOrderError = Mage::getModel('jet/orderimport');
                    $jetOrderError->setMerchantOrderId($result['merchant_order_id']);
					$jetOrderError->setReferenceNumber($result['reference_order_id']);
                    $jetOrderError->setReason($message);
                    $jetOrderError->setOrderItemId($item['order_item_id']);
                    $jetOrderError->save();
                    $Quote_execute = False;
                    
                }
            }else{


                /*if ($productexist_config) {*/
                    $autoReject = true;
                    $reject_items_arr[] = array('order_item_acknowledgement_status' => 'nonfulfillable - invalid merchant SKU',
                        'order_item_id' => $item['order_item_id']);
                /*}*/
                $message = "Product SKU " . $item['merchant_sku'] . " Not Found on the site!";
                $jetOrderError = Mage::getModel('jet/orderimport');
                $jetOrderError->setMerchantOrderId($result['merchant_order_id']);
				$jetOrderError->setReferenceNumber($result['reference_order_id']);
                $jetOrderError->setReason($message);
                $jetOrderError->setOrderItemId($item['order_item_id']);
                $jetOrderError->save();
                $Quote_execute = False;
            }
        }

        if ($autoReject) {
            $data_var = array();
            $data_var['acknowledgement_status'] = "rejected - item level error";
            $data_var['order_items'] = $reject_items_arr;

            
            $data = Mage::helper('jet')->CPutRequest('/orders/' . $result['merchant_order_id'] . '/acknowledge', json_encode($data_var));
            $response = json_decode($data);
            
            $reject=Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('merchant_order_id', $result['merchant_order_id'])->getFirstItem();
            if($response == NULL){
                //$this->saveJetOrder($reject->getId());
            }
        }

        if (count($productArray) > 0 && count($items_array) == count($productArray) && !$autoReject) {
            $transaction = Mage::getModel('core/resource_transaction');
            $order->setCustomer_email($customer->getEmail())
                ->setCustomerFirstname($customer->getFirstname())
                ->setCustomerLastname($customer->getLastname())
                ->setCustomerGroupId($customer->getGroupId())
                ->setCustomer_is_guest(0)
                ->setCustomer($customer);

            $CshippingInfo = $result['shipping_to']['recipient']['name'];
            $CshippingInfo = preg_replace('/\s+/', ' ', $CshippingInfo);
            $customer_shippingInfo = explode(' ', $CshippingInfo);

            if (!isset($customer_shippingInfo[1]) || $customer_shippingInfo[1] == '') {
                $customer_shippingInfo[1] = $customer_shippingInfo[0];
            }
            // set Billing Address
            try {
                $billing = $customer->getDefaultBillingAddress();
                $complete_address1 = $result['shipping_to']['address']['address1'];
                $complete_address2 = $result['shipping_to']['address']['address2'];

                if ($complete_address2 != null && trim($complete_address2) != '') {
                    $complete_address1 = $complete_address1 . ' ' . $complete_address2;
                }
                $billingAddress = Mage::getModel('sales/order_address')
                    ->setStoreId($storeId)
                    ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
                    ->setCustomerId($customer->getId())
                    ->setCustomerAddressId($customer->getDefaultBilling())
                    ->setCustomer_address_id('')
                    ->setPrefix('')
                    ->setFirstname($customer_shippingInfo[0])
                    ->setMiddlename('')
                    ->setLastname($customer_shippingInfo[1])
                    ->setSuffix('')
                    ->setCompany('')
                    //->setStreet($result['shipping_to']['address']['address1'])
                    ->setStreet($complete_address1)
                    ->setCity($result['shipping_to']['address']['city'])
                    ->setCountry_id('US')
                    ->setRegion($result['shipping_to']['address']['state'])
                    ->setRegion_id('')
                    ->setPostcode($result['shipping_to']['address']['zip_code'])
                    ->setTelephone($result['shipping_to']['recipient']['phone_number'])
                    ->setFax('');
                $order->setBillingAddress($billingAddress);

                $shipping = $customer->getDefaultShippingAddress();
                $shippingAddress = Mage::getModel('sales/order_address')
                    ->setStoreId($storeId)
                    ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
                    ->setCustomerId($customer->getId())
                    ->setCustomerAddressId($customer->getDefaultShipping())
                    ->setCustomer_address_id('')
                    ->setPrefix('')
                    ->setFirstname($customer_shippingInfo[0])
                    ->setMiddlename('')
                    ->setLastname($customer_shippingInfo[1])
                    ->setSuffix('')
                    ->setCompany('')
                    //->setStreet($result['shipping_to']['address']['address1'])
                    ->setStreet($complete_address1)
                    ->setCity($result['shipping_to']['address']['city'])
                    ->setCountry_id('US')
                    ->setRegion($result['shipping_to']['address']['state'])
                    ->setRegion_id('')
                    ->setPostcode($result['shipping_to']['address']['zip_code'])
                    ->setTelephone($result['shipping_to']['recipient']['phone_number'])
                    ->setFax('');
                $order->setShippingAddress($shippingAddress)
                    ->setShippingMethod($shippingMethod)
                    ->setShippingDescription(Mage::getStoreConfig("carriers/shipjetcom/title") . "-" . $shippingMethod);
                $orderPayment = Mage::getModel('sales/order_payment')
                    ->setStoreId($storeId)
                    ->setCustomerPaymentId(0)
                    ->setMethod($paymentMethod);
                //->setPo_number(' - ');
                $order->setPayment($orderPayment);


                $order->setSubtotal($baseprice)
                    ->setBaseSubtotal($baseprice)
                    ->setShippingAmount($shippingcost)
                    ->setBaseShippingAmount($shippingcost)
                    ->setTaxAmount($tax)
                    ->setBaseTaxAmount($tax)
                    ->setGrandTotal($baseprice + $shippingcost + $tax)
                    ->setBaseGrandTotal($baseprice + $shippingcost + $tax);
                    $order->setBaseToGlobalRate(1);

                $transaction->addObject($order);
                $transaction->addCommitCallback(array($order, 'place'));
                $transaction->addCommitCallback(array($order, 'save'));
                if ($transaction->save() && $order->getId() > 0) {
                    $OrderData = array('order_item_id' => $result['order_items'][0]['order_item_id'],
                        'merchant_order_id' => $result['merchant_order_id'],
                        'merchant_sku' => $result['order_items'][0]['merchant_sku'],
                        'deliver_by' => $result['order_detail']['request_delivery_by'],
                        'magento_order_id' => $order->getIncrementId(),
                        'status' => $result['status'],
                        'order_data' => serialize($resultObject),
                        'reference_order_id' => $result['reference_order_id']
                    );

                    $model = Mage::getModel('jet/jetorder')->addData($OrderData);
                    $model->save();

                    //if (Mage::getStoreConfig('jet_options/jet_order/active') == 1) {
                     	 $this->autoOrderacknowledge($order->getIncrementId());
                    //}
                    $this->sendmailtoadmin($order->getIncrementId(),$result['order_detail']['request_ship_by'],$order_items_qty_arr,$result['order_items'][0]['merchant_sku']);

                    if($order->canInvoice()) {
                        /*$invoiceId = Mage::getModel('sales/order_invoice_api')
                            ->create($order->getIncrementId(), array());
                        $invoice = Mage::getModel('sales/order_invoice')
                            ->loadByIncrementId($invoiceId);
                        $invoice->capture()->save();*/

                        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                        $invoice->register();
                        $invoice->getOrder()->setCustomerNoteNotify(false);
                        $invoice->getOrder()->setIsInProcess(true);

                        $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder());

                        $transactionSave->save();
                    }
                }

            } catch (Exception $e) {
                $message = "Fail To Create Order Due to Error " . $e->getMessage();
                $jetOrderError = Mage::getModel('jet/orderimport');
                $jetOrderError->setMerchantOrderId($result['merchant_order_id']);
				        $jetOrderError->setReferenceNumber($result['reference_order_id']);
                $jetOrderError->setOrderItemId($item['order_item_id']);
                $jetOrderError->setReason($message);
                $jetOrderError->save();

            }
        }
    }
     /*
     * @Auto Order Notification Mail To Store Admin
     */
     public function sendmailtoadmin($order_id,$ship_date,$lesinvetory,$sku)
        {
            $current_date =  new DateTime(date('Y-m-d'));
          
           $from_email = Mage::getStoreConfig('jet_options/ced_jet/jet_admin_email_id');
           $orderItems='';
            $orderItems.= '<h3>Ordered Sku : '.$sku.'</h3>';
            if($from_email)
            {
                    if($ship_date)
                    {
                        
                        $date_array = array();
                        $date_array = explode('T',$ship_date);
                        $time = new DateTime($date_array[0]);
                        $interval = $current_date->diff($time)->days;
                      if($interval<=2)
                      {
                        

                          if(count($lesinvetory)>0)
                         {

                            // $msg = "Hello! \n  Congratulations. You have a New Jet Order ".$order_id." imported to your Magento admin panel. The Shipment date of this order is near and the inventory of the products existing in this order is also getting low. Please review your admin panel instantly.\n Thanks";
                    $msg ='<table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="email-heading">
                                            <h1>You have a new order from jet.</h1>
                                            <p>The Shipment date of this order is near and the inventory of the products existing in this order is also getting low. Please update the inventory and ship the order asap.Thanks.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="order-details">
                                <h3>Your order <span class="no-link">#'.$order_id.'</span></h3>
                                
                                '.$orderItems.'
                            </td>
                        </tr>  
                    </table>';

                          //$msg = wordwrap($msg,70);
                          $this->directmail($from_email,$msg);
                         }
                         else
                         {
                            // $msg = "Hello ! \n  Congratulations. You have a New Jet Order ".$order_id." imported to your Magento admin panel. The Shipment Date of this order is near. Please review your admin panel instantly. \n Thanks";

                        $msg ='<table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="email-heading">
                                            <h1>You have a new order from jet.</h1>
                                            <p>The Shipment Date of this order is near. Please ship the order asap.Thanks.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="order-details">
                                <h3>Your order <span class="no-link">#'.$order_id.'</span></h3>
                              
                                '.$orderItems.'
                            </td>
                        </tr>  
                    </table>';

                          //$msg = wordwrap($msg,70);
                         $this->directmail($from_email,$msg);
                         }
                      }
                      else
                      {
                        
                         if(count($lesinvetory)>0)
                         {
                             //$msg = "Hello !\n  Congratulations. You have a New Jet Order ".$order_id." imported to your Magento admin panel. The inventory of the products existing in this order is getting low. Therefore, please review your admin panel and update it.\n Thanks";

                              $msg ='<table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="email-heading">
                                            <h1>You have a new order from jet.</h1>
                                            <p> The inventory of the products existing in this order is getting low. Therefore, Please update the inventory asap and ship the order.Thanks.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="order-details">
                                <h3>Your order <span class="no-link">#'.$order_id.'</span></h3>
                               
                                '.$orderItems.'
                            </td>
                        </tr>  
                    </table>';
                          //$msg = wordwrap($msg,70);
                         $this->directmail($from_email,$msg);
                         }
                         else
                         {
                            //$msg = "Hello !\n  Congratulations. You have a New Jet Order ".$order_id." imported to your Magento admin panel. Please review your admin panel.\n Thanks";

                            $msg ='<table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="email-heading">
                                            <h1>You have a new order from jet.</h1>
                                            <p> Please review your admin panel."</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="order-details">
                                <h3>Your order <span class="no-link">#'.$order_id.'</span></h3>
                              
                                '.$orderItems.'
                            </td>
                        </tr>  
                    </table>';

                          //$msg = wordwrap($msg,70);
                         $this->directmail($from_email,$msg);
                         }
                      }
                       
                    }
                        else
                        {
                             //$msg = "Hello !\n  Congratulations. You have a New Jet Order ".$order_id." imported to your Magento admin panel. Please review your admin panel.\n Thanks";
                             //$msg = "Hello !\n  Congratulations. You have a New Jet Order ".$order_id." imported to your Magento admin panel. Please review your admin panel.\n Thanks";

                            $msg ='<table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="email-heading">
                                            <h1>You have a new order from jet.</h1>
                                            <p> Please review your admin panel."</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="order-details">
                                <h3>Your order <span class="no-link">#'.$order_id.'</span></h3>
                               
                                '.$orderItems.'
                            </td>
                        </tr>  
                    </table>';
                         // $msg = wordwrap($msg,70);
                        $this->directmail($from_email,$msg);
                        }
                 
            }

           return;
        }
        public function directmail($from_email,$msg)
        {
            $to_email = $from_email;
            $to_name =  'Jet Seller';
            $subject = 'Imp: New Jet New Order Imported';
            $Body = $msg;
            $senderEmail ='jetadmin@cedcommerce.com';
            $senderName ='Jet';

            $mail = new Zend_Mail(); 
            $mail->setBodyHtml($Body); 
            $mail->setFrom($senderEmail, $senderName);
            $mail->addTo($to_email, $to_name);
            //$mail->addCc($cc, $ccname);    //can set cc
            //$mail->addBCc($bcc, $bccname);    //can set bcc
            $mail->setSubject($subject);
           try{
                 $mail->send();

            }catch(\Exception $e)
            {
                print_r($e->getMessage());
               
            }
        }

    /*
     * @Auto Order Acknowledgement Process
     */
    public function autoOrderacknowledge($Incrementid)
    {
        $resultdata = Mage::getModel('jet/jetorder')->getCollection()
            ->addFieldToFilter('magento_order_id', $Incrementid)
            ->addFieldToSelect('order_data')
            ->addFieldToSelect('id')
            ->addFieldToSelect('merchant_order_id')
            ->getData();


        if (empty($resultdata) || count($resultdata) == 0) {
            return 0;
        }

        $serialize_data = unserialize($resultdata[0]['order_data']);

        if (empty($serialize_data) || count($serialize_data) == 0) {

            $result = Mage::helper('jet')->CGetRequest('/orders/withoutShipmentDetail/' . $resultdata[0]['merchant_order_id']);
            $Ord_result = json_decode($result);

            if (empty($result) || count($result) == 0) {
                return 0;
            } else {

                $jobj = Mage::getModel('jet/jetorder')->load($resultdata[0]['id']);
                $jobj->setOrderData(serialize($Ord_result));
                $jobj->save();

                $serialize_data = $Ord_result;
            }
        }

        if (empty($serialize_data)) {
            return 0;
        }

        $fullfill_array = array();
        foreach ($serialize_data->order_items as $k => $valdata) {
            $fullfill_array[] = array('order_item_acknowledgement_status' => 'fulfillable',
                'order_item_id' => $valdata->order_item_id);
        }


        $order_id = $resultdata[0]['merchant_order_id'];
        $data_var = array();
        $data_var['acknowledgement_status'] = "accepted";


        $data_var['order_items'] = $fullfill_array;


        $data = Mage::helper('jet')->CPutRequest('/orders/' . $order_id . '/acknowledge', json_encode($data_var));

        $response = json_decode($data);

        if (!empty($response) && $response != null && count($response->errors) > 0 && $response->errors[0] != "") {
            return 0;
        } else {
            $modeldata = Mage::getModel('jet/jetorder')->getCollection()
                ->addFieldToFilter('magento_order_id', $Incrementid)->getData();

            if (count($modeldata) > 0) {
                $id = $modeldata[0]['id'];
                $model = Mage::getModel('jet/jetorder')->load($id);
                $model->setStatus('acknowledged');
                $model->save();
            }
        }
        return 0;
    }

    public function addButton(Varien_Event_Observer $event)
    {

        $block = $event->getBlock();
        if ($block->getId() != 'sales_order_view') {
            return $this;
        }

        $order = $block->getOrder();
        if (!$order->getId()) {
            return $this;
        }

        $orderid = $order->getId();
        $Incrementid = $order->getIncrementId();
        $resultdata = Mage::registry('current_jet_order');

        if (count($resultdata) == 0) {
            $resultdata = Mage::getModel('jet/jetorder')->getCollection()
                ->addFieldToFilter('magento_order_id', $Incrementid)
                //->addFieldToSelect('status')
                ->getData();
        }
        $status = '';
        if(isset($resultdata[0]['status']))
            $status = $resultdata[0]['status'];
        if ($status == 'ready') {
            $block->addButton('delete', array(
                'label' => 'Acknowledge',
                'class' => 'Acknowledge',
                'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to send acknowledge for this order?')
                    . '\', \'' . $block->getUrl('adminhtml/adminhtml_jetorder/acknowledge', array('increment_id' => $Incrementid)) . '\')',
            ));
            $block->addButton('delete1', array(
                'label' => 'Reject',
                'class' => 'Reject',
                'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to send rejection for this order?')
                    . '\', \'' . $block->getUrl('adminhtml/adminhtml_jetorder/rejectreason', array('increment_id' => $Incrementid)) . '\')',
            ));
        } else if ($status == 'acknowledged') {

            $block->addButton('delete', array(
                'label' => 'Acknowledge',
                'class' => 'Acknowledge',
                'disabled' => true,
                'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to send acknowledge for this order?')
                    . '\', \'' . $block->getUrl('adminhtml/adminhtml_jetorder/acknowledge', array('increment_id' => $Incrementid)) . '\')',


            ));

            $block->addButton('delete1', array(
                'label' => 'Reject',
                'class' => 'Reject',
                'disabled' => true,
                'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to send rejection for this order?')
                    . '\', \'' . $block->getUrl('adminhtml/adminhtml_jetorder/reject', array('increment_id' => $Incrementid)) . '\')',


            ));
        }
    }



    public function jetreturn()
    {
        $false_return = "";
        $success_return = "";
        $success_count = 0;
        $false_count = 0;
        $data = Mage::helper('jet')->CGetRequest('/returns/created');

        $response = json_decode($data);
        $response = $response->return_urls;


        if (!empty($response) && count($response) > 0) {

            foreach ($response as $res) {
                $arr = explode("/", $res);
                $returnid = "";
                $returnid = $arr[3];
                $resultdata = Mage::getModel('jet/jetreturn')->getCollection()->addFieldToFilter('returnid', $returnid)->getData();
               
                if (empty($resultdata)) {

                    $returndetails = Mage::helper('jet')->CGetRequest($res);
                    
                    if ($returndetails) {
                        $return = json_decode($returndetails);
                        $serialized_details = serialize($return);

						try{
							$text = array(
								 'merchant_order_id' => $return->merchant_order_id,
								 'status' => 'created',
								 'returnid' => "$returnid",
								 'return_details' => $serialized_details);

							$model = Mage::getModel('jet/jetreturn')->addData($text);
							$model->save();
						}catch(Exception $e){
							
						}

                        if ($success_return == "") {
                            $success_return = $returnid;
                            $success_count++;
                        } else {
                            $success_return = $success_return . " , " . $returnid;
                            $success_count++;
                        }
                    } else {
                        if ($false_return == "") {
                            $false_return = $returnid;
                            $false_count++;
                        } else {
                            $false_return = $false_return . " , " . $returnid;
                            $false_count++;
                        }
                    }


                }

            }
        }
    }

    public function updaterefund()
    {
        $res_arr=array('created','processing');
        $result = Mage::getModel('jet/jetrefund')->getCollection()->addFieldToFilter('refund_status', array(array('in' => $res_arr)))->addFieldToSelect('refund_id')->getData();
        $count = count($result);
        $success_count = 0;
        $success_ids = "";

        if ($count > 0) {

            foreach ($result as $res) {

                $refundid = "";
                $refundid = $res['refund_id'];
                $data = Mage::helper('jet')->CGetRequest('/refunds/state/' . $refundid . '');
                $responsedata = json_decode($data);
                $success_count++;
                if ($responsedata->refund_status != 'created') {
                    $modeldata = Mage::getModel('jet/jetrefund')->getCollection()->addFieldToFilter('refund_id', $refundid);
                    foreach ($modeldata as $models) {
                        $id = $models['id'];
                        $update = array('refund_status' => $responsedata->refund_status);
                        $model = "";
                        $model = Mage::getModel('jet/jetrefund')->load($id);
                        $model->addData($update);
                        $model->save();
                        $status = "";
                        $status = $responsedata->refund_status;
                        if (trim($status) == 'accepted') {
                            $saved_data = "";
                            $saved_data = $model->getData('saved_data');
                            if ($saved_data != "") {
                                $saved_data = unserialize($saved_data);
                                $flag = false;
                                $flag = Mage::helper('jet')->generateCreditMemoForRefund($saved_data);
                            }

                        }
                    }
                }
            }
        }
    }




    public function updatePassive_status()
    {
		$this->getProductByStatus('Archived', 'archived');
		$this->getProductByStatus('Excluded', 'excluded');
        $this->getProductByStatus('Unauthorized', 'unauthorized');
    }

    public function updateReview_status()
    {
        $this->getProductByStatus('Under Jet Review', 'under_jet_review');
		$this->getProductByStatus('Missing Listing Data', 'missing_listing_data');
    }

    public function updateActive_status()
    {
        $this->getProductByStatus('Available for Purchase', 'available_for_purchase');
    }

    public function getProductByStatus($status, $status_code, $profileId = false)
    {

        $collection = Mage::getResourceModel('catalog/product_collection');
        $count = $collection->getSize() + 100;

        $raw_encode = rawurlencode($status);
        $response = Mage::helper('jet')->CGetRequest('/portal/merchantskus?from=0&size=5000&statuses='.$raw_encode);
        $result = json_decode($response,true);

        $SKU = array();

        if(is_array($result) && isset($result['merchant_skus']) && count($result['merchant_skus'])>0){
            foreach ($result['merchant_skus'] as $sku) {
                 $SKU[] = $sku['merchant_sku'];
            }
        }

        if (count($SKU) == 0)
            return;

        $collection->addAttributeToFilter('sku', array('in', $SKU));
        $allIds = $collection->getAllIds();
        
        $parent_idss = array();
        foreach ($allIds as $key => $value) {
           $parent_idss[] = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($value);

        }
        if(count($parent_idss)>1)
        {
            $allIds = array();
            foreach ($parent_idss as $key => $value) {
                if(count($value[0])==1)
                {
                    $allIds[] = $value[0];
                }
           
            }
        }
        

          $attribute_mod = Mage::getModel('eav/config')->getAttribute('catalog_product', 'jet_product_status');
        $att_id = $attribute_mod->getId();
       
        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();

        if (sizeof($allIds) > 0) {

            //$chunk_data = array_chunk($allIds, 500);
            
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
            $readconnection = $resource->getConnection('core_read');
           
            foreach ($allIds as $k => $chunk) {

                //$read = Mage::getSingleton('core/resource')->getConnection('core_read');
                $result=$readconnection->query('SELECT * FROM '.$resource->getTableName('catalog_product_entity_varchar').' where entity_id = '.$chunk.' and attribute_id = '. $att_id.'');
                $row = $result->fetch();

               
                if(count($row)==1)
                {
                         $insert_query="insert into " . $resource->getTableName('catalog_product_entity_varchar') . "  values (NULL,'".$entityTypeId."','".$att_id."','0','".$chunk."','".$status_code."')";
                       
                         $writeConnection->query($insert_query);
                }
                else
                {
                    $query =  "Update " . $resource->getTableName('catalog_product_entity_varchar') . " Set value = '" . $status_code . "' where entity_id = ".$chunk. " and attribute_id = ".$att_id;
                    $writeConnection->query($query);
                }
                
            } 
            /*foreach ($chunk_data as $k => $chunk) {
                for($i=0;count($chunk)>$i;$i++){
                    if(trim($chunk[$i])!=""){
                        $model="";
                        $model=Mage::getModel('catalog/product')->load(trim($chunk[$i]));
                        $model->setData('jet_product_status',$status_code);
                        $model->save();
                    }
                }
                
            }*/

        }

    }

    public function getupdatedStatus($profileId = false)
    {

		$this->getProductByStatus('Under Jet Review', 'under_jet_review', $profileId);
        $this->getProductByStatus('Missing Listing Data', 'missing_listing_data', $profileId);
        $this->getProductByStatus('Unauthorized', 'unauthorized', $profileId);
        $this->getProductByStatus('Excluded', 'excluded', $profileId);
        $this->getProductByStatus('Available for Purchase', 'available_for_purchase', $profileId);
        $this->getProductByStatus('Archived', 'archived', $profileId);

	}


    /**
     * @save Jet Category information validation
     */
	/*
    public function jetCatInfocheck($observer)
    {
       $observer = $observer->getEvent()->getCategory();
        if (count($observer) > 0) {
            $is_jet_category = $observer->getIsJetCategory();
            $jet_category_id = $observer->getJetCategoryId();

            if ($observer->getId()) {

                if ($observer->getData('name') != NULL && $is_jet_category == '1' && (!is_numeric($jet_category_id) || $jet_category_id == null || $jet_category_id <= 0)) {

                    throw new Exception("Fail to Save Jet Category.If you chosen Yes Is Jet Category Information then  please enter the Jet Category Id! ");
                }
            }
        }
    }
	*/



    /*
     * observer for clearing jet token after saving new Jet Config details
     */

    public function clearToken(Varien_Event_Observer $observer)
    {
        $data = $observer->getEvent()->getData();
        //$setup = new Mage_Core_Model_Resource_Setup();
        //$setup->deleteConfigData('jetcom/token');
        $body = '{}';
        $data = new Mage_Core_Model_Config();
        $data->saveConfig('jetcom/token', $body, 'default', 0);

    }

    public function jetProductDelete($observer)
    {
        $product = "";
        $product_available = false;

        $checkStatus= Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_option');
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == 'simple') {
            $sku = $product->getSku();

            $profileProduct = Mage::getModel('jet/profileproducts')->loadByField('product_id', $product->getId());
            if($profileProduct && $profileProduct->getId()) {
                $product_available = true;
            }
            if ($product_available) {
                $data = "";
                $response = '';
                $arr = array();
                $arr['is_archived'] = true;
                $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $sku . '/status/archive', json_encode($arr));
                $response = json_decode($data);
            }
        }
        if ($product->isConfigurable()) {
                $simple_collection = Mage::getModel('catalog/product_type_configurable')->setProduct($product)
                    ->getUsedProductCollection()
                    ->addAttributeToSelect('sku')
                    ->addFilterByRequiredOptions();
                foreach ($simple_collection  as $_item) {
                    if ($_item->getData('type_id') == 'simple') {
                        $arr = array();
                        $arr['is_archived'] = true;
                        if (!$checkStatus) {
                            $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $_item->getSku() . '/status/archive', json_encode($arr));
                            $response = json_decode($data);
                            break;
                        } else {
                            $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $_item->getSku() . '/status/archive', json_encode($arr));
                            $response = json_decode($data);
                        }
                    }
                }
        }
       
    }

    function array_diff_assoc_recursive($array1, $array2) {
        $difference=array();
        foreach($array1 as $key => $value) {
            if( is_array($value) ) {
                if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                    if( !empty($new_diff) )
                        $difference[$key] = $new_diff;
                }
            } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }

/* Archive product if sku is changed
 * */
    public function jetProductSaveBefore($observer)
    {
        /*$prod = Mage::helper('jet')->getProductDetail('test_red_prod');
        print_r($prod);
        $prod = Mage::helper('jet')->getProductDetail('testGreen');
        print_r($prod);
        die;*/


        $product = $observer->getProduct();
        if ($product->hasDataChanges()) {

       $auto_sync_enable = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_product_auto_sync');
        $product = $observer->getProduct();
        if($auto_sync_enable == 1)
        {

            if ($product->getTypeId() == 'simple')
             {
                $product_available = true;
                $id = $product->getId();
                $current_sku = $product->getSku();
                $previous_sku = Mage::getModel('catalog/product')->load($id)->getSku();

                 $profileProduct = Mage::getModel('jet/profileproducts')->loadByField('product_id', $id);
                 if($profileProduct && $profileProduct->getId()) {
                     $product_available = true;
                 }

                if ($product_available) 
                {
                     if($current_sku!= $previous_sku)
                        {
                        $data = "";
                        $response = '';
                        $arr = array();
                        $arr['is_archived'] = true;
                        $data = Mage::helper('jet')->CPutRequest('/merchant-skus/' . $previous_sku . '/status/archive', json_encode($arr));
                        $response = json_decode($data);
                        }
                }
            }

        }
        $product->setData('jet_product_validation', 'not_validated');
        if($product->getTypeId() == "simple"){
            $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
            foreach ($parentIds as $id){
                $product =  Mage::getModel('catalog/product')->load($id);
                $product->setData('jet_product_validation','not_validated');
                $product->getResource()->saveAttribute($product,'jet_product_validation');
            }
        }


        $product->setData('jet_product_validation_error', null);
        }
    }



    public function saveJetOrder($jetId){
        $saveJetorder=Mage::getModel('jet/jetorder')->load($jetId);
        $saveJetorder->setData('status','cancelled');
        $saveJetorder->save();
    }


	public function updateInvcron(){
      
        $batch_data = Mage::getModel('jet/jetcron');
        $batch_count = $batch_data->getCollection()->getData();
        if(count($batch_count) == 0)
            {
                $response = Mage::helper('jet')->CGetRequest('/merchant-skus?offset=0&limit=10000');
                $result = json_decode($response,true);

                $SKU = array();

                if(is_array($result['sku_urls']) && isset($result['sku_urls']) && count($result['sku_urls'])>0)
                {
                    foreach ($result['sku_urls'] as $sku) 
                    {
                        $temp_skus = explode('merchant-skus/',$sku);
                        $SKU[] = $temp_skus[1];
                    }
                }
                if(count($SKU)>1)
                {
                    $chunk_size = Mage::getStoreConfig('jet_options/ced_cron/jet_cronsize');
                    if($chunk_size == ''){
                        $chunk_size = 1000;
                    }
                $sku_chunks = array_chunk($SKU,$chunk_size);

                foreach ($sku_chunks as $key => $value) 
                {
                    $batch_data = Mage::getModel('jet/jetcron');
                    $batch_data->setEvent('jet_invupdate');
                    $batch_data->setSkus(json_encode($value));
                    $batch_data->setStatus('pending');
                    $batch_data->save();
                }
                }
                
                
                
            }
        else
        { 
          foreach ($batch_count as $key => $value) {
                $SKU = json_decode($value['skus']);
                $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku');

            
        $collection->addFieldToFilter('sku', array('in'=>$SKU));
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')){
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
             $collection->joinField('is_in_stock',
                'cataloginventory/stock_item',
                'is_in_stock',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        $website = Mage::app()->getWebsite();
       
        Mage::Helper('jet/jet')->createuploadDir();
        $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
        
        
        $Inventory  =array();
        $merchantNode = array();
        $merchantNode1 = array();
        foreach ($collection as $change){ 
            $temp_inv = array();
            $node =array();
            $node['fulfillment_node_id']="$fullfillmentnodeid";
            $node['quantity']=(int)$change->getQty();
            //custom code added
            if($change->getQty() == 0 || $change->getQty() < 0 || $change->getIsInStock() == 0) 
            {
                $merchantNode1[$change->getSku()]=array('is_archived'=>true);
            }
            else
            {   
                $merchantNode[$change->getSku()]=array('is_archived'=>false);
            }
            //custom code end

            $temp_inv[$change->getSku()]['fulfillment_nodes'][]=$node;
            $Inventory = $Inventory+$temp_inv;
        }

        if(count($Inventory)>0){
            $tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
            $tokendata = json_decode($tokenresponse);

            $inventoryPath = Mage::helper('jet')->createJsonFile("Inventory", $Inventory);
            $arr = array();
            $arr = explode(DS, $inventoryPath);
            $sku_file_name =  end($arr);
            $inventoryPath=$inventoryPath.'.gz';
            $reponse = Mage::helper('jet')->uploadFile($inventoryPath,$tokendata->url);
            $postFields='{"url":"'.$tokendata->url.'","file_type":"Inventory","file_name":"'.$sku_file_name.'"}';
            $responseinventry = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
        }
        if(count($merchantNode)>0)
        {
            $tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
            $tokendata = json_decode($tokenresponse);
            $tokenurl=$tokendata->url;
            $merchantSkuPath = Mage::helper('jet')->createJsonFile("unArchieveSKUs", $merchantNode);
            $sku_file_name = explode(DS, $merchantSkuPath);
            $sku_file_name = end($sku_file_name);
            $merchantSkuPath=$merchantSkuPath.'.gz';
            $reponse = Mage::helper('jet')->uploadFile($merchantSkuPath,$tokenurl);
            $postFields='{"url":"'.$tokenurl.'","file_type":"Archive","file_name":"'.$sku_file_name.'"}';
            $response = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
            $data2  = json_decode($response);
        }
        if(count($merchantNode1)>0)
        {
            $tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
            $tokendata = json_decode($tokenresponse);
            $tokenurl=$tokendata->url;
            $merchantSkuPath = Mage::helper('jet')->createJsonFile("ArchieveSKUs", $merchantNode1);
            $sku_file_name = explode(DS, $merchantSkuPath);
            $sku_file_name = end($sku_file_name);
            $merchantSkuPath=$merchantSkuPath.'.gz';
            $reponse = Mage::helper('jet')->uploadFile($merchantSkuPath,$tokenurl);
            $postFields='{"url":"'.$tokenurl.'","file_type":"Archive","file_name":"'.$sku_file_name.'"}';
            $response = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
            $data2  = json_decode($response);
        }
                $batch_id = $value['id'];
                Mage::getModel('jet/jetcron')->load($batch_id)->delete();
                break;

            }  
        }
        return $this;
    }

    public function updatePricecron(){
        Mage::Helper('jet/jet')->createuploadDir();

        $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');

        $resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName('jet/jetcron');

        //profile products
        $collection = Mage::getModel('jet/profileproducts')->getCollection();
        $ids = $collection->getColumnValues('product_id');

        if(count($ids)>0){
            $goupload = true;
        }


        $data = array();
        if($goupload){

            $data = $collection->getData();

            $batch_data = (array_chunk($ids, 5000));

            foreach($batch_data as $data){

                $collection = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter('entity_id', array('in'=>$data))
                    ->addAttributeToFilter('type_id', array('in' => array('simple','configurable')))
                    ->addAttributeToFilter('visibility', 4);

                $Price  =array();
                foreach($collection as $product){
                   
                    
                    if($product->getTypeId()=='configurable'){
                         


                         $send_parent_price = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_price');
                         $childPrice = '';
                         
                         if($send_parent_price == 1)
                         {
                            $childPrice = Mage::helper('jet/jet')->getChildPrice($product->getId());
                         }
                         

                        $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
                           
                               foreach ($childProducts as $chp) {
                                $sku = $chp->getSku();
                               
                                    $temp_inv = array();
                                    $node =array();
                                    $node['fulfillment_node_id']="$fullfillmentnodeid";
                                    
                                   if($childPrice!='')
                                   {
                                        $node['price']=$childPrice[$sku];

                                   }
                                   else
                                   {
                                    $node['price'] =  Mage::helper('jet/jet')->getJetPrice($chp);
                                   }
                                   
                                   
                                    $temp_inv[$sku]['fulfillment_nodes'][]=$node;

                                    $Price = Mage::Helper('jet/jet')->Jarray_merge($temp_inv,$Price);
                                    
                                }
                            
                               
                         

                    }else{
                        $temp_inv = array();
                        $node =array();
                        $node['fulfillment_node_id']="$fullfillmentnodeid";
                        $product_price =  Mage::helper('jet/jet')->getJetPrice($product);
                        $node['price']=$product_price;
                        $temp_inv[$product['sku']]['fulfillment_nodes'][]=$node;

                        $Price = Mage::Helper('jet/jet')->Jarray_merge($temp_inv,$Price);
                    }
                }
                
                if(count($Price)>0){
                    $tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
                    $tokendata = json_decode($tokenresponse);

                    $pricePath = Mage::helper('jet')->createJsonFile("Price", $Price);

                     $arr = array();
                    $arr = explode(DS, $pricePath);
                    $sku_file_name =  end($arr);
                    $pricePath=$pricePath.'.gz';
                    

                    $reponse = Mage::helper('jet')->uploadFile($pricePath,$tokendata->url);
                    $postFields='{"url":"'.$tokendata->url.'","file_type":"Price","file_name":"'.$sku_file_name.'"}';

                    $responseinventry = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
                    //$invetrydata = json_decode($responseinventry);
                    //if($invetrydata->status == 'Acknowledged'){echo $invetrydata->status;}
                    $Price = array();
                }

            }

        }

    }



    public function  catalogInventoryStockItemSaveAfter($observer){

        $oldValue = (int)$observer->getData('item')->getOrigData('qty');
        $newValue = (int)$observer->getData('item')->getData('qty');
        $isInStock = (int)$observer->getData('item')->getData('is_in_stock');
        //if out of stock then set value to 0
        if(!$isInStock)
            $newValue = 0;

        if($oldValue==$newValue)
            return $this;

        $productId = (int)$observer->getData('item')->getData('product_id');

        $this->setProductChange($productId, $oldValue, $newValue);
        return $this;
    }

    public function  catalogInventoryStockItemSaveAfterOrder($observer){
        $items = $observer->getQuote()->getAllItems();
        foreach ($items as $item){
           $productId =  $item->getData('product_id');
            $oldValue = '';
            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);

            $newValue = 0;
            if($stock->getIsInStock())
                $newValue = $stock->getQty();

            $this->setProductChange($productId, $oldValue, $newValue);
        }
    }

    public function  catalogInventoryStockItemSaveAfterCreditmemo($observer){

        $creditmemo = $observer->getEvent()->getCreditmemo();
        $items = array();
        foreach ($creditmemo->getAllItems() as $item) {
            /* @var $item Mage_Sales_Model_Order_Creditmemo_Item */
            $return = false;
            if ($item->hasBackToStock()) {
                if ($item->getBackToStock() && $item->getQty()) {
                    $return = true;
                }
            } elseif (Mage::helper('cataloginventory')->isAutoReturnEnabled()) {
                $return = true;
            }
            if ($return) {
                $parentOrderId = $item->getOrderItem()->getParentItemId();
                /* @var $parentItem Mage_Sales_Model_Order_Creditmemo_Item */
                $parentItem = $parentOrderId ? $creditmemo->getItemByOrderId($parentOrderId) : false;
                $qty = $parentItem ? ($parentItem->getQty() * $item->getQty()) : $item->getQty();
                if (isset($items[$item->getProductId()])) {
                    $items[$item->getProductId()]['qty'] += $qty;
                } else {
                    $items[$item->getProductId()] = array(
                        'qty'  => $qty,
                        'item' => null,
                    );
                }
            }
        }

        foreach ($items as $productId => $item){
            $oldValue = '';
            $orderedQty = $item['qty'];
            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            $newValue = 0;
            if($stock->getIsInStock())
                $newValue = $stock->getQty()+$orderedQty;


            $this->setProductChange($productId, $oldValue, $newValue);
        }
        Mage::getSingleton('cataloginventory/stock')->revertProductsSale($items);

    }

    public function setProductChange($productId, $oldValue='', $newValue=''){
        if ($productId <= 0) {
            return $this;
        }

        $profileProduct = Mage::getModel('jet/profileproducts')->loadByField('product_id', $productId);
        if($profileProduct && $profileProduct->getId()) {
            $model = Mage::getModel('jet/productchange');
            $collection = $model->getCollection()->addFieldToFilter('product_id', $productId);

            if (count($collection) > 0) {
                $model->load($collection->getFirstItem()->getId());
            } else {
                $model->setProductId($productId);
            }

            $model->setOldValue($oldValue);
            $model->setNewValue($newValue);
            $model->setAction(Ced_Jet_Model_Productchange::ACTION_UPDATE);
            $model->setCronType(Ced_Jet_Model_Productchange::CRON_TYPE_INVENTORY);
            $model->save();
        }
        return $this;
    }


    public function  setStandardIdentifireElement($observer){

        $form = $observer->getEvent()->getForm();


        $standardIdentifier = $form->getElement('standard_identifier');
        if ($standardIdentifier) {
            $standardIdentifier->setRenderer(
                Mage::app()->getLayout()->createBlock('jet/adminhtml_catalog_product_edit_tab_standard_identifier')
            );
        }



        return $this;
    }
}