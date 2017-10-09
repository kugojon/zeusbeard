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
  
class Ced_Jet_Adminhtml_JetrequestController extends Ced_Jet_Controller_Adminhtml_MainController
{

	protected function _isAllowed()
    {
        return true;
    }
    public function newAction(){
        $profileId = $this->getRequest()->getParam('profile_id');
		Mage::getModel('jet/observer')->getupdatedStatus();
		$this->_redirect('adminhtml/adminhtml_jetrequest/uploadproduct', array('profile_id' => $profileId));
	}
	
	public function uploadproductAction(){


        if($profileId = $this->getRequest()->getParam('profile_id') ){
            $productIds = Mage::getModel('jet/profileproducts')->getProfileProducts($profileId);
            $model = Mage::getModel('jet/profile')->load($profileId);



            $msg = "";
            if(!$model->getProfileStatus()){
                $msg = "Profile is Inactive Please Activate profile and try again <br />";
            }
            if(count($productIds)==0){
                $msg .= "This profile does not have any product, Please associate product with this profile then again upload product.";
            }

            if($msg !=""){
                Mage::getSingleton('adminhtml/session')->addError($msg);
                $this->_redirectReferer();
                return;
            }

        }else{
            Mage::getSingleton('adminhtml/session')
                ->addError('No Profile Selected Please Choose Profile Then Upload Product');
            $this->_redirect('adminhtml/adminhtml_profile');
            return;
        }



        $this->loadLayout();
		$this->_setActiveMenu('jet/uploadproduct');
		$this->renderLayout();
		
	}
	
	public function uploadproductgridAction(){
		$this->loadLayout();
		$this->renderLayout();
		
	}
	public function liveproductgridAction(){
		
		$this->loadLayout();
		$this->renderLayout();
		
	}
	
	/*
	* Download Attribute csv for Creating Category
	*/
	public function downloadsampleAction(){
        
		$filename = 'Jet_Taxonomy.csv';
        $path = Mage::getBaseDir('var') .DS.'jetcsv'.DS.$filename;
		if (file_exists($path) && is_readable($path)) {
            
            $size = filesize($path);
            header('Content-Type: application/octet-stream');
            header('Content-Length: '.$size);
            header('Content-Disposition: attachment; filename='.$filename);
            header('Content-Transfer-Encoding: binary');
            
            $file = @ fopen($path, 'rb');
            if ($file) {
                fpassthru($file);
                exit;
            } else {
                echo "Error";
            }
        }  
	}
	/*
	* Download Attribute csv for mapping Attribute ID
	*/
	public function downloadattrAction(){
        
		$filename = 'Jet_Taxonomy_attribute.csv';
        $path = Mage::getBaseDir('var') .DS.'jetcsv'.DS.$filename;
		if (file_exists($path) && is_readable($path)) {
            
            $size = filesize($path);
            header('Content-Type: application/octet-stream');
            header('Content-Length: '.$size);
            header('Content-Disposition: attachment; filename='.$filename);
            header('Content-Transfer-Encoding: binary');
            
            $file = @ fopen($path, 'rb');
            if ($file) {
                fpassthru($file);
                exit;
            } else {
                echo "Error";
            }
        }  
	}
	
	
	public function productDetailsAction()
	{

		
		$id = $this->getRequest()->getParam('id');
		$profileId = $this->getRequest()->getParam('profile_id');
		$product = Mage::getModel('catalog/product')->load($id);

		$sku = $product->getSku();
		
		//$advanceProd = array("configurable", "bundle", "grouped");
		$advanceProd = array("configurable");
		if(in_array($product->getTypeId(),$advanceProd)){
			$sku = Mage::helper('jet')->getMainProductSku($product);
		}
		if($sku == FALSE)
		{
			Mage::getSingleton('adminhtml/session')
                   ->addError('Associated (child) products does not exist');

			$this->_redirect('*/*/uploadproduct', array('profile_id' => $profileId));
		}
		$productStatus = $product->getJetProductStatus();
		$response = Mage::helper('jet')->getProductDetail($sku);
		if(isset($response['Message']) && $response['Message'] == 'Authorization has been denied for this request.')
		{
			Mage::getSingleton('adminhtml/session')
                   ->addError('Please check API User/API Secret/Fulfillment Node Id Under Jet->Configuration.');

			$this->_redirect('*/*/uploadproduct', array('profile_id' => $profileId));
		}
		Mage::register('relationship', $response);

		if(is_array($response))
		{
			if($response['status']!=''){
				$code =false;
				
				if($response['status']=='Available For Purchase'){
					$code = 'available_for_purchase';
				}else if($response['status']=='Archived'){
					$code = 'archived';
				} else if($response['status']=='Missing Listing Data'){
					$code = 'missing_listing_data';
				}else if($response['status']=='Under Jet Review'){
					$code = 'under_jet_review';
				}else if($response['status']=='Excluded'){
				 	$code = 'excluded';
				}else if($response['status']=='Unauthorized'){
				 	$code = 'unauthorized';
				}else {
					$code =false;
				}
				
				if($code!=false){
					$product->setJetProductStatus($code);
					$product->save();	
				}
				
			}
			$return_data=array();
			if(isset($response['time_to_return'])){
				$return_data=array(
						'time_to_return'=>$response['time_to_return'],
						'return_shipping_methods'=>$response['return_shipping_methods'],
						'return_location_ids'=>$response['return_location_ids'],
				);
			}
			Mage::register('return_data',$return_data);
			$substatus='';
			if($response['sub_status']!=NULL && count($response['sub_status'])>0){
				$substatus= implode(',',$response['sub_status']);
				
			}
			$collectionData=array(
								'sku'=>$response['merchant_sku'],
								'title'=>$response['product_title'],
								'description'=>$response['product_description'],
								'merchant_id'=>$response['merchant_id'],
								'merchant_sku_id'=>$response['merchant_sku_id'],
								'multipack_quantity'=>$response['multipack_quantity'],
								'sku_last_update'=>$response['sku_last_update'],
								'inventory_last_update'=>$response['inventory_last_update'],
								'qty'=>$response['inventory_by_fulfillment_node'][0]['quantity'],
								'price'=>$response['price'],
								'status'=>($response['status']!='')? $response['status']: 'No status Response form Jet.com' ,
								'sub_status'=>($substatus!='')?$substatus: 'No Sub status from Jet',
								'fulfillment_price' =>$response['price_by_fulfillment_node'],
								'fulfillment_qty' =>$response['inventory_by_fulfillment_node'],
								'relationship'=>isset($response['relationship']) ? $response['relationship'] : "No relationship",
								'variation_refinements'=>(isset($response['variation_refinements']) && count($response['variation_refinements'])>0) ? implode(',',$response['variation_refinements']): "No variation Refinements" ,
								'main_image_url' =>isset($response['main_image_url']) ? $response['main_image_url']: '',
								'manufacturer' => isset($response['manufacturer'])?$response['manufacturer']:'',
								'safety_warning' => isset($response['safety_warning'])?$response['safety_warning'] : '',
								'brand' => $response['brand']
								);
								
			$collectionload = Mage::getModel('jet/jetshippingexcep')->getCollection()->addFieldToFilter('sku',$collectionData['sku']);
			
			foreach ($collectionload as $value) {
				$shippid=$value['id'];
				break;
			}
            $loadData = false;
			if($collectionload->count()>0)
			{
				$loadData=Mage::getModel('jet/jetshippingexcep')->load($shippid);
	
	       	}
	       	if (($collectionData['sku']!='' || $id == 0))
	       	{	
	       		 Mage::register('prod_data', $collectionData);
	             Mage::register('shipping_data',$loadData);
				 
	             $this->loadLayout();
	             $this->_setActiveMenu('jet/set_time');
	             $this->_addBreadcrumb('Product Manager', 'Product Manager');
	             $this->_addBreadcrumb('Product Description', 'Product Description');
	             $this->getLayout()->getBlock('head')
	                  ->setCanLoadExtJs(true);
	             $this->_addContent($this->getLayout()
	                  ->createBlock('jet/adminhtml_prod_edit'))
	                  ->_addLeft($this->getLayout()
	                  ->createBlock('jet/adminhtml_prod_edit_tabs')
	              );
	             $this->renderLayout();
	       	}
	    }
       	else
       	{
       		if($productStatus=='not_uploaded'){
       			Mage::getSingleton('adminhtml/session')
                   ->addError('Product not uploaded on Jet.com');
       		}
       		else
       		{
            	Mage::getSingleton('adminhtml/session')
                   ->addError('Either product just uploaded(just uploaded product status will be visible when jet.com processed finish Processing for that product) OR Product does not uploaded at jet.com yet.');
        	}
        	$this->_redirect('*/*/uploadproduct', array('profile_id' => $profileId));
        }
	}
	
