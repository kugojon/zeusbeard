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

class Ced_Jet_Adminhtml_JetproductController extends Ced_Jet_Controller_Adminhtml_MainController
{

    protected function _isAllowed()
    {
        return true;
    }

    public function clearallAction()
    {


        $file = new Varien_Io_File();
        $jetUploadPath = Mage::getBaseDir('var').DS.'jetupload';
        $result =  $file->rmdir($jetUploadPath, true);
        $file->mkdir($jetUploadPath, 0777, true);
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');

        $query = 'TRUNCATE TABLE '.$resource->getTableName('jet/fileinfo').'';
        $writeConnection->query($query);




        Mage::getSingleton('adminhtml/session')->addSuccess('Rejected batch File Log cleared.');

        $this->_redirect('*/*/rejected');
    }

    public function resubmitAction()
    {


        $jfile_id = $this->getRequest()->getPost('id');

        $loadfile = Mage::getModel('jet/fileinfo')->load($jfile_id);
        $products = $loadfile->getMagentoBatchInfo();

        //$loadfile->setStatus('Resubmit Requested')->save();
        $loadfile->delete();

        Mage::getSingleton('adminhtml/session')->addSuccess('Inventory File submission successfully done.');
        $this->_redirect("adminhtml/adminhtml_jetajax/massimport", array("product_ids"=>$products));
    }

    public function newAction()
    {
        Mage::getModel('jet/observer')->updateProduct();
        $this->_redirect('*/*/rejected');
    }

    public function mfeedsAction()
    {
        $baseDir = Mage::getBaseDir();
        $fileIo = new Varien_Io_File;
        $dir = $baseDir . DS . 'var' . DS . 'jetupload' . DS;
        $collection="";
        $collection= new Varien_Data_Collection(); 
        if (is_dir($dir)){
          if ($dh = opendir($dir)){
            while (($file = readdir($dh)) !== false){
                if($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'json')
                {
                                $thing_1 = new Varien_Object();
                                $thing_1->setName($file);
                                $thing_1->setCreatedAt(date("Y-m-d H:i:s", filemtime($dir.$file)));
                                $thing_1->setContent(@file_get_contents($dir.$file));
                                $collection->addItem($thing_1);
                    //echo "filename:" . $file . "<br>";
                }
            }

            closedir($dh);
          }
        }

        Mage::getSingleton('adminhtml/session')->setData('mfeeds_collection', $collection);

        $this->loadLayout();
        $this->renderLayout();
    }
public function gridfilterAction()
{
        $this->loadLayout();
              $this->getResponse()->setBody(
                  $this->getLayout()->createBlock('jet/adminhtml_mfeeds_grid')->toHtml()
              );
}
public function livearchieveAction()
{

        $sku = $this->getRequest()->getPost('sku');
        try
            {
                $result = Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($sku));
                    $response=json_decode($result, true);

                    if($response['status'] == 'Archived' || $response['is_archived']==true){
                        $data = array('sucess' => $response['status']);
                        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
                    }else {
                        $data2=Mage::helper('jet')->CPutRequest('/merchant-skus/'.rawurlencode($sku).'/status/archive', json_encode(array('is_archived'=>true)));
                        //$productLoad =  Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
                        //$productLoad->setJetProductStatus('archived')->save();
                            $data = array('sucess' => 'Product is successfully archived');
                        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
                    }
        }catch (Exception $e)
            {
                $data = array('error' => $e);
                        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
        }
        

        
}
    public function Unarchprocess($productLoad)
    {

        $fullfillmentnodeid=Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');

        $sku=$productLoad->getSku();
        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productLoad);

        $node1 = array();
        $inventory =array();
        $qty= ((int)$stock->getQty())>0 ? (int)$stock->getQty() : 0;

        $node1['fulfillment_node_id']="$fullfillmentnodeid";
        $node1['quantity']=$qty;
        $inventory['fulfillment_nodes'][]=$node1;


        $data1=Mage::helper('jet')->CPutRequest('/merchant-skus/'.rawurlencode($sku).'/status/archive', json_encode(array('is_archived'=>false)));

        $inventry=Mage::helper('jet')->CPatchRequest('/merchant-skus/'.rawurlencode($sku).'/inventory', json_encode($inventory));

        $result=Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($sku).'');

        $response=json_decode($result);

        $code = false;
        if(($response->status!='') || ( $response->is_archived ==false)){
            if($response->status=='Available for Purchase'){
                $code = 'available_for_purchase';
            }else if($response->status=='Archived'){
                $code = 'archived';
            } else if($response->status=='Missing Listing Data'){
                $code = 'missing_listing_data';
            }else if($response->status=='Under Jet Review'){
                $code = 'under_jet_review';
            }else if($response->status=='Excluded'){
                $code = 'excluded';
            }else if($response->status=='Unauthorized'){
                $code = 'unauthorized';
            }
        }

        return $code;
    }

public function liveunarchieveAction()
{
        $sku = $this->getRequest()->getPost('sku');
    
            try
            {
                $product = Mage::getModel('catalog/product')->getIdBySku($sku);
                $productLoad = Mage::getModel('catalog/product')->load($product);
                $code = $this->Unarchprocess($productLoad);
                $data = array('sucess' => 'Product is successfully unarchived');
                        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
            }catch (Exception $e)
            {
                $data = array('error' => $e->getMessage());
                        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
            }
        
        
}




    public function directapiuploadAction()
    {
        $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
        
        if(empty($fullfillmentnodeid) || $fullfillmentnodeid=='' || $fullfillmentnodeid== null){
            $data = array('error' => 'Enter fullfillmentnode id in Jet Configuration');
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
        }

        Mage::Helper('jet/jet')->createuploadDir();

        $fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
        if(empty($fullfillmentnodeid) || $fullfillmentnodeid=='' || $fullfillmentnodeid== null){
            $data = array('error' => 'Enter fullfillmentnode id form Jet Configuration');
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
        }

        $pid = $this->getRequest()->getPost('entity_id');
        $result=array();
        $node=array();
        $inventory=array();
        $price=array();
        $relationship = array();

        

        

        
        $prod_image = Mage::getModel('catalog/product')->load($pid);
         $parent_prod_id = '';
        $send_parent_image = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_image');
        $send_parent_price = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_price');
        $send_parent_name = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_name');
        $send_parent_desc = Mage::getStoreConfig('jet_options/ced_jetproductedit/jet_config_product_desc');
        $childPrice = '';
        $parent_image = '';
        $parent_name = '';
        $parent_desc = '';
        if($prod_image instanceof Mage_Catalog_Model_Product)
            {
                if($prod_image->getTypeId() == "configurable")
                    {
                        $parent_prod_id = $prod_image->getId();
                        if($send_parent_image == 1)
                            {
                                $parent_image = Mage::getModel('catalog/product_media_config')->getMediaUrl($prod_image->getImage());
                        }

                            if($send_parent_name == 1)
                            {
                                $name_mapping_attr = Mage::getStoreConfig('jet_options/productinfo_map/jtitle');
                                if(trim($name_mapping_attr) && $prod_image->getData($name_mapping_attr)!="")
                                {
                                    $parent_name = $prod_image->getData($name_mapping_attr);
                                }
                                else
                                {
                                    $parent_name = $prod_image->getName();
                                }
                            }

                            if($send_parent_desc == 1)
                            {
                                $desc_mapping_attr = Mage::getStoreConfig('jet_options/productinfo_map/jdescription');
                                if(trim($desc_mapping_attr) && $prod_image->getData($desc_mapping_attr)!="")
                                {
                                    $parent_desc = $prod_image->getData($desc_mapping_attr);
                                }
                                else
                                {
                                    $parent_desc = $prod_image->getDescription();
                                }
                            }

                            if($send_parent_price == 1)
                            {
                                $childPrice = Mage::helper('jet/jet')->getChildPrice($pid);
                            }
                }
        }

        //check if the product is valid
        if($prod_image->getData('jet_product_validation') != 'valid'){
            $message =  Mage::helper('jet')->validateProduct($prod_image->getId(), $prod_image, $parent_image, $parent_prod_id, $parent_name, $parent_desc);
            if(isset($message['error'])){
                $data = array('error' => 'Product is not validated, Current status: '.$prod_image->getData('jet_product_validation'));
                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
            }
        }


       
        
        
        
        $resultData = Mage::helper('jet')->createProduct($pid, $childPrice, $parent_image, $parent_prod_id, $parent_name, $parent_desc);


        if($resultData['type']=='error'){
            $data = array('error' => $resultData['data']);
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
        }

        $sku_array = array_keys($resultData["merchantsku"]);
        $sku = trim($sku_array[0]);
            if($resultData)
            {
                    $result = $resultData['merchantsku'];
                    $price =  $resultData['price'];
                    $inventory =  $resultData['inventory'];
                    if(isset($resultData['relationship']))
                    $relationship = $resultData['relationship'];
            }

            $upload_file = false;
            $t=time();
                

                if(!empty($result) && count($result)>0){
                    $merchantSkuPath = Mage::helper('jet')->prepareJsonFile("MerchantSKUs", $result);
                }
                
                if(!empty($price) && count($price)>0){
                    $pricePath = Mage::helper('jet')->prepareJsonFile("Price", $price);
                }
                
                if(!empty($inventory) && count($inventory)>0){
                    $inventoryPath = Mage::helper('jet')->prepareJsonFile("Inventory", $inventory);
                }

            $api_res_mer_sku = "";
            $api_res_price = "";
            $api_res_inventry = "";


            if($merchantSkuPath)
            {
                    $zz = json_decode($merchantSkuPath);
                    foreach ($zz as $key => $value) {
                        $response = Mage::helper('jet')->CPutRequest('/merchant-skus/'.trim($key), json_encode($value));

                $api_res_mer_sku  = json_decode($response);
                if(isset($api_res_mer_sku->Message))
                {
                    $data = array('error' => 'Please check API User/API Secret/Fulfillment Node Id Under Jet->Configuration.');
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));    
                }

                if(isset($api_res_mer_sku->errors))
                {
                    $data = array('error' => $api_res_mer_sku->errors[0]);
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));    
                }
                    }
                
                
                    
                    if(!empty($relationship) && count($relationship)>0){
                        $str="";
                        foreach($relationship as $key=>$relval){
                            $json_data = Mage::helper('jet')->Varitionfix(json_encode($relval), count($relval['variation_refinements']));
                            $res = Mage::helper('jet')
                                ->CPutRequest('/merchant-skus/'.rawurlencode($key).'/variation', $json_data);
                            if($res){
                                $res1  = json_decode($res);
                                if($res1 && count($res1->errors)>0){
                                    $str=$str."Error(s) in Relationship for sku : ".$key;
                                    foreach($res1->errors as $er){
                                        $str=$str."<br/>".$er;
                                    }

                                    $str=$str."<br/>";
                                }
                            }
                        }

                        if($str !=""){
                            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($str));
                        }
                    }
            }    
                
            if($pricePath){
                $zz = json_decode($pricePath);
                foreach ($zz as $key => $value) {
                        $response = Mage::helper('jet')->CPutRequest('/merchant-skus/'.trim($key).'/price', json_encode($value));
                $api_res_price  = json_decode($response);
                if(isset($api_res_price->errors))
                {
                    $data = array('error' => $api_res_price->errors[0]);
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));    
                }
                }
            }
                

            if($inventoryPath){
                $zz = json_decode($inventoryPath);
                foreach ($zz as $key => $value) {
                $response = Mage::helper('jet')->CPatchRequest('/merchant-skus/'.trim($key).'/inventory', json_encode($value));
                $api_res_inventry  = json_decode($response);
                if(isset($api_res_inventry->errors))
                {
                    $data = array('error' => $api_res_inventry->errors[0]);
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));    
                }
                }
            }

            
            if($api_res_mer_sku == null && $api_res_price == null && $api_res_inventry == null)
            {
            if($resultData['type']=='success')
            {
                if(isset($resultData['data']))
                    {
                        $data = array('sucess' => $resultData['data']);
                }
                    else
                    {
                        $data = array('sucess' => 'null');
                    }

                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
            }
            }else{
                if($resultData['type']=='error')
                {
                    $data = array('error' => $resultData['data']);
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
                }
            }

            unset($result);
            unset($price);
            unset($inventory);
            unset($relationship);        

        //$this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct');

    }


    /*
	* Action created for showing rejected Files
	*/
    public function rejectedAction()
    {
        $this->loadLayout();
        if(!Mage::helper('jet')->isDebugMode()){
            $configUrl  = $this->getUrl('adminhtml/system_config/edit/section/jet_options');
            Mage::getSingleton('adminhtml/session')->addError('Please enable debug mode from system <a href="'.$configUrl.'">Configuration</a> link.');
        }

        $this->renderLayout();
    }
    /*
	* Action created for showing live jet products
	*/
    public function liveproductsAction()
    {

        $raw_encode = rawurlencode('Available for Purchase');
        $response = Mage::helper('jet')->CGetRequest('/portal/merchantskus?from=0&size=50000&statuses='.$raw_encode);
        $result = json_decode($response, true);
        $collection="";
        $collection= new Varien_Data_Collection(); 
        foreach ($result['merchant_skus'] as $key => $value) {
        $thing_1="";
                        $thing_1 = new Varien_Object();
                        $thing_1->setSku($value['merchant_sku']);
                        $thing_1->setName($value['product_title']);
                        $collection->addItem($thing_1);
        }

        Mage::getSingleton('adminhtml/session')->setData('live_product_collection', $collection);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('jet/adminhtml_rejected_grid')->toHtml()
        );
    }


    public function jerrorDetailsAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('jet/fileinfo');
        if ($id) {
            $model->load($id);
            //if acknowledge state then update status
            if($model->getStatus() == 'Acknowledged')
                $model = Mage::helper('jet')->updateLogFileStatus($model);

            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jet')->__('Choosen Record is Not Found!'));
                $this->_redirect('adminhtml/adminhtml_jetrequest/rejcted');
            }
        }

        Mage::register('errorfile_collection', $model);

        $this->loadLayout();
        $this->renderLayout();
    }



    public function massDeleteAction()
    {
            $success_count=0;
            if(sizeof($this->getRequest()->getParam('error_ids'))>0){
                $errorIds = $this->getRequest()->getParam('error_ids');
                foreach($errorIds as $errorid){
                    $error = Mage::getModel('jet/fileinfo')->load($errorid);
                    $error->delete();
                    $success_count++;
                }
            }

            if($success_count>0){
                Mage::getSingleton('adminhtml/session')->addSuccess("$success_count record(s) successfully deleted.");
            }else{
                    Mage::getSingleton('adminhtml/session')->addNotice("No record(s) deleted.");
            }

            $this->_redirect('*/*/rejected');
    }
    public function getpriceAction()
    {
        $sku = $this->getRequest()->getPost('sku');
        $response = Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($sku).'/price');
        $result = json_decode($response, true);
        $data = array('success' => $result['price']);
                        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
    }
    public function getqtyAction()
    {
        $sku = $this->getRequest()->getPost('sku');
        $response = Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($sku).'/inventory');
        $result = json_decode($response, true);
        $data = array('success' => $result['fulfillment_nodes'][0]['quantity']);
                        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
    }
    public function getsalesdataAction()
    {
        $sku = $this->getRequest()->getPost('sku');
        $response = Mage::helper('jet')->CGetRequest('/merchant-skus/'.rawurlencode($sku).'/salesdata');

        $result = json_decode($response, true);

        if($result == NULL)
        {
            $message = 'Jet Sales Data API Is Down';
        
        $data = array('message' =>  $message);
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
        }

        if(isset($result['sales_rank']['level_0']) && isset($result['units_sold']['last_30_days']))
        {
             $message = 'Your best offer : </br>Item Price = '.$result['my_best_offer'][0]['item_price'].' & Shipping Price = '.$result['my_best_offer'][0]['shipping_price'].' </br>Marketplace best offer : </br>Item Price = '.$result['best_marketplace_offer'][0]['item_price'].' & Shipping Price = '.$result['best_marketplace_offer'][0]['shipping_price'].' </br>Rank Of Product : '.$result['sales_rank']['level_0'].' </br>Units Sold In last 30 days : '.$result['units_sold']['last_30_days'];
        }
        else
        {
             $message = 'Your best offer : </br>Item Price = '.$result['my_best_offer'][0]['item_price'].' & Shipping Price = '.$result['my_best_offer'][0]['shipping_price'].' </br>Marketplace best offer : </br>Item Price = '.$result['best_marketplace_offer'][0]['item_price'].' & Shipping Price = '.$result['best_marketplace_offer'][0]['shipping_price'];
        }
       
        
        $data = array('message' =>  $message);
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
    }
    


}
