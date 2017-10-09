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
  
class Ced_Jet_Adminhtml_JeteditController extends Mage_Adminhtml_Controller_Action{

  protected function _isAllowed()
    {
        return true;
    }

    public function jetProductEditAction($config_id='',$childPrice='')
	{
        if($config_id == '')
        {
             $post_id = $this->getRequest()->getParam('id');
        }
       else
       {
            $post_id = $config_id;
       }
       if (empty($childPrice))
       {
            $childPrice = '';
       }

         $arr = array();
        $product = Mage::getModel('catalog/product')->load($post_id);

        $profile = Mage::helper('jet')->getProductProfile($post_id);
        if(isset($profile['profile_status']) && !$profile['profile_status']){
            Mage::getSingleton('adminhtml/session')->addError(' Profile '.$profile['profile_code'].' is disabled and please enable and try again.');
            $this->_redirectReferer();
            return;
        }




        if ($product->getTypeId() == 'simple') 
        {
           Mage::helper('jet')->updateonjetSync($product,$childPrice);
           if(count($arr)!=0)
           {    
              foreach ($arr as $value) 
               {
                   if($value!= $product->getSku())
                        {
                        Mage::getSingleton('adminhtml/session')->addSuccess("Product Data Successfully Sync With Jet");
                        $this->_redirectReferer();
                        }
               }  
           }
           else
           {
                 Mage::getSingleton('adminhtml/session')->addSuccess("Product Data Successfully Sync With Jet");
                        $this->_redirectReferer();
           }
               
        }else if($product->getTypeId()=='configurable'){
            $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
            $childPrice = Mage::helper('jet/jet')->getChildPrice($post_id);
            foreach($childProducts as $chp){
                $arr= $chp->getSku();
                $this->jetProductEditAction($chp->getId(),$childPrice);
                }
            Mage::getSingleton('adminhtml/session')->addSuccess("Product Data Successfully Sync With Jet");
            $this->_redirectReferer();
        }
    }


    public function enablevacationmodeAction()
    {
        $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
        $arr = array();
        $arr[0] = 'Available for Purchase';
        $arr[1] = 'Under Jet Review';
        foreach ($arr as $key => $value) {
          
       
        $raw_encode = rawurlencode($value);
        $response = Mage::helper('jet')->CGetRequest('/portal/merchantskus?from=0&size=5000&statuses='.$raw_encode);
        $result = json_decode($response,true);

        $SKU = array();

        if(is_array($result) && isset($result['merchant_skus']) && count($result['merchant_skus'])>0){
            foreach ($result['merchant_skus'] as $sku) {
                 $SKU[] = $sku['merchant_sku'];
            }
        }

            $Inventory  = array();
            foreach ($SKU as $sk) {

                                    $temp_inv = array();
                                    $node =array();
                                    $node['fulfillment_node_id']="$fullfillmentnodeid";
                                    $node['quantity']= 0;
                                    $temp_inv[$sk]['fulfillment_nodes'][]=$node;
                                    
                                    $Inventory = Mage::Helper('jet/jet')->Jarray_merge($temp_inv,$Inventory);
            }
            
            if(count($Inventory)>0){
                    $tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
                    $tokendata = json_decode($tokenresponse);

                    $inventoryPath = Mage::helper('jet')->createJsonFile( "Inventory", $Inventory);
                    $sku_file_name =  end(explode(DS, $inventoryPath));
                    $inventoryPath=$inventoryPath.'.gz';

                    $reponse = Mage::helper('jet')->uploadFile($inventoryPath,$tokendata->url);
                    $postFields='{"url":"'.$tokendata->url.'","file_type":"Inventory","file_name":"'.$sku_file_name.'"}';

                    $responseinventry = Mage::helper('jet')->CPostRequest('/files/uploaded',$postFields);
                    //$invetrydata = json_decode($responseinventry);
                    //if($invetrydata->status == 'Acknowledged'){echo $invetrydata->status;}
                    $Inventory = array();
                }

             }
             Mage::getSingleton('adminhtml/session')->addSuccess("Vacation Mode has been enabled successfully, Your product's quantity will be 0 on jet.com during this mode.You can disable Vacation mode any time.Happy Vacations.");
                $this->_redirectReferer();
        
    }
     public function disablevacationmodeAction()
    {
        Mage::getModel('jet/observer')->updateInvcron();
             Mage::getSingleton('adminhtml/session')->addSuccess("Vacation Mode has been disabled successfully, Your product's quantity will be same on jet.com which are in magento.You can enable Vacation mode any time.Thank You.");
                $this->_redirectReferer();
        
    }
    
}


