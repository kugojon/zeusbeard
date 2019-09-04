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

class Ced_Jet_Model_Wship
{
    public function jetShipment($order, $comments, $carrierData, $tracking)
    {         
        $orderid = $order->getIncrementId();
        $jetorder = Mage::getModel('jet/jetorder')->getCollection()->addFieldToFilter('magento_order_id', $orderid)->getFirstItem();
        $jetorderid = $jetorder->getData('merchant_order_id');
        $jet_order_primary_key =  $jetorder->getData('id');               

        if($jetorderid){           
            $error_reporting = array();                        
            $flag = true;
            $offset_end = Mage::helper('jet/jet')->getStandardOffsetUTC();
            if (empty($offset_end) || trim($offset_end) == '') {
                $offset = '.0000000-00:00';
            } else {
                $offset = '.0000000' . trim($offset_end);
            }

            $otaltime = (string)date("m/d/Y").' '.date("H:i:s");
            $date = DateTime::createFromFormat('m/d/Y H:i:s', $otaltime);

            $shipTodatetime = $date->getTimestamp();
            $Carr_pickdate = $shipTodatetime;
            $Exp_delivery = strtotime('+5 day', $shipTodatetime);

            $Ship_todate = date("Y-m-d", $shipTodatetime) . 'T' . date("H:i:s", $shipTodatetime) . $offset;
            $Exp_delivery = date("Y-m-d", $Exp_delivery) . 'T' . date("H:i:s", $Exp_delivery) . $offset;
            $Carr_pickdate = date("Y-m-d", $Carr_pickdate) . 'T' . date("H:i:s", $Carr_pickdate) . $offset;

            $id = $orderid;
            $carrierData = preg_split("[\|]", $carrierData);
            $shipStationcarrier = (string)$carrierData[0];           
                  

            $address1 = Mage::getStoreConfig('jet_options/ced_jetaddress/jet_address1');
            $address2 = Mage::getStoreConfig('jet_options/ced_jetaddress/jet_address2');
            $city = trim(Mage::getStoreConfig('jet_options/ced_jetaddress/jet_city'));
            $state = trim(Mage::getStoreConfig('jet_options/ced_jetaddress/jet_state'));
            $zip = trim(Mage::getStoreConfig('jet_options/ced_jetaddress/jet_zip'));

            if (trim($zip) == "") {
                $flag=false;
            }

            if (trim($state) == "") {
                $flag=false;
            }

            if (trim($city) == "") {
                $flag=false;
            }

            if (trim($address1) == "") {
                $flag=false;
            }  

            if($flag){
                $Arry_returnLoc = array('address1' => $address1,
                    'address2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'zip_code' => $zip
                );

                $shipment_arr = array();
                $rma = '';

                $jetHelper = Mage::helper('jet/jet');

                /* start loop for item data received */
                foreach ($order->getAllVisibleItems() as $item) {
                    $time = time() + ($item->getId() + 1);
                    $shp_id = implode("-", str_split($time, 3));


                    $ship_sku = $item->getSku();
                    $shipment_arr[] = array(
                        'merchant_sku' => $ship_sku,
                        'response_shipment_sku_quantity' => (int)$item->getQtyOrdered(),
                        'response_shipment_cancel_qty' => 0,
                        'RMA_number' => "$rma" ,
                        'days_to_return' => 30 ,
                        'return_location' => $Arry_returnLoc
                    );
                }                 

                $carrier = Mage::helper('jet/jet')->getJetShipCarrier($shipStationcarrier);
                $unique_random_number = $id.mt_rand(10, 10000);                
                $data_ship = array();
                $checkShipdata = false;
               
                foreach ($shipment_arr as $value) {
                    if (isset($value['response_shipment_sku_quantity']) && $value['response_shipment_sku_quantity']!=0) {
                       $checkShipdata = true;
                    }
                }
                
                 if ($checkShipdata) {
                    $data_ship['shipments'][] = array(
                    'alt_shipment_id' => $unique_random_number,
                    'shipment_tracking_number' => "$tracking",
                    'response_shipment_date' => $Ship_todate,
                    'response_shipment_method' => '',
                    'expected_delivery_date' => $Exp_delivery,
                    'ship_from_zip_code' => "$zip",
                    'carrier_pick_up_date' => $Carr_pickdate,
                    'carrier' => $carrier,
                    'shipment_items' => $shipment_arr
                );
                 }
                else
                {
                    $data_ship['shipments'][] = array(
                        'alt_shipment_id' => $unique_random_number,
                        'shipment_items' => $shipment_arr
                    );
                }

                if ($data_ship) {                    
                    $data = Mage::helper('jet')->CPutRequest('/orders/' . $jetorderid . '/shipped', json_encode($data_ship));
                    $responsedata = json_decode($data);
                    $jetmodel = Mage::getModel('jet/jetorder')->load($jet_order_primary_key);

                    if (($responsedata == NULL) || ($responsedata == "")) {
                        $jetmodel->setStatus('complete');
                        $jetmodel->setShipmentData(serialize($data_ship));
                        $jetmodel->save();
                    }
                    else{
                        $data = array('order_id'=> $orderid ,'jet_reference_id'=> $jetorderid ,'error'=>$responsedata->errors[0],'jet_shipment_status' => 'unshipped');
                        $jetAutoshipError = Mage::getSingleton('jet/autoship');
                        $jetAutoshipError->setData($data)->save();
                    }
                }
            }else{
                $data = array('order_id'=> $orderid ,'jet_reference_id'=> $jetorderid ,'error'=>'kindly set zip code , state , city and address from system configuration' , 'jet_shipment_status' => 'unshipped');
                $jetAutoshipError = Mage::getSingleton('jet/autoship');
                $jetAutoshipError->setData($data)->save();
            }
        }      
    } 
}