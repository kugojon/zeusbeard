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
  
class Ced_Jet_Adminhtml_JetajaxController extends Mage_Adminhtml_Controller_Action
{

    protected $_bulk_upload_batch = 50;
    protected $_bulk_archive_batch = 500;
    protected $_bulk_unarchive_batch = 500;
    protected $_bulk_invprice_batch = 50;
    protected $_isDebugMode = false;
    protected $_sync_product_status = 1;
    protected $_jet_validate_product = 1;
 protected function _isAllowed()
 {
        return true;
 }

    public function errordetailsAction()
    {
                if($this->getRequest()->getParam('id')){
                            $product_id=$this->getRequest()->getParam('id');
                            $this->getResponse()->setBody(
                                $this->getLayout()
                                ->createBlock('core/template')
                                ->setTemplate("ced/jet/jeterror.phtml")
                                ->setData('id', $product_id)
                                ->toHtml()
                            );
                }
    }

    public function massimportAction()
    {
        $data = $this->getRequest()->getParam('product');
        $profileId = $this->getRequest()->getParam('profile_id');

        //check for the Resubmit action by the error log action
        if(!$data){
            $data = $this->getRequest()->getParam('product_ids');
            $data = explode(',', $data);
        }

        if ($data) {
            Mage::Helper('jet/jet')->createuploadDir();

            $productids = (array_chunk($data, $this->_bulk_upload_batch));
            Mage::getSingleton('adminhtml/session')->setProductChunks($productids);
            $this->loadLayout();
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError('No Product Selected.');
            $this->_redirect('*/adminhtml_jetrequest/uploadproduct', array('profile_id' => $profileId));
        }
    }

    public function massarchivedAction()
    {
        $data = $this->getRequest()->getParam('product');
        $profileId = $this->getRequest()->getParam('profile_id');
        if ($data) {
            Mage::Helper('jet/jet')->createuploadDir();
            
            $productids = (array_chunk($data, $this->_bulk_archive_batch));
            Mage::getSingleton('adminhtml/session')->setProductArcChunks($productids);
            $this->loadLayout();
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError('No Product Selected.');
            $this->_redirect('*/adminhtml_jetrequest/uploadproduct', array('profile_id' => $profileId));
        }
    }

    /**
     * Import product pne by one
     */
    public function startUploadAction()
    {
        $message=array();
        $message['error']="";
        $message['success']="";
        $commaseperatedskus='';
        $commaseperatedskus1="";
        $commaseperatedids ="";

        $this->_isDebugMode = Mage::helper('jet')->isDebugMode();
        $helper = Mage::helper('jet');
        $helper->initBatcherror();
        $key = $this->getRequest()->getParam('index');
        $api_dat =array();
        $api_dat = Mage::getSingleton('adminhtml/session')->getProductChunks();
        $index = $key + 1;

        if(count($api_dat) <= $index){
            Mage::getSingleton('adminhtml/session')->unsProductChunks();
        }

        if(isset($api_dat[$key])){
                    $product_ids= array();
                    $error_msg = '';
                    $product_ids= $api_dat[$key];
                    $fullfillmentnodeid ="";
                    $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
                    if(empty($fullfillmentnodeid) || $fullfillmentnodeid=='' || $fullfillmentnodeid== null){
                        $message['error']=$message['error'].'Enter fullfillmentnode id in Jet Configuration.';
                        //echo json_encode($message);
                        //return;
                        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));
                    }

                    $result=array();
                     $node=array();
                    $inventory=array();
                    $price=array();
                    $relationship = array();

                    foreach($product_ids as $pid){
                        $model=Mage::getModel('catalog/product')->load($pid);

                        if($commaseperatedskus==""){
                                $commaseperatedids = $pid; 
                                $commaseperatedskus=$model->getSku();
                                $commaseperatedskus1=$model->getSku();
                        }else{
                                $commaseperatedids = $commaseperatedids.','.$pid; 
                                $commaseperatedskus=$commaseperatedskus." , ".$model->getSku();
                                $commaseperatedskus1=$commaseperatedskus1.",".$model->getSku();
                        }

        $parent_prod_id = '';
        $send_parent_image = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_image');
        $send_parent_price = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_price');
        $send_parent_name = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_name');
        $send_parent_desc = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_desc');
        $childPrice = '';
        $parent_image = '';
        $parent_name = '';
        $parent_desc = '';
        if($model instanceof Mage_Catalog_Model_Product)
            {
                if($model->getTypeId() == "configurable")
                    {
                        $parent_prod_id = $model->getId();
                        if($send_parent_image == 1)
                            {
                                $parent_image = Mage::getModel('catalog/product_media_config')->getMediaUrl($model->getImage());
                        }

                            if($send_parent_name == 1)
                            {
                                $name_mapping_attr = Mage::getStoreConfig('jet_options/productinfo_map/jtitle');
                                if(trim($name_mapping_attr) && $model->getData($name_mapping_attr)!="")
                                {
                                    $parent_name = $model->getData($name_mapping_attr);
                                }
                                else
                                {
                                    $parent_name = $model->getName();
                                }
                            }

                            if($send_parent_desc == 1)
                            {
                                $desc_mapping_attr = Mage::getStoreConfig('jet_options/productinfo_map/jdescription');
                                if(trim($desc_mapping_attr) && $model->getData($desc_mapping_attr)!="")
                                {
                                    $parent_desc = $model->getData($desc_mapping_attr);
                                }
                                else
                                {
                                    $parent_desc = $model->getDescription();
                                }
                            }

                            if($send_parent_price == 1)
                            {
                                $childPrice = Mage::helper('jet/jet')->getChildPrice($pid);
                            }
                }
        }
                
                        /*----batch manage start-----*/
                        $helper->initBatchErrorForProduct($model->getId(), $index);

                        //check if product is not valid
                        if($model->getData('jet_product_validation') != 'valid'){
                            $message =  $helper->validateProduct($model->getId(), $model, $parent_image, $parent_prod_id, $parent_name, $parent_desc);

                            if(isset($message['error'])){
                                $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
                                $batcherror[$pid]['error']="Product validation issue: ".$model->getData('jet_product_validation');
                                $batcherror[$pid]['sku']=$model->getSku();
                                $batcherror[$pid]['batch_num']=$index;
                                Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);

                                continue;
                            }
                        }