	public function saveAction(){
		
		$dataRequest=$this->getRequest()->getParams();
		$profileId = $this->getRequest()->getParam('profile_id');
		$go_redirect =false;
		if($dataRequest)
		{
			try
			{

				$fullfillmentnodeid=Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
				$sku=$this->getRequest()->getPost('sku');
				
				if($this->getRequest()->getPost('shipping_override')){
						$chargeamount=$this->getRequest()->getPost('shipping_charge');
						$exceptiontype=$this->getRequest()->getPost('shipping_excep');
						$shippinglevel=$this->getRequest()->getPost('shipping_carrier');
						$shippingmethod=$this->getRequest()->getPost('shipping_method');
						$overridetype=$this->getRequest()->getPost('shipping_override');
						if($shippinglevel){
							$shipping=array();
							$shipping['fulfillment_nodes'][]=array('fulfillment_node_id'=>"$fullfillmentnodeid",
														'shipping_exceptions'=>array(
															array('service_level'=>$shippinglevel,
																  'override_type'=>$overridetype,
																  'shipping_charge_amount'=>(float)$chargeamount,			
																  'shipping_exception_type'=>$exceptiontype)));
						}
						else{
							$shipping=array();
							$shipping['fulfillment_nodes'][]=array('fulfillment_node_id'=>"$fullfillmentnodeid",
															'shipping_exceptions'=>array(
																array('shipping_method'=>trim($shippingmethod),
																	'override_type'=>$overridetype,
																	'shipping_charge_amount'=>(float)$chargeamount,
																	'shipping_exception_type'=>$exceptiontype)));
							
							}
						
						$data=Mage::helper('jet')->CPutRequest('/merchant-skus/'.rawurlencode($sku).'/shippingexception',json_encode($shipping));
						
						
						if($data==''){
							$shippingObj=Mage::getModel('jet/jetshippingexcep');
							$collectionload=$shippingObj->getCollection()->addFieldToFilter('sku',$sku);
							foreach ($collectionload as $value) {
								$id=$value['id'];
								break;
							}

							if($collectionload->count()>0){
								$shippingObj->load($id)
											->setData('sku',$sku)
											->setData('shipping_charge',$chargeamount)
											->setData('shipping_excep',$exceptiontype)
											->setData('shipping_carrier',$shippinglevel)
											->setData('shipping_method',$shippingmethod)
											->setData('shipping_override',$overridetype);
							}
							else{		
								$shippingObj->setData('sku',$sku)
											->setData('shipping_charge',$chargeamount)
											->setData('shipping_excep',$exceptiontype)
											->setData('shipping_carrier',$shippinglevel)
											->setData('shipping_method',$shippingmethod)
											->setData('shipping_override',$overridetype);
							} 

							$shippingObj->save();
							Mage::getSingleton('adminhtml/session')
		                                  ->addSuccess('Shipping Exception has been saved successfully');
							$go_redirect =true;			  
						}else{
							$error = json_decode($data, true);
							$msg = '';
							if(isset($error['errors']) && !empty($error['errors'])){
								$err_count = count($error['errors']);
								if($err_count>0){
									for($i=0; $i<=$err_count; $i++){
										$msg = $msg.$error['errors'][$i].'</br>';
									}
									Mage::getSingleton('adminhtml/session')
		                                  ->addError($msg);
								}else{
									Mage::getSingleton('adminhtml/session')
		                                  ->addError("There is an error in shipping exception processing");	
								}
							}else{
								Mage::getSingleton('adminhtml/session')
		                                  ->addError("There is an error in shipping exception processing");	
							}
							$go_redirect =false;	
						}
				}

				if($this->getRequest()->getParam('time_to_return')){
							$return_arr=array();
							$time_to_return='';
							$time_to_return=$this->getRequest()->getParam('time_to_return');
							if($time_to_return!=""  && trim($time_to_return)!=""){
								$return_arr['time_to_return']=(int)$time_to_return;
							}else{
										Mage::getSingleton('adminhtml/session')
                                  			->addError('Please enter correct Time to return.');
                                 		 $this->_redirect('*/*/productDetails',array('id' => $this->getRequest()->getParam('id')));
                						return;
							}
							$location_ids=array();
							$locations=array();
							if($this->getRequest()->getParam('locations')){
										$locations=$this->getRequest()->getParam('locations');
										if(count($locations['value'])>0){
													for($i=0;$i < count($locations['value']);$i++){
															if($locations['delete'][$i]==""){
																	if($locations['value'][$i]!=""  && trim($locations['value'][$i])!=""){
																			$location_ids[]=$locations['value'][$i];
																	}
																	
															}
													}
										}
							}
							if(count($location_ids)>0){
									$return_arr['return_location_ids']=$location_ids;
							}

							$ship_methods=array();
							$ship=array();
							if($this->getRequest()->getParam('ship_methods')){
										$ship=$this->getRequest()->getParam('ship_methods');
										if(count($ship['value'])>0){
													for($i=0;$i < count($ship['value']);$i++){
															if($ship['delete'][$i]==""){
																	if($ship['value'][$i]!="" && trim($ship['value'][$i])!=""){
																			$ship_methods[]=trim($ship['value'][$i]);
																	}
															}
													}
										}
							}
							if(count($ship_methods)>0){
									$return_arr['return_shipping_methods']=$ship_methods;
							}
							if(count($location_ids)<=0){
										 Mage::getSingleton('adminhtml/session')->addError('Please enter Return Location Ids.');
                                 		 $this->_redirect('*/*/productDetails',array('id' => $this->getRequest()->getParam('id')));
                						return;
							}
							if(count($ship_methods)<=0){
										 Mage::getSingleton('adminhtml/session')->addError('Please enter Return Shipping Methods.');
                                  		$this->_redirect('*/*/productDetails',array('id' => $this->getRequest()->getParam('id')));
                						return;
							}
							if(count($return_arr)>0){
									$url ='/merchant-skus/'.rawurlencode($sku).'/returnsexception';
									$data = Mage::helper('jet')->CPutRequest($url,json_encode($return_arr));
									
									if(!empty($data) || $data!=''){
												$data1="";
												$data1=json_decode($data);
												$error_str1='Return Exception Failed.<br/>';
												$error_str='';
												$j=0;

												foreach($data1->errors as $error){
															$string="";
															$title="";
															$time=false;
															if(strpos($error, 'return_shipping_methods')){
																	$title="Error in Return Shipping Methods :";
															}
															if(strpos($error, 'time_to_return')){
																	$title="Error in Time to Return :";
																	$time=true;
															}
															if(preg_match('/location/',$error)){
																	$title="Error in Return Location Ids :";

															}
															if(strpos($error, 'Path:')){
																	$string=substr($error,0,strpos($error, 'Path:'));
															}
															if($time && strpos($error, '30L')){
																	$string="Value should be 30 or fewer days.";
															}
															if($j>0){
																if($string!=""){
																		$error_str=$error_str.'<br/>'.$title.$string;
																}else{
																		$error_str=$error_str.'<br/>'.$title.$error;
																}
																		
															}else{
																if($string!=""){
																	$error_str=$title.$string;
																}else{
																	$error_str=$title.$error;
																}
																
															}
													$j++;
												}
												if($error_str){
													Mage::getSingleton('adminhtml/session')->addError($error_str1.$error_str);
												}
												
												$go_redirect =false;	
									}else{
										Mage::getSingleton('adminhtml/session')->addSuccess('Return Exception has been saved successfully');
										
										$go_redirect =true;	
									}
									
							}

				}
			}catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->settestData($this->getRequest()->getPost());
				$go_redirect =false;
            }
		}
	
		if($go_redirect){
			$this->_redirect('*/*/uploadproduct', array('profile_id' => $profileId));
		}else{
			$this->_redirect('*/*/productDetails',array('id' => $this->getRequest()->getParam('id')));
		}
	}
	
	public function relationGridAction() {
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('jet/adminhtml_prod_edit_tab_relationgrid')->toHtml()
		);
	}
	
	public function helpAction(){
		
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('CedCommerce Jet Knowledge base'));
        $this->_setActiveMenu('jet/jetknowledgebase');
		$this->renderLayout();
		
	}
	


	
}		