                        /*----batch manage end-----*/
                        

        
                        

                        if($resultData = $helper->createProductOnJet($pid, $childPrice, $parent_image, $parent_prod_id, $parent_name, $parent_desc)){
                        if(isset($resultData['merchantsku']))
                            $result = Mage::helper('jet/jet')->Jarray_merge($result, $resultData['merchantsku']);
                        if(isset($resultData['price']))
                            $price = Mage::helper('jet/jet')->Jarray_merge($price, $resultData['price']);
                        if(isset($resultData['inventory']))
                            $inventory =  Mage::helper('jet/jet')->Jarray_merge($inventory, $resultData['inventory']);
                        if(isset($resultData['relationship']))
                        {
                            /*foreach($resultData['relationship'] as $key=>$relval)
							{
								$json_data = Mage::helper('jet')->Varitionfix(json_encode($relval), count($relval['variation_refinements']));*/
                                //print_r(json_decode($json_data,true));die();
                                $relationship = Mage::helper('jet/jet')->Jarray_merge($relationship, $resultData['relationship']);
                            //}
                        }
                        
                            $batcherror=array();
                            $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
                            $error_msg="Error occured in Batch $index :";
                            $msg="";
                            foreach($batcherror as $key=>$val){
                                    if($batcherror[$key]['error'] !=""){
                                            $msg=$msg."<br/>Error in Product Sku (".$batcherror[$key]['sku'].") :  ".$batcherror[$key]['error'];
                                    }
                            }

                            if($msg !=""){
                                    $message['error']=$error_msg.$msg;
                            }
                        }

                        if(isset($resultData['type']) && $resultData['type']=='error'){
                            $error_msg = $resultData['data'];
                        }
                    }

                    $upload_file = false;
                    $t=time();
                    if(!empty($result) && count($result)>0){
                        $merchantSkuPath = $helper->createJsonFile("MerchantSKUs", $result);
                        $sku_file_name=  explode(DS, $merchantSkuPath);
                        $sku_file_name = end($sku_file_name);
                        $upload_file = true;
                    }

                    if(!empty($price) && count($price)>0){
                        $pricePath = $helper->createJsonFile("Price", $price);
                        $price_file_name=explode(DS, $pricePath);
                        $price_file_name = end($price_file_name);
                    }

                    if(!empty($inventory) && count($inventory)>0){
                        $inventoryPath =$helper->createJsonFile("Inventory", $inventory);
                        $inventory_file_name=explode(DS, $inventoryPath);
                        $inventory_file_name = end($inventory_file_name);
                    }

                    if(!empty($relationship) && count($relationship)>0){
                        $relationshipPath =$helper->createJsonFile("Relationship", $relationship);
                        $relationship_file_name=explode(DS, $relationshipPath);
                        $relationship_file_name = end($relationship_file_name);
                    }

                    if($upload_file==false){ 
                        $batcherror=array();
                        $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
                        $error_msg = "Error occured in Batch $index :" . $error_msg;
                        $message['error']=$error_msg;
                        $msg="";
                        foreach($batcherror as $key=>$val){
                                    if($batcherror[$key]['error'] !=""){
                                            $msg=$msg."<br/>Error in Product Sku (".$batcherror[$key]['sku'].") :  ".$batcherror[$key]['error'];
                                    }
                        }

                        if($msg !=""){
                                    $message['error'] = $message['error'].$msg;
                        }

                        $message['error'] = $message['error']."<br/>Some Product informtion was incomplete so they are not prepared for upload.";
                        $message['error'] = $message['error']."<br/>Product sku(s) that are not uploaded :- $commaseperatedskus";
                        
                        try{
                                Mage::helper('jet')->saveBatchData($index);
                        }catch(Exception $e){
                            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));
                        }

                        //echo json_encode($message);
                        //return;
                        
                        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));
                    }

                    $merchantSkuPath =    $merchantSkuPath.".gz";
                    $pricePath =$pricePath.".gz";
                    $inventoryPath =$inventoryPath.".gz";
                    $relationshipPath =$relationshipPath.".gz";
                    if(fopen($merchantSkuPath, "r")!=false){
                        $response =$helper->CGetRequest('/files/uploadToken');
                        $data = json_decode($response);
                        $fileid=$data->jet_file_id;
                        $tokenurl=$data->url;
                        if($this->_isDebugMode) {
                            $text = array('magento_batch_info' => $commaseperatedids, 'jet_file_id' => $fileid, 'token_url' => $tokenurl, 'file_name' => $sku_file_name, 'file_type' => "MerchantSKUs", 'status' => 'unprocessed');
                            $model = Mage::getModel('jet/fileinfo')->addData($text);
                            $model->save();
                            $currentid = $model->getId();
                        }

                        $reponse = $helper->uploadFile($merchantSkuPath, $data->url);
                        $postFields='{"url":"'.$data->url.'","file_type":"MerchantSKUs","file_name":"'.$sku_file_name.'"}';
                        $response = $helper->CPostRequest('/files/uploaded', $postFields);
                        $data2  = json_decode($response);
                        
                        if($data2->status=='Acknowledged'){
                            if($this->_isDebugMode) {
                                $update = array('status' => 'Acknowledged');
                                $model = Mage::getModel('jet/fileinfo')->load($currentid)->addData($update);
                                $model->setId($currentid)->save();
                            }
                        } 
                    } 
                    
                    if(fopen($pricePath, "r")!=false){
                        $response = Mage::helper('jet')->CGetRequest('/files/uploadToken');
                        $data = json_decode($response);
                        $fileid=$data->jet_file_id;
                        $tokenurl=$data->url;
                        if($this->_isDebugMode) {
                            $text = array('magento_batch_info' => $commaseperatedids, 'jet_file_id' => $fileid, 'token_url' => $tokenurl, 'file_name' => $price_file_name, 'file_type' => "Price", 'status' => 'unprocessed');
                            $model = Mage::getModel('jet/fileinfo')->addData($text);
                            $model->save();
                            $currentid = $model->getId();
                        }

                        $reponse = Mage::helper('jet')->uploadFile($pricePath, $data->url);
                        $postFields='{"url":"'.$data->url.'","file_type":"Price","file_name":"'.$price_file_name.'"}';
                        $responseprice = Mage::helper('jet')->CPostRequest('/files/uploaded', $postFields);
                        $pricedata  = json_decode($responseprice);
                        if($pricedata->status=='Acknowledged' && $this->_isDebugMode){
                            $update=array('status'=>'Acknowledged');
                            $model = Mage::getModel('jet/fileinfo')->load($currentid)->addData($update);
                            $model->setId($currentid)->save();
                        }
                    }
                    
                    if(fopen($inventoryPath, "r")!=false){
                        $response = Mage::helper('jet')->CGetRequest('/files/uploadToken');
                        $data = json_decode($response);
                        $fileid=$data->jet_file_id;
                        $tokenurl=$data->url;
                        if($this->_isDebugMode) {
                            $text = array('magento_batch_info' => $commaseperatedids, 'jet_file_id' => $fileid, 'token_url' => $tokenurl, 'file_name' => $inventory_file_name, 'file_type' => "Inventory", 'status' => 'unprocessed');
                            $model = Mage::getModel('jet/fileinfo')->addData($text);
                            $model->save();
                            $currentid = $model->getId();
                        }

                        $reponse = Mage::helper('jet')->uploadFile($inventoryPath, $data->url);
                        $postFields='{"url":"'.$data->url.'","file_type":"Inventory","file_name":"'.$inventory_file_name.'"}';
                        $responseinventry = Mage::helper('jet')->CPostRequest('/files/uploaded', $postFields);
                        $invetrydata=json_decode($responseinventry);
                        if($invetrydata->status=='Acknowledged' && $this->_isDebugMode){
                            $update=array('status'=>'Acknowledged');
                            $model = Mage::getModel('jet/fileinfo')->load($currentid)->addData($update);
                            $model->setId($currentid)->save();
                        }
                    }

                    //relation code start
                    if(fopen($relationshipPath, "r")!=false){
                        $response = Mage::helper('jet')->CGetRequest('/files/uploadToken');
                        $data = json_decode($response);
                        $fileid=$data->jet_file_id;
                        $tokenurl=$data->url;
                        if($this->_isDebugMode) {
                            $text = array('magento_batch_info' => $commaseperatedids, 'jet_file_id' => $fileid, 'token_url' => $tokenurl, 'file_name' => $relationship_file_name, 'file_type' => "Variation", 'status' => 'unprocessed');
                            $model = Mage::getModel('jet/fileinfo')->addData($text);
                            $model->save();
                            $currentid = $model->getId();
                        }

                        $reponse = Mage::helper('jet')->uploadFile($relationshipPath, $data->url);
                        $postFields='{"url":"'.$data->url.'","file_type":"Variation","file_name":"'.$relationship_file_name.'"}';
                        $responsevariation = Mage::helper('jet')->CPostRequest('/files/uploaded', $postFields);
                        $variationdata  = json_decode($responsevariation);
                        if($variationdata->status=='Acknowledged' && $this->_isDebugMode){
                            $update=array('status'=>'Acknowledged');
                            $model = Mage::getModel('jet/fileinfo')->load($currentid)->addData($update);
                            $model->setId($currentid)->save();
                        }
                    }

                    //relation code end
                    $batcherror=array();
                    $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
                    $error_msg1="";
                    $error_msg1="Error occured in Batch $index :";
                    $message['error']=$error_msg1;
                    $msg1="";
                    $errored_skus=array();
                    foreach($batcherror as $key=>$val){
                                if($batcherror[$key]['error'] !=""){
                                        $errored_skus[]=$batcherror[$key]['sku'];
                                        $msg1=$msg1."<br/>Error in Product Sku (".$batcherror[$key]['sku'].") :  ".$batcherror[$key]['error'];
                                }
                    }

                    if($msg1 !=""){
                        $message['error']=$message['error'].$msg1;
                    }else{
                         $message['error']="NO Error occured in Batch $index";
                    } 
                    
                    $exploded_arr=array();
                    if($commaseperatedskus1 !=""){
                            $exploded_arr=explode(',', $commaseperatedskus1);
                    }

                    $successfull_arr=array();
                    $successfull_arr=array_diff($exploded_arr, $errored_skus);
                    
                    $imploded_str="";
                    $imploded_str=implode(' , ', $successfull_arr);

                    if(isset($message['success']))
                        $message['success']=$message['success']."Batch $index products Upload Request Send Successfully on Jet.com.Contained product skus are : $commaseperatedskus.Successfully uploaded product skus are : $imploded_str .";
                    
                    //echo json_encode($message);
                   // return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));
                    
                            
                    unset($result);
                    unset($price);
                    unset($inventory);
                    unset($relationship);
        }
        else{
            $message['error']=$message['error']."Batch $index included Product(s) data not found.";
            //echo json_encode($message);
            //return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));
        }

        try{
            Mage::helper('jet')->saveBatchData($index);
        }catch(Exception $e){
            return;
        }

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));
    }

    public function startArchieveAction()
    {
        $message=array();
        $message['error']="";
        $message['success']="";
        $commaseperatedskus='';
        $commaseperatedskus1="";
        $commp_ids ="";
        $this->_isDebugMode = Mage::helper('jet')->isDebugMode();

        $helper=Mage::helper('jet');
        $helper->initBatcherror();
        $key = $this->getRequest()->getParam('index');
        $api_dat =array();
        $api_dat = Mage::getSingleton('adminhtml/session')->getProductArcChunks();
        $index = $key + 1;
        if(count($api_dat) <= $index){ 
            Mage::getSingleton('adminhtml/session')->unsProductArcChunks();
        }

        if(isset($api_dat[$key])) {
            $product_ids = array();
            $product_ids = $api_dat[$key];
            $fullfillmentnodeid = "";
            $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
            if (empty($fullfillmentnodeid) || $fullfillmentnodeid == '' || $fullfillmentnodeid == null) {
                $message['error'] = $message['error'] . 'Enter fullfillmentnode id in Jet Configuration.';
                //echo $helper->__('Enter fullfillmentnode id in Jet Configuration.');
                //echo json_encode($message);
                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));
            }
        }

        $merchantNode=array();
        $productdata = Mage::getModel('catalog/product');
        $commaseperatedids = implode(",", $product_ids);
        foreach($product_ids as $pid){
           // $commaseperatedids = implode(",", $pid);
            $productLoad=$productdata->load($pid);
            
            if($productLoad->isConfigurable()){
                $simple_collection = Mage::getModel('catalog/product_type_configurable')->setProduct($productLoad)
                    ->getUsedProductCollection()
                    ->addAttributeToSelect('sku')
                    ->addFilterByRequiredOptions();
                    
                foreach ($simple_collection  as $_item)
                {
                    if($commaseperatedskus==""){
                        $commaseperatedskus=$_item->getSku();
                        $commp_ids = $_item->getId(); 
                    }else{
                        $commp_ids = $commp_ids.",".$_item->getId(); 
                        $commaseperatedskus=$commaseperatedskus." , ".$_item->getSku();
                    }

                    $merchantNode[$_item->getSku()]=array('is_archived'=>true);
                }
            }
            else{
                $sku = $productLoad->getData('sku');
                $merchantNode[$sku]=array('is_archived'=>true);

                if($commaseperatedskus==""){
                    $commaseperatedskus = $sku;
                    $commp_ids = $productLoad->getId(); 
                }else{
                    $commp_ids = $commp_ids.",".$productLoad->getId(); 
                    $commaseperatedskus = $commaseperatedskus.",".$sku;
                }
            }
        }

        $tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
        $tokendata = json_decode($tokenresponse);
        $fileid=$tokendata->jet_file_id;
        $tokenurl=$tokendata->url;
        
        $merchantSkuPath = Mage::helper('jet')->createJsonFile("ArchieveSKUs", $merchantNode);
        $sku_file_name = explode(DS, $merchantSkuPath);
        $sku_file_name = end($sku_file_name);
        $merchantSkuPath=$merchantSkuPath.'.gz';

        if($this->_isDebugMode) {
            $text = array('magento_batch_info' => $commp_ids, 'jet_file_id' => $fileid, 'token_url' => $tokenurl, 'file_name' => $sku_file_name, 'file_type' => "Archive", 'status' => 'unprocessed');
            $model = Mage::getModel('jet/fileinfo')->addData($text);
            $model->save();
            $currentid = $model->getId();
        }
            
        $reponse = Mage::helper('jet')->uploadFile($merchantSkuPath, $tokenurl);

        $postFields='{"url":"'.$tokenurl.'","file_type":"Archive","file_name":"'.$sku_file_name.'"}';
        $response = Mage::helper('jet')->CPostRequest('/files/uploaded', $postFields);
        $data2  = json_decode($response);

        if($data2->status=='Acknowledged'){
            if($this->_isDebugMode) {
                $update = array('status' => 'Acknowledged');
                $model1 = Mage::getModel('jet/fileinfo')->load($currentid);
                $model1->addData($update);
                $model1->save();
            }

            $message['success']="Batch $index products Archive Request Send Successfully on Jet.com.Contained product skus are : $commaseperatedskus.";
        }else{
            $message['error']="Batch $index products Archive Request rejected on Jet.com";
        }
        
        //echo json_encode($message);
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));
    }

    public function startUnarchieveAction()
    {
        
        $message=array();
        $message['error']="";
        $message['success']="";
        $commaseperatedskus='';
        $commaseperatedskus1="";
        $commp_ids ="";
        $this->_isDebugMode = Mage::helper('jet')->isDebugMode();
        $helper=Mage::helper('jet');
        $helper->initBatcherror();
        $key = $this->getRequest()->getParam('index');
        $api_dat =array();
        $api_dat = Mage::getSingleton('adminhtml/session')->getProductUndoArcChunks();
        $index = $key + 1;
        if(count($api_dat) <= $index){ 
            Mage::getSingleton('adminhtml/session')->unsProductUndoArcChunks();
        }

        if(isset($api_dat[$key])) {
            $product_ids = array();
            $product_ids = $api_dat[$key];
            $fullfillmentnodeid = "";
            $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
            if (empty($fullfillmentnodeid) || $fullfillmentnodeid == '' || $fullfillmentnodeid == null) {
                $message['error'] = $message['error'] . 'Enter fullfillmentnode id in Jet Configuration.';
                //echo $helper->__('Enter fullfillmentnode id in Jet Configuration.');
                //echo json_encode($message);
                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));

                //return;
            }
        }

        $merchantNode=array();
        $Inventory=array();
        
        $productdata = Mage::getModel('catalog/product');
        $commaseperatedids = implode(",", $product_ids);
        foreach($product_ids as $pid){
//            $commaseperatedids = implode(",", $pid);
            $productLoad=$productdata->load($pid);
            
            if($productLoad->isConfigurable()){
                $simple_collection = Mage::getModel('catalog/product_type_configurable')->setProduct($productLoad)
                    ->getUsedProductCollection()
                    ->addAttributeToSelect('sku')
                    ->addFilterByRequiredOptions();
                    
                foreach ($simple_collection  as $_item)
                {
                    if($commaseperatedskus==""){
                        $commaseperatedskus=$_item->getSku();
                        $commp_ids = $_item->getId(); 
                    }else{
                        $commp_ids = $commp_ids.",".$_item->getId(); 
                        $commaseperatedskus=$commaseperatedskus." , ".$_item->getSku();
                    }

                    $merchantNode[$_item->getSku()]=array('is_archived'=>false);
                    $qty=(int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($_item)->getQty();
                    $qty = ($qty < 0) ? 0 : $qty; 
                    $temp = array();
                    $temp=array($_item->getSku() => array(
                                    'fulfillment_nodes'=>array(
                                         array('fulfillment_node_id'=>$fullfillmentnodeid
                                         ,'quantity'=>$qty))));
                                         
                    $Inventory = Mage::helper('jet/jet')->Jarray_merge($temp, $Inventory);
                }
            }
            else{
                $sku = $productLoad->getData('sku');
                $merchantNode[$sku]=array('is_archived'=>false);

                if($commaseperatedskus==""){
                    $commaseperatedskus = $sku;
                    $commp_ids = $productLoad->getId(); 
                }else{
                    $commp_ids = $commp_ids.",".$productLoad->getId(); 
                    $commaseperatedskus = $commaseperatedskus.",".$sku;
                }
                
                $temp = array();
                $qty=(int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($productLoad)->getQty();
                $qty = ($qty < 0) ? 0 : $qty; 
                
                $temp= array($sku => array('fulfillment_nodes' =>array(array('fulfillment_node_id' => $fullfillmentnodeid, 'quantity' => $qty))));
                
                $Inventory = Mage::helper('jet/jet')->Jarray_merge($temp, $Inventory);
            }
        }

        $tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
        $tokendata = json_decode($tokenresponse);
        $fileid=$tokendata->jet_file_id;
        $tokenurl=$tokendata->url;
        
        $merchantSkuPath = Mage::helper('jet')->createJsonFile("unArchieveSKUs", $merchantNode);
        $sku_file_name = explode(DS, $merchantSkuPath);
        $sku_file_name = end($sku_file_name);
        $merchantSkuPath=$merchantSkuPath.'.gz';

        if($this->_isDebugMode) {
            $text = array('magento_batch_info' => $commp_ids, 'jet_file_id' => $fileid, 'token_url' => $tokenurl, 'file_name' => $sku_file_name, 'file_type' => "Archive", 'status' => 'unprocessed');
            $model = Mage::getModel('jet/fileinfo')->addData($text);
            $model->save();
            $currentid = $model->getId();
        }
            
        $reponse = Mage::helper('jet')->uploadFile($merchantSkuPath, $tokenurl);

        $postFields='{"url":"'.$tokenurl.'","file_type":"Archive","file_name":"'.$sku_file_name.'"}';
        $response = Mage::helper('jet')->CPostRequest('/files/uploaded', $postFields);
        $data2  = json_decode($response);
        
        if($data2->status=='Acknowledged'){
            if($this->_isDebugMode) {
                $update = array('status' => 'Acknowledged');
                $model1 = Mage::getModel('jet/fileinfo')->load($currentid);
                $model1->addData($update);
                $model1->save();
            }

            if(count($Inventory)>0){        
                $tokenresponse2 = Mage::helper('jet')->CGetRequest('/files/uploadToken');
                $tokendata2 = json_decode($tokenresponse2);
                    
                $inventorypath = Mage::helper('jet')->createJsonFile("UncinventorySKUs", $Inventory);
                $inventory_file_name =  explode(DS, $inventorypath);
                $inventory_file_name =  end($inventory_file_name);
                $inventorypath = $inventorypath.'.gz';
                if($this->_isDebugMode) {
                    $text = array('magento_batch_info' => $commp_ids, 'jet_file_id' => $tokendata2->jet_file_id, 'token_url' => $tokendata2->url, 'file_name' => $inventory_file_name, 'file_type' => "Inventory", 'status' => 'unprocessed');
                    $model2 = Mage::getModel('jet/fileinfo')->addData($text);
                    $model2->save();
                    $currentinvid = $model2->getId();
                }
                
                $reponse = Mage::helper('jet')->uploadFile($inventorypath, $tokendata2->url);
                $postFields='{"url":"'.$tokendata2->url.'","file_type":"Inventory","file_name":"'.$inventory_file_name.'"}';
                $responseinventry = Mage::helper('jet')->CPostRequest('/files/uploaded', $postFields);
                
                $invetrydata=json_decode($responseinventry);
    
                if($invetrydata->status=='Acknowledged'){
                    if($this->_isDebugMode) {
                        $update = array('status' => 'Acknowledged');
                        $model = Mage::getModel('jet/fileinfo')->load($currentinvid)->addData($update);
                        $model->save();
                    }

                    $message['success']="Batch $index products Unarchive Request Sent Successfully on Jet.com. Contained product skus are : $commaseperatedskus.";
                }else{
                    $message['error']="Batch $index products Uarchive Request rejected on Jet.com";
                }
            }        
        }
        
        //echo json_encode($message);
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));


        //return;
    
    }
    
    public function archieveProduct($product,$sku)
    {
        $data['is_archived']=true;
        $result = Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($sku));
        $response=json_decode($result);
        $msg=array();

        if($response->status == 'Archived' || $response->is_archived ==true){
            $msg[0]="error";
            $msg[1]="product with sku &nbsp".$sku."&nbsp already archieved";
            return $msg;
        }else {
            $data1=Mage::helper('jet')->CPutRequest('/merchant-skus/'.$sku.'/status/archive', json_encode($data));
            $product->setData('jet_product_status', 'Archived')->save();
            $msg[0]="success";
            $msg[1]="product with sku &nbsp".$sku."&nbsp successfully archieved";
           return $msg;
        }
    }


    public function massunarchivedAction()
    {
        $data = $this->getRequest()->getParam('product');
        $profileId = $this->getRequest()->getParam('profile_id');
        if ($data) {
            Mage::Helper('jet/jet')->createuploadDir();
            
            $productids = (array_chunk($data, $this->_bulk_unarchive_batch));
            Mage::getSingleton('adminhtml/session')->setProductUndoArcChunks($productids);
            
            $this->loadLayout();
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError('No Product Selected.');
            $this->_redirect('*/adminhtml_jetrequest/uploadproduct/', array('profile_id' => $profileId));
        }
    }

     public function syncAction()
     {
     
        Mage::Helper('jet/jet')->createuploadDir();
        $profileId = $this->getRequest()->getParam('profile_id');

         $pids =array();
        $products = $this->getRequest()->getParam('product');
        if(count($products)>0){
            $pids =$products;
            $this->_bulk_invprice_batch = count($products);
        }else{
            $pids = Mage::getModel('jet/profileproducts')->getProfileProducts($profileId);
        }

        if(count($pids)>0){
                $productrows=array_chunk($pids, $this->_bulk_invprice_batch);
            Mage::getSingleton('adminhtml/session')->setSyncChunks($productrows);
            
            $this->loadLayout();
            $this->renderLayout();
        }else{
            Mage::getSingleton('core/session')->addError('No Product available in Upload Product list.');
            $this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct', array('profile_id'=> $profileId));
        }
     }

    public function beginInvPriceSynAction()
    {
        $message=array();
        $message['error']="";
        $message['success']="";
        //$commaseperatedskus='';
        $commaseperatedids  ='';
        $fullfillmentnodeid = "";
        $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
        
        if (empty($fullfillmentnodeid) || $fullfillmentnodeid == '' || $fullfillmentnodeid == null) {
            $message['error'] = $message['error'] . 'Enter fullfillmentnode id in Jet Configuration.';
            //echo json_encode($message);
            //return;
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));
        }

        $childPrice = false;
        $helper=Mage::helper('jet')->initBatcherror();
        $key = $this->getRequest()->getParam('index');
        
        $api_dat =array();
        $api_dat = Mage::getSingleton('adminhtml/session')->getSyncChunks();
        $index = $key + 1;
        if(count($api_dat) <= $index){
            Mage::getSingleton('adminhtml/session')->unsSyncChunks();
        }
        
        if(isset($api_dat[$key])) {
            $product_ids = array();
            $product_ids = $api_dat[$key];
        }
        
        $Inventory = array();
        $Price = array();
        
        
        foreach($product_ids as $pId){
            $commaseperatedids = $commaseperatedids .','.$pId;
            $productLoad = Mage::getModel('catalog/product')->load($pId);
            
            if(!$productLoad){
                return;
            }
            
           if($productLoad->isConfigurable()){ 
               $send_parent_price = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_price');
               $childPrice = '';
               if($send_parent_price == 1)
               {
                   $childPrice = Mage::helper('jet/jet')->getChildPrice($pId);
               }
               

                $ids=Mage::getResourceSingleton('catalog/product_type_configurable')->getChildrenIds($pId);

                if(isset($ids[0]) && count($ids[0])>0){
                    // load all children data
                    foreach ($ids[0]  as $prd)
                    {
                        $commaseperatedids = $commaseperatedids .','.$prd;
                        $_item =false;
                        $_item = Mage::getModel('catalog/product')->load($prd);
                        if($_item){
                            $node = array();    
                            $node1 = array();
                            
                            if(!array_key_exists($_item->getSku(), $Inventory)){
                                $qty = 0;
                                $qty=(int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($_item)->getQty();
                                $qty = ($qty < 0) ? 0 : $qty; 
                                $node1['fulfillment_node_id']="$fullfillmentnodeid";
                                $node1['quantity']=$qty;
                                
                                $Inventory[$_item->getSku()]['fulfillment_nodes'][]=$node1;
                                
                                $product_price =0;
                                
                                $product_price =  Mage::helper('jet/jet')->getJetPrice($_item);
                                $node['fulfillment_node_id']="$fullfillmentnodeid";
                                //custom coded added
                                $compt_price_config = Mage::getStoreConfig('jet_options/ced_jetreprice/active');

                                if($compt_price_config)
                                {
                                        if($childPrice!='')
                                        {
                                            $compt_price =  Mage::helper('jet/jet')->getComptPrice($childPrice[$_item->getSku()], $_item->getSku(), $_item);
                                        }
                                        else
                                        {
                                            $product_price =   Mage::helper('jet/jet')->getJetPrice($_item);
                                            $compt_price =  Mage::helper('jet/jet')->getComptPrice($product_price, $_item->getSku(), $_item);
                                        }                            
                                    
                                    $node['fulfillment_node_price'] = $compt_price;
                                    $Price[$_item->getSku()]['price'] = $compt_price;
                                }

                                else
                                {
                                    if($childPrice!='')
                                    {
                                        $node['fulfillment_node_price'] = $childPrice[$_item->getSku()];
                                        $Price[$_item->getSku()]['price'] = $childPrice[$_item->getSku()];
                                    }
                                    else
                                    {
                                        $product_price =   Mage::helper('jet/jet')->getJetPrice($_item);
                                        $node['fulfillment_node_price'] = $product_price;
                                        $Price[$_item->getSku()]['price'] = $product_price;
                                    }
                                }

                                //custom code end

                            
                                $Price[$_item->getSku()]['fulfillment_nodes'][]=$node;
                            }
                        }    
                    } 
                }
           }else{
                $node = array();    
                $node1 = array();
                
                $qty=(int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($productLoad)->getQty();
                $qty = ($qty < 0) ? 0 : $qty; 
                $node1['fulfillment_node_id']="$fullfillmentnodeid";
                $node1['quantity']=$qty;
                $Inventory[$productLoad->getSku()]['fulfillment_nodes'][]=$node1;
                
                $product_price =0;
                $product_price =   Mage::helper('jet/jet')->getJetPrice($productLoad);
                    
                $node['fulfillment_node_id']="$fullfillmentnodeid";


                //code start for compt price
                    $compt_price_config = Mage::getStoreConfig('jet_options/ced_jetreprice/active');

                    if($compt_price_config)
                    {
                            $compt_price =  Mage::helper('jet/jet')->getComptPrice($product_price, $productLoad->getSku(), $productLoad);
                        $node['fulfillment_node_price'] = $compt_price;
                        $price[$productLoad->getSku()]['price'] = $compt_price;
                    }
                    else
                    {
                            $node['fulfillment_node_price'] = $product_price;
                            $Price[$productLoad->getSku()]['price']=$product_price;
                    }

                    //custom code end
                
                $Price[$productLoad->getSku()]['fulfillment_nodes'][]=$node;
           }
        }
        
        if(count($Inventory)>0){
            $inventoryPath= "";    
            $inventoryPath = Mage::helper('jet')->createJsonFile("SyncInventory", $Inventory);
            $inventory_file_name=  explode(DS, $inventoryPath);
            $inventory_file_name=  end($inventory_file_name);
            $inventoryPath = $inventoryPath.'.gz';
            
            $tokenresponse = Mage::helper('jet')->CGetRequest('/files/uploadToken');
            $tokendata = json_decode($tokenresponse);
            
            $reponse = Mage::helper('jet')->uploadFile($inventoryPath, $tokendata->url);
            
            $postFields='{"url":"'.$tokendata->url.'","file_type":"Inventory","file_name":"'.$inventory_file_name.'"}';
            $responseInv = Mage::helper('jet')->CPostRequest('/files/uploaded', $postFields);
            
            $data2  = json_decode($responseInv);
    
            if($data2->status=='Acknowledged'){
                 $message['success']="Batch $index products Inventory Update Request Sent Successfully on Jet.com. Contained Product Ids are : $commaseperatedids.";
            }else{
                $message['error']="Batch $index products Inventory Update Request Rejected on Jet.com";
            }  
        }
        
        if(count($Price)>0){
            $price_Path ="";
            $price_file_name ="";
            
            $price_Path = Mage::helper('jet')->createJsonFile("SyncPrice", $Price);
            $price_file_name =  explode(DS, $price_Path);
            $price_file_name =  end($price_file_name);
            $price_Path = $price_Path.'.gz';
            
            $tokenresponse2 = Mage::helper('jet')->CGetRequest('/files/uploadToken');
            $tokendata2 = json_decode($tokenresponse2);
            
            $reponse = Mage::helper('jet')->uploadFile($price_Path, $tokendata2->url);
            $postFields='{"url":"'.$tokendata2->url.'","file_type":"Price","file_name":"'.$price_file_name.'"}';
            $responsePrice = Mage::helper('jet')->CPostRequest('/files/uploaded', $postFields);
            
            $pricedata=json_decode($responsePrice);
    
            if($pricedata->status=='Acknowledged'){
                $message['success1']="Batch $index products Price Update Request Sent Successfully on Jet.com. Contained Product Ids are : $commaseperatedids.";
            }else{
                $message['error1']="Batch $index products Price Update Request Rejected on Jet.com";
            }
        }
        
        //echo json_encode($message);
        //return;
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));


    }


    public  function syncProductStatusAction()
    {

        Mage::Helper('jet/jet')->createuploadDir();
        $profileId = $this->getRequest()->getParam('profile_id');
        $pids =array();
        $products = $this->getRequest()->getParam('product');
        /*if(count($products)>0){
           // $this->_bulk_invprice_batch = count($products);
        }else{
            $pids = Mage::getModel('jet/profileproducts')->getProfileProducts($profileId);
        }*/
        $pids =$products;


        if(count($pids)>0){
            $productrows=array_chunk($pids, $this->_sync_product_status);
            Mage::getSingleton('adminhtml/session')->setStatusSyncChunks($productrows);

            $this->loadLayout();
            $this->renderLayout();
        }else{
            Mage::getSingleton('core/session')->addError('No Product available in Upload Product list.');
            $this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct', array('profile_id'=> $profileId));
        }
    }

    public function beginProductStatusSyncAction()
    {
        $message=array();
        $message['error']="";
        $message['success']="";
        $notFound= true;


        $options = Mage::getModel('jet/source_productstatus')->getOptionArray();


        $helper=Mage::helper('jet')->initBatcherror();
        $key = $this->getRequest()->getParam('index');

        $api_dat =array();
        $api_dat = Mage::getSingleton('adminhtml/session')->getStatusSyncChunks();
        $index = $key + 1;
        if(count($api_dat) <= $index){
            Mage::getSingleton('adminhtml/session')->unsStatusSyncChunks();
        }

        if(isset($api_dat[$key])) {
            $product_ids = array();
            $product_ids = $api_dat[$key];
        }

        $sku = '';

        foreach($product_ids as $pId){
            $productLoad = Mage::getModel('catalog/product')->load($pId);

            $sku = $productLoad->getSku();

            if(!$productLoad){
                return;
            }


            if($productLoad->isConfigurable()){
                $childPrice = Mage::helper('jet/jet')->getChildPrice($pId);

                $ids=Mage::getResourceSingleton('catalog/product_type_configurable')->getChildrenIds($pId);

                if(isset($ids[0]) && count($ids[0])>0){
                    // load all children data
                    foreach ($ids[0]  as $prd)
                    {
                        $_item = Mage::getModel('catalog/product')->load($prd);
                        $csku = $_item->getSku();
                        $response_simple = Mage::helper('jet')->getProductDetail($csku);
                        $status = $response_simple['status'];
                        $value = array_search($status, $options);
                        $productLoad->setJetProductStatus($value);
                        $notFound =  false;
                        $message['success']='Product Sku "'.$sku.'" has been updated.';
                        break;
                    }
                }
            }else{
                $response_simple = Mage::helper('jet')->getProductDetail($sku);
                if($response_simple != null) {
                    $notFound =  false;
                    $status = $response_simple['status'];
                    $value = array_search($status, $options);
                    $productLoad->setJetProductStatus($value);
                    $message['success'] = 'Product Sku "' . $sku . '" has been updated.';
                }
            }
        }

        /*if($notFound)
            $message['error']='Product sku: "'.$sku.'" is not found on jet.com ';*/

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));




    }

    public  function validateProductsAction()
    {
        /*$product = Mage::getModel('catalog/product')->load(2);
        print_r($product->getData());die;*/
        Mage::Helper('jet/jet')->createuploadDir();
        $profileId = $this->getRequest()->getParam('profile_id');
        $pids =array();
        $products = $this->getRequest()->getParam('product');
        $pids =$products;


        if(count($pids)>0){
            $productrows=array_chunk($pids, $this->_jet_validate_product);
            Mage::getSingleton('adminhtml/session')->setValidateChunks($productrows);

            $this->loadLayout();
            $this->renderLayout();
        }else{
            Mage::getSingleton('core/session')->addError('No Product available To validate.');
            $this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct', array('profile_id'=> $profileId));
        }

    }

    public function beginValidateProductsAction()
    {
        $message=array();
        $message['error']="";
        $message['success']="";
        $notFound= true;

        $options = Mage::getModel('jet/source_productstatus')->getOptionArray();


        $helper=Mage::helper('jet')->initBatcherror();
        $key = $this->getRequest()->getParam('index');

        $api_dat =array();
        $api_dat = Mage::getSingleton('adminhtml/session')->getValidateChunks();
        $index = $key + 1;
        if(count($api_dat) <= $index){
            Mage::getSingleton('adminhtml/session')->unsValidateChunks();
        }

        if(isset($api_dat[$key])) {
            $product_ids = array();
            $product_ids = $api_dat[$key];
        }

        $sku = '';
        $helper = Mage::helper('jet');
        foreach($product_ids as $pId){
            $model = Mage::getModel('catalog/product')->load($pId);
            //custom code start
            $parent_prod_id = '';
        $send_parent_image = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_image');
        $send_parent_price = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_price');
        $send_parent_name = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_name');
        $send_parent_desc = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_desc');
        $childPrice = '';
        $parent_image = '';
        $parent_name = '';
        $parent_desc = '';
        if($model instanceof Mage_Catalog_Model_Product)
            {
                if($model->getTypeId() == "configurable")
                    {
                        $parent_prod_id = $model->getId();
                        if($send_parent_image == 1)
                            {
                                $parent_image = Mage::getModel('catalog/product_media_config')->getMediaUrl($model->getImage());
                        }

                            if($send_parent_name == 1)
                            {
                                $name_mapping_attr = Mage::getStoreConfig('jet_options/productinfo_map/jtitle');
                                if(trim($name_mapping_attr) && $model->getData($name_mapping_attr)!="")
                                {
                                    $parent_name = $model->getData($name_mapping_attr);
                                }
                                else
                                {
                                    $parent_name = $model->getName();
                                }
                            }

                            if($send_parent_desc == 1)
                            {
                                $desc_mapping_attr = Mage::getStoreConfig('jet_options/productinfo_map/jdescription');
                                if(trim($desc_mapping_attr) && $model->getData($desc_mapping_attr)!="")
                                {
                                    $parent_desc = $model->getData($desc_mapping_attr);
                                }
                                else
                                {
                                    $parent_desc = $model->getDescription();
                                }
                            }

                            if($send_parent_price == 1)
                            {
                                $childPrice = Mage::helper('jet/jet')->getChildPrice($pId);
                            }
                }
        }

            //custom code end
            $message =  $helper->validateProduct($pId, $model, $parent_image, $parent_prod_id, $parent_name, $parent_desc);
        }

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($message));
    }
}


