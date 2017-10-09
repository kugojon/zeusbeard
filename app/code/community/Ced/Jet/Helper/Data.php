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


class Ced_Jet_Helper_Data extends Mage_Core_Helper_Abstract{
    
    protected $_apiHost='';
    protected $user='';
    protected $pass='';  
    public $batcherror=array();


    protected $_jetApiUrl = false;
    protected $_jetFulfillmentNode = false;
    protected $_jetUser = false;
    protected $_jetPass = false;
    protected $_jetIsActive = false;
    protected $_jetIsSanbdBox = false;

    protected $_jetExtraAmazontype = false;
    protected $_jetExtraNumUnitPPU = false;
    protected $_jetExtraTypeUnitPPU = false;

    protected $_jetExtraPackageLength = false;
    protected $_jetExtraPackageWidth = false;
    protected $_jetExtraPackageHeight = false;
    protected $_jetExtraDisplayLength = false;
    protected $_jetExtraDisplayWidth = false;
    protected $_jetExtraDisplayHeight = false;

    protected $_jetExtraLegalDesclaim = false;
    protected $_jetExtraSafetyWarning = false;
    protected $_jetExtraMsrp = false;
    protected $_jetExtraFulfillmentTime = false;

    protected $_jetExtraNoReturnFeeAdjustment = false;
    protected $_jetExtraCountryOfOrigin = false;
    protected $_jetBullets = false;
    protected $_jetShippingWeight = false;
    protected $_jetMapPrice = false;


    protected $_jetRequiredBrand = false;
    protected $_jetRequiredMFPN = false;
    protected $_jetRequiredManufacturer = false;
    protected $_jetRequiredTitle = false;
    protected $_jetRequiredDescription = false;
    protected $_jetRequiredUPC = false;
    protected $_jetRequiredEAN = false;
    protected $_jetRequiredISBN13 = false;
    protected $_jetRequiredISBN10 = false;
    protected $_jetRequiredGTIN14 = false;
    protected $_jetRequiredASIN = false;
    protected $_jetRequiredMultipackQuantity = false;



    protected $_jetRePriceActive = false;
    protected $_profile = false;
    protected  $_jetDebugMode = false;



    public function __construct(){
        $this->_apiHost = Mage::getStoreConfig('jet_options/ced_jet/jet_apiurl');
        $this->getCredentials();
        $this->prepareConfiguration();
    }







    public function validateProduct($id, $product = null,$parent_image,$parent_prod_id,$parent_name,$parent_desc)
    {
            if($product->getTypeId()=="configurable"){
                $configValidation = true;
                $validateResult = array();
                $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
                foreach($childProducts as $chp){
                    $chp = Mage::getModel('catalog/product')->load($chp->getId());
                    $result = $this->validateProduct($chp->getId(), $chp,$parent_image,$parent_prod_id,$parent_name,$parent_desc);
                    if(isset($result['error'])){
                        $validateResult = array_merge_recursive($validateResult, $result);
                        $configValidation = false;
                    }
                }
                if (isset($validateResult['error']) > 0 && !$configValidation) {
                    // $attributesEmpty = implode(',', $attributesEmpty);
                    $error = $validateResult['error'];
                    array_unshift($error, "<b>Correct issues in Sub Products </b><br>");
                    $validateResult['error'] = $error;
                    $product->setData('jet_product_validation_error', json_encode($error));
                    $product->setData('jet_product_validation', 'invalid');

                } else {
                    $product->setData('jet_product_validation', 'valid');
                    $product->setData('jet_product_validation_error', json_encode('valid'));
                    $validateResult['id'] = $id;
                }
                $product->getResource()->saveAttribute($product,'jet_product_validation_error')->saveAttribute($product,'jet_product_validation');
                return $validateResult;
            }


        $attributesEmpty = array();
        $validatedProduct = false;
        $validate = true;






        $identifier =  $product->getData('standard_identifier');
        $standardCodes =array();
        $asin = "";
        $err_msg = '';
        if(is_array($identifier) || count($identifier)>0){
            foreach ($identifier as $value){
                $barcode  = Mage::helper('jet/barcodevalidator');
                $localValidate = true;
                if(isset($value['identifier']) && $value['identifier'] == 'ASIN' ) {
                    if (!$barcode->isAsin($value['value'])) {
                        $validate = false;
                        //$attributesEmpty[] = "Error in Product: " . $product->getName() . " ASIN must be of 10 digits <br />";
                        $attributesEmpty[] = " ASIN must be of 10 digits <br />";
                        $localValidate = false;
                    } else {
                        $asin = $value['value'];
                    }
                }
                else if(isset($value['identifier']) && ($value['identifier'] == 'ISBN-10' || $value['identifier'] == 'ISBN-13')){
                    if(!$barcode->findIsbn($value['value'])){
                        $validate = false;
                        //$attributesEmpty[] = "Error in Product Sku: ".$product->getSku()." <b>".$value['identifier']."</b> is not valid <br />";
                        $attributesEmpty[] = " <b>".$value['identifier']."</b> is not valid <br />";
                        $localValidate = false;
                    }
                }else{
                    $barcode->setBarcode($value['value']);
                    if(!$barcode->isValid()){
                        $validate = false;
                        //$attributesEmpty[] = "Error in Product Sku: ".$product->getSku()." <b>".$value['identifier']."</b> is not valid <br />";
                        $attributesEmpty[] = " <b>".$value['identifier']."</b> is not valid <br />";
                        $localValidate = false;
                    }
                }
                if($localValidate){
                    $standardCodes[] = array('standard_product_code_type' => $value['identifier'],
                        'standard_product_code' => $value['value']);
                }
            }
        }


        //check identifier if not found at product level then check on global
        if(count($standardCodes)==0){
            foreach ($this->getGlobalIdentifierAttributeMapping() as $code => $attribute){
                $value = $product->getData($attribute);
                if(!$value || $value=='')
                    continue;
                $barcode  = Mage::helper('jet/barcodevalidator');
                $localValid = true;
                if($code == 'ASIN' ) {
                    if (!$barcode->isAsin($value)) {
                        $validate = false;
                        //$attributesEmpty[]  = "Error in Product: " . $product->getName() . " ASIN must be of 10 digits";
                        $attributesEmpty[]  =  " ASIN must be of 10 digits";
                        $localValid = false;
                    } else {
                        $asin = $value;
                    }
                }
                else if( $code == 'ISBN-10' || $code == 'ISBN-13'){
                    if(!$barcode->findIsbn($value)){
                        $validate = false;
                        //$attributesEmpty[]  = "Error in Product Sku: ".$product->getSku()." <b>".$code."</b> is not valid";
                        $attributesEmpty[]  = " <b>".$code."</b> is not valid";
                        $localValid = false;
                    }
                }else{
                    $barcode->setBarcode($value);
                    if(!$barcode->isValid()){
                        $validate = false;
                        //$attributesEmpty[]  = "Error in Product Sku: ".$product->getSku()." <b>".$code."</b> is not valid";
                        $attributesEmpty[]  = " <b>".$code."</b> is not valid";
                        $localValid = false;
                    }
                }

                if($localValid){
                    $standardCodes[] = array('standard_product_code_type' => $code,
                        'standard_product_code' => $value);
                }
            }
        }



            $mfrp_exist =false;

            $manu_part_number  = $this->getProductMFPN($product);

            if($manu_part_number!=null){
                $mfrp_exist = true;
            }

            $brand =  $this->getProductBrand($product);



            if($brand==NULL || trim($brand)==''){
                $validate =false;
                //$attributesEmpty[] = "Error in Product Sku: <b>'".$product->getSku()."'</b> Brand information is missing <br>";
                $attributesEmpty[] = "Brand information is missing <br>";
               
                }

            if(count($standardCodes)==0){
                $validate =false;
                //$attributesEmpty[] = "Error in Product Sku: <b>'".$product->getSku()."'</b> Standard Identifier is required please set Identifier values(UPC, EAN,GTIN-14,ISBN-13,ISBN-10) OR ASIN <br>";
                $attributesEmpty[] = "Standard Identifier is required please set Identifier values(UPC, EAN,GTIN-14,ISBN-13,ISBN-10) OR ASIN <br>";

            }




                $sku = $product->getSku();
                $SKU_Array['product_title'] = $this->getProductTitle($product,$parent_name);
                if (strlen($SKU_Array['product_title']) < 5) {
                    $validate =false;
                    //$attributesEmpty[] = "Error in Product Sku: <b>'".$product->getSku()."'</b> product title length must be equal or greater than 5 <br>";
                    $attributesEmpty[] = " product title length must be equal or greater than 5 <br>";
                  }

                $description = $this->getProductDescription($product,$parent_desc);
                //$description = strip_tags($description);

                if (strlen($description) == 0) {
                    $validate =false;
                    //$attributesEmpty[] = "Error in Product Sku: <b>'".$product->getSku()."'</b> product description not found <br>";
                    $attributesEmpty[] = "product description not found <br>";
                }




                if($product->getImage() && $product->getImage() == 'no_selection') {
                    if($parent_image == '')
                    {
                        $validate =false;
                        $attributesEmpty[] = " product image not found <br>";
                    }
                    
                    
                }else{

                    try {
                        $width = Mage::helper('catalog/image')->init($product, 'image')->getOriginalHeight();
                        $height = Mage::helper('catalog/image')->init($product, 'image')->getOriginalWidth();

                        if($width<500 ||  $height<500){
                            //$attributesEmpty[] = "Error in Product Sku: <b>'".$product->getSku()."'</b> product image must be more than 500x500 Px  <br>";
                            $attributesEmpty[] = " product image must be more than 500x500 Px  <br>";
                        }
                        }
                    catch (Exception $e) {
                        $validate = false;
                        //$attributesEmpty[] = "Error in Product Sku: <b>'".$product->getSku()."'</b> ".$e->getMessage()."  <br>";
                        $attributesEmpty[] = $e->getMessage()."  <br>";
                    }

                }



                $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);

                $qty = 0;
                if($stock->getIsInStock())
                    $qty = (int)$stock->getQty();

                if ($qty < 0) {
                   $validate =false;
                //$attributesEmpty[] = "Error in Product Sku: <b>'".$product->getSku()."'</b> product quantity is 0 or less <br>";
                    $attributesEmpty[] = "</b> product quantity is 0 or less <br>";
                }

             if (count($attributesEmpty) > 0 && !$validate) {
                // $attributesEmpty = implode(',', $attributesEmpty);
                $validatedProduct['error'][$product->getSku()] = $attributesEmpty;
                $product->setData('jet_product_validation_error', json_encode($attributesEmpty));
                 $product->setData('jet_product_validation', 'invalid');

            } else {
                $product->setData('jet_product_validation', 'valid');
                $product->setData('jet_product_validation_error', json_encode('valid'));
                $validatedProduct['id'] = $id;
            }
          //  echo "<pre>";print_r($validatedProduct);
        $product->getResource()->saveAttribute($product,'jet_product_validation_error')->saveAttribute($product,'jet_product_validation');
        return $validatedProduct;
    }


    public function prepareConfiguration(){

        $this->_jetApiUrl = Mage::getStoreConfig('jet_options/ced_jet/jet_apiurl');
        $this->_jetFulfillmentNode = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');
        $this->_jetIsActive = Mage::getStoreConfig('jet_options/ced_jet/active');
        $this->_jetIsSanbdBox = Mage::getStoreConfig('jet_options/ced_jet/sandbox');

        $this->_jetExtraAmazontype = Mage::getStoreConfig('jet_options/productextra_infomap/amazon_item_type_keyword');
        $this->_jetExtraNumUnitPPU = Mage::getStoreConfig('jet_options/productextra_infomap/number_units_for_ppu');
        $this->_jetExtraTypeUnitPPU = Mage::getStoreConfig('jet_options/productextra_infomap/type_of_unit_for_ppu');

         $this->_jetExtraPackageLength = Mage::getStoreConfig('jet_options/productextra_infomap/package_length_inches');
         $this->_jetExtraPackageWidth = Mage::getStoreConfig('jet_options/productextra_infomap/package_width_inches');;
         $this->_jetExtraPackageHeight = Mage::getStoreConfig('jet_options/productextra_infomap/package_height_inches');
         $this->_jetExtraDisplayLength = Mage::getStoreConfig('jet_options/productextra_infomap/display_length_inches');
         $this->_jetExtraDisplayWidth = Mage::getStoreConfig('jet_options/productextra_infomap/display_width_inches');
         $this->_jetExtraDisplayHeight = Mage::getStoreConfig('jet_options/productextra_infomap/display_height_inches');

         $this->_jetExtraLegalDesclaim = Mage::getStoreConfig('jet_options/productextra_infomap/legal_disclaimer_description');
         $this->_jetExtraSafetyWarning = Mage::getStoreConfig('jet_options/productextra_infomap/safety_warning');
         $this->_jetExtraMsrp = Mage::getStoreConfig('jet_options/productextra_infomap/msrp');
         $this->_jetExtraFulfillmentTime = Mage::getStoreConfig('jet_options/productextra_infomap/fullfillment_time');

         $this->_jetExtraNoReturnFeeAdjustment = Mage::getStoreConfig('jet_options/productextra_infomap/noreturnfee_adjustment');
         $this->_jetExtraCountryOfOrigin = Mage::getStoreConfig('jet_options/productextra_infomap/country_of_origin');
         $this->_jetBullets = Mage::getStoreConfig('jet_options/productinfo_map/jbullets');
         $this->_jetShippingWeight = Mage::getStoreConfig('jet_options/productinfo_map/jshipping_weight_pounds');
         $this->_jetMapPrice = Mage::getStoreConfig('jet_options/productinfo_map/jmap_price');

         $this->_jetRequiredBrand = Mage::getStoreConfig('jet_options/productinfo_map/jbrand');
         $this->_jetRequiredMFPN = Mage::getStoreConfig('jet_options/productinfo_map/jmanufacturer_part_number');
         $this->_jetRequiredManufacturer = Mage::getStoreConfig('jet_options/productinfo_map/jmanufacture');
         $this->_jetRequiredTitle = Mage::getStoreConfig('jet_options/productinfo_map/jtitle');
         $this->_jetRequiredDescription =  Mage::getStoreConfig('jet_options/productinfo_map/jdescription');
         $this->_jetRequiredUPC = Mage::getStoreConfig('jet_options/productinfo_map/jupc');
         $this->_jetRequiredEAN = Mage::getStoreConfig('jet_options/productinfo_map/jean');
         $this->_jetRequiredISBN13 = Mage::getStoreConfig('jet_options/productinfo_map/jisbn_13');
         $this->_jetRequiredISBN10 = Mage::getStoreConfig('jet_options/productinfo_map/jisbn_10');
         $this->_jetRequiredGTIN14 = Mage::getStoreConfig('jet_options/productinfo_map/jgtin_14');
         $this->_jetRequiredASIN = Mage::getStoreConfig('jet_options/productinfo_map/jasin');
         $this->_jetRequiredMultipackQuantity = Mage::getStoreConfig('jet_options/productinfo_map/jmultipack_quantity');
         $this->_jetRePriceActive = Mage::getStoreConfig('jet_options/ced_jetreprice/active');
         $this->_jetDebugMode = Mage::getStoreConfig('jet_options/ced_jet/debug');


    }

    public function isEnabled(){
        /*$flag=false;
        if(Mage::getStoreConfig('jet_options/ced_jet/active')){
            $flag=true;
        }
        return $flag;*/
        return $this->_jetIsActive;
    }
    public function isDebugMode(){
        return $this->_jetDebugMode;
    }
    public function JrequestTokenCurl(){
    
        $ch = curl_init();
        $url= $this->_apiHost.'/Token';
        $postFields='{"user":"'.$this->user.'","pass":"'.$this->pass.'"}';
        
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json;"));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    
        $server_output = curl_exec ($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($server_output, 0, $header_size);
        $body = substr($server_output, $header_size);
        curl_close ($ch);
        $token_data =json_decode($body, true);
        
        if(is_array($token_data) && isset($token_data['id_token'])){
            $data = new Mage_Core_Model_Config();
            $data->saveConfig('jetcom/token', $body, 'default', 0);
            return json_decode($body, true);
        }else{
            return false;
        }
            
    }
    
    /*
     * Post Request on Jetcom
     */
    
    public function CPostRequest($method,$postFields){
        
        $url= $this->_apiHost.$method;
    
        $tObject =$this->Authorise_token();
    
        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer ".$tObject['id_token'];
    
            
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$postFields);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            //curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    
    
            $server_output = curl_exec ($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($server_output, 0, $header_size);
            $body = substr($server_output, $header_size);
            curl_close ($ch);
    
            return $body;
    }
    
    /*
    * PUT Request on Jetcom
    */
    public function CPutRequest($method, $post_field){

        

        $url= $this->_apiHost.$method;
        $ch = curl_init($url);
        $tObject =$this->Authorise_token();

        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer ".$tObject['id_token'];
                
    
            curl_setopt($ch, CURLOPT_POSTFIELDS,$post_field);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            //curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    
            $server_output = curl_exec ($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($server_output, 0, $header_size);
            $body = substr($server_output, $header_size);
            curl_close ($ch);
    
            return $body;
    
        }
    
        public function CGetRequest($method){
            $tObject =$this->Authorise_token();
            $ch = curl_init();
            $url= $this->_apiHost.$method;
    
    
            $headers = array();
            $headers[] = "Content-Type: application/json";
            $headers[] = "Authorization: Bearer ".$tObject['id_token'];
    
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $server_output = curl_exec ($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($server_output, 0, $header_size);
            $body = substr($server_output, $header_size);
            curl_close ($ch);
    
            return $body;
        }
        public function directfile($method){
            //$tObject =$this->Authorise_token();
            $ch = curl_init();
            $url= $method;
    
    
            //$headers = array();
            //$headers[] = "Content-Type: application/json";
            //$headers[] = "Authorization: Bearer ".$tObject['id_token'];
    
            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            //curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $server_output = curl_exec ($ch);
            //$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            //$header = substr($server_output, 0, $header_size);
            //$body = substr($server_output, $header_size);
            curl_close ($ch);
    
            return $server_output;
        }
    
    
    
        public function Authorise_token(){

            $Jtoken = json_decode(Mage::getStoreConfig('jetcom/token'), true);

            $refresh_token =false;

            if(is_array($Jtoken) && isset($Jtoken['id_token'])){
                $ch = curl_init();
                $url= $this->_apiHost.'/authcheck';
                
        
                $headers = array();
                $headers[] = "Content-Type: application/json";
                $headers[] = "Authorization: Bearer ".$Jtoken["id_token"];
        
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
                $server_output = curl_exec ($ch);
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $header = substr($server_output, 0, $header_size);
                $body = substr($server_output, $header_size);
                curl_close ($ch);
                
                $bjson = json_decode($body);
                
            if(is_object($bjson) &&
                 $bjson->Message!='' &&
                 $bjson->Message=='Authorization has been denied for this request.')
                 {
                    $refresh_token =true;   
                 }      
                
            }else{
                $refresh_token =true;   
            }
            
            if($refresh_token){
                $token_data = $this->JrequestTokenCurl();
                if($token_data!= false){
                    return $token_data;
                }else{
                    Mage::getSingleton('core/session', array('name'=>'adminhtml'));
                    if(Mage::getSingleton('admin/session')->isLoggedIn()){
                        $session = Mage::getSingleton('adminhtml/session');
                        $message = 'API user & API password either empty or Invalid. Please set API user & API pass from jet configuration.';
                        foreach ($session->getMessages()->getItems() as $item){
                    if($item->getText() == $message)
                        return $this;
                     
                }
                $session
                    ->addError($message);
                    }
                   
                }       
            }else{
                return $Jtoken;
            }
        
        }
    
        /**
        * http://www.php.net/manual/en/function.gzwrite.php#34955
        *
        * @param string $source Path to file that should be compressed
        * @param integer $level GZIP compression level (default: 9)
        * @return string New filename (with .gz appended) if success, or false if operation fails
        */
        function gzCompressFile($source, $level = 9){
            $dest = $source . '.gz';
            $mode = 'wb' . $level;
            $error = false;
            if ($fp_out = gzopen($dest, $mode)) {
            if ($fp_in = fopen($source,'rb')) {
            while (!feof($fp_in))
                gzwrite($fp_out, fread($fp_in, 1024 * 512));
                    fclose($fp_in);
                } else {
                    $error = true;
                }
                    gzclose($fp_out);
                } else {
                    $error = true;
                }
            if ($error)
                return false;
            else
                return $dest;
        }
    
    
        /*
        * New function to upload file
        */
        public function uploadFile($localfile ,$url){

             $headers = array();
             $headers[] = "x-ms-blob-type:BlockBlob";
             $ch = curl_init();
             curl_setopt($ch, CURLOPT_URL, $url);
             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
             curl_setopt($ch, CURLOPT_HEADER, 1);
             curl_setopt($ch, CURLOPT_PUT, 1);
             $fp = fopen ($localfile, 'r');
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch, CURLOPT_INFILE, $fp);
             curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localfile));
    
    
             $http_result = curl_exec($ch);
             $error = curl_error($ch);
             $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
    
             curl_close($ch);
             fclose($fp);
    
        }
    
    /*
    * jet return feed backoptions
    */
    
    public function feedbackOptArray(){
    
                return array(
                        array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select a Option')),
                             array('value' => 'item damaged', 'label' => Mage::helper('adminhtml')->__('item damaged')),
                             array('value' => 'not shipped in original packaging', 'label' => Mage::helper('adminhtml')->__('not shipped in original packaging')),
                             array('value' => 'customer opened item', 'label' => Mage::helper('adminhtml')->__('customer opened item')),
                            
                      );
            
    }
    
    public function wanttoreturn(){
        return array(
                        array('value' => '0', 'label' => Mage::helper('adminhtml')->__('No')),
                             array('value' => '1', 'label' => Mage::helper('adminhtml')->__('Yes')),
                        );
    }
    /*
    * jet refund reason 
    */
    public function refundreasonOptionArr(){
    
                return array(
                        array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select a Option')),
                             array('value' => 'No longer want this item', 'label' => Mage::helper('adminhtml')->__('No longer want this item')),
                             array('value' => 'Received the wrong item', 'label' => Mage::helper('adminhtml')->__('Received the wrong item')),
                             array('value' => 'Website description is inaccurate', 'label' => Mage::helper('adminhtml')->__('Website description is inaccurate')),
                             array('value' =>'Product is defective / does not work', 'label' => Mage::helper('adminhtml')->__('Product is defective / does not work')),
                             array('value' => 'Item arrived damaged - box intact', 'label' => Mage::helper('adminhtml')->__('Item arrived damaged - box intact')),
                             array('value' => 'Item arrived damaged - box damaged', 'label' => Mage::helper('adminhtml')->__('Item arrived damaged - box damaged')),
                             array('value' => 'Package never arrived', 'label' => Mage::helper('adminhtml')->__('Package never arrived')),
                             array('value' => 'Package arrived late', 'label' => Mage::helper('adminhtml')->__('Package arrived late')),
                             array('value' => 'Wrong quantity received', 'label' => Mage::helper('adminhtml')->__('Wrong quantity received')),
                             array('value' => 'Better price found elsewhere', 'label' => Mage::helper('adminhtml')->__('Better price found elsewhere')),
                             array('value' => 'Unwanted gift', 'label' => Mage::helper('adminhtml')->__('Unwanted gift')),
                             array('value' => 'Accidental order', 'label' => Mage::helper('adminhtml')->__('Accidental order')),
                             array('value' => 'Unauthorized purchase', 'label' => Mage::helper('adminhtml')->__('Unauthorized purchase')),
                             array('value' => 'Item is missing parts / accessories', 'label' => Mage::helper('adminhtml')->__('Item is missing parts / accessories')),
                             array('value' => 'Return to Sender - damaged, undeliverable, refused', 'label' => Mage::helper('adminhtml')->__('Return to Sender - damaged, undeliverable, refused')),
                             array('value' => 'Return to Sender - lost in transit only', 'label' => Mage::helper('adminhtml')->__('Return to Sender - lost in transit only')),
                             array('value' => 'Item is refurbished', 'label' => Mage::helper('adminhtml')->__('Item is refurbished')),
                             array('value' => 'Item is expired', 'label' => Mage::helper('adminhtml')->__('Item is expired')),
                             array('value' => 'Package arrived after estimated delivery date', 'label' => Mage::helper('adminhtml')->__('Package arrived after estimated delivery date')),
                            
                      );
            
    }
    
    public function shippingCarrier(){
        return array(
                        array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select an Option')),
                        array('value' => 'SecondDay', 'label' => Mage::helper('adminhtml')->__('SecondDay')),
                        array('value' => 'NextDay', 'label' => Mage::helper('adminhtml')->__('NextDay')),
                        array('value' => 'Scheduled', 'label' => Mage::helper('adminhtml')->__('Scheduled')),
                        array('value' =>'Expedited', 'label' => Mage::helper('adminhtml')->__('Expedited')),
                        array('value' => 'Standard', 'label' => Mage::helper('adminhtml')->__('Standard')),
                            
                      );
    }
    
    public function shippingMethod(){
        return array(
                        array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select an Option')),
                        array('value' => 'UPS', 'label' => Mage::helper('adminhtml')->__('UPS')),
                        array('value' => 'FedEx', 'label' => Mage::helper('adminhtml')->__('FedEx')),
                        array('value' => 'USPS', 'label' => Mage::helper('adminhtml')->__('USPS')),
                            
                    );
    }
    
    public function shippingOverride(){
        return array(
                        array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select an Option')),
                        array('value' => 'Override charge', 'label' => Mage::helper('adminhtml')->__('Override charge')),
                        array('value' => 'Additional charge', 'label' => Mage::helper('adminhtml')->__('Additional charge')),
                            
                    );
    }
    
    public function shippingExcep(){
        return array(
                        array('value' => '', 'label' => Mage::helper('adminhtml')->__('Please Select an Option')),
                        array('value' => 'exclusive', 'label' => Mage::helper('adminhtml')->__('exclusive')),
                        array('value' => 'restricted', 'label' => Mage::helper('adminhtml')->__('restricted')), 
                    );
    }
    
    public function getFulfillmentNode(){
        /*$fullfillmentnodeid = Mage::getStoreConfig('jet_options/ced_jet/jet_fullfillmentnode');*/
        return $this->_jetFulfillmentNode;
        
    }
    
    public function getStandardOffsetUTC()
    {
        $timezone = date_default_timezone_get();
        
        if($timezone == 'UTC') {
            return '';
        } else {
            $timezone = new DateTimeZone($timezone);
            $transitions = array_slice($timezone->getTransitions(), -3, null, true);
    
            foreach (array_reverse($transitions, true) as $transition)
            {
                if ($transition['isdst'] == 1)
                {
                    continue;
                }
    
                return sprintf('UTC %+03d:%02u', $transition['offset'] / 3600, abs($transition['offset']) % 3600 / 60);
            }
    
            return false;
        }
    }

    public function Varitionfix($json, $count){
    
        $patt='';$replacement='';
        for($i=1; $i<=$count;$i++){
            $patt=$patt.'"(\d+)",';
            $replacement=$replacement.'$'.$i.',';
        }
        $patt=rtrim($patt,',');
        $replacement=rtrim($replacement,',');
        
        $pattern = '/"variation_refinements":\['.$patt.'\]/i';
        
        $replacement = '"variation_refinements":['.$replacement.']';
        
        return preg_replace($pattern, $replacement, $json);
    
    }
    
    public function ConvertNodeInt($json){
        
        $pattern = '/"jet_browse_node_id":"(\d+)"/i';

        $replacement = '"jet_browse_node_id":$1';
        $json_replaced_node = preg_replace($pattern, $replacement, $json);
        
        $pattern1 = '"mjattr';
        $replacement1 = '';     
        $json_replaced_node = str_replace($pattern1, $replacement1, $json_replaced_node);
        $pattern1 = 'mjattr"';
        $json_replaced_node = str_replace($pattern1, $replacement1, $json_replaced_node);
        
        
        
        $pattern1 = '/"attribute_id":"(\d+)"/i';
        $replacement1 = '"attribute_id":$1';
        return preg_replace($pattern1, $replacement1, $json_replaced_node);
        
    }
    

    public function createJsonFile($type, $data){
        //if debug mode then track file
        $t = '';
        if($this->isDebugMode())
            $t=time()+rand(2,5);
        
        $finalskujson= json_encode($data);
        if($type=='MerchantSKUs'){
            $newJsondata = $this->ConvertNodeInt($finalskujson);
        }else{
            $newJsondata = $finalskujson;
        }
        
        $file_path = Mage::getBaseDir("var").DS."jetupload".DS.$type.$t.".json";
        $file_type = $type;
        $file_name=$type.$t.".json";
        $myfile = fopen($file_path, "w") ;
        fwrite($myfile, $newJsondata);
        fclose($myfile);
        if(!file_exists($file_path.".gz")){
            Mage::helper('jet')->gzCompressFile($file_path,9);
        }
        return $file_path;
    }

//for ajax upload start
    public function prepareJsonFile($type, $data){
        $t=time()+rand(2,5);
        
        $finalskujson= json_encode($data);
        if($type=='MerchantSKUs'){
            $newJsondata = $this->ConvertNodeInt($finalskujson);
        }else{
            $newJsondata = $finalskujson;
        }
        
        
        return $newJsondata;
    }
    //for ajax upload end

        public function generateJsonFile($type, $data){
            $t=time()+rand(2,5);

            $finalskujson= json_encode($data);
            if($type=='MerchantSKUs'){
                $newJsondata = $this->ConvertNodeInt($finalskujson);
            }else{
                $newJsondata = $finalskujson;
            }
            return $newJsondata;
        }
    
    public function getAssociatedJetCategoryId($product){
        $cats = $product->getCategoryIds();
        if(!$cats || (is_array($cats) && count($cats)<=0)){
                return false;
        }
        $Jet_cat_result = Mage::getModel('jet/jetcategory')
                    ->getCollection()
                    ->addFieldToFilter('magento_cat_id',array('in',$cats));
        if(count($Jet_cat_result)>0)            
            return $Jet_cat_result->getFirstItem();
        return false;
    }
    
    public function getAssociatedJetAttributeIds($product){
        if($jetCategory = $this->getAssociatedJetCategoryId($product)){
            $attribute = $jetCategory->getJetAttributes();
            //return array_map(function($val) { return "mjattr".$val."mjattr";} , explode(',', $attribute));
        }
        return false;
    }
    
    public function getMainProductSku($product){
        $sku = false;
        if($product->getTypeId()=="bundle"){
            return $sku;
        }else if($product->getTypeId()=="grouped"){
            return $sku;
        }else if($product->getTypeId()=="configurable"){
             $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
                 /*If only one product in child no need to make relation */
                 foreach($childProducts as $chp){
                     $detail = $this->getProductDetail($chp->getSku());
                    if(is_array($detail) && isset($detail['relationship']) && $detail['relationship']== "Variation"){
                        //continue;
                         $sku =  $chp->getSku();
                         break; 
                    }
                        $sku =  $chp->getSku();
                    break;  
                }               
        }
            
        return $sku;
    }
    
    public function getProductDetail($sku){
        $response =$this->CGetRequest('/merchant-skus/'.rawurlencode($sku).'');
        $result=json_decode($response, true);
        return $result;
    }

    public function getBatchIdFromProductId($pid=""){
        $batch_id=0;
        if($pid){
                $batchcoll="";
                $batchcoll=Mage::getModel('jet/batcherror')->getCollection();
                $batch_id="";
                foreach($batchcoll as $bat){
                            if($bat->getData('product_id')==$pid){
                                    $batch_id=$bat->getData('id');
                                    return $batch_id;
                            }
                }
        }
        return $batch_id;
    }
    public function initBatcherror(){
            $batcherror=array();
            Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
    }
    public function initBatchErrorForProduct($pid='',$index=0){
            $batcherror=array();
            $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
            $batch_id=0;
            $batch_id=$this->getBatchIdFromProductId($pid);
            $model="";
            $model=Mage::getModel('catalog/product')->load($pid);
            if($batch_id){
                $batchmod='';
                $batchmod=Mage::getModel('jet/batcherror')->load($batch_id);
                $batchmod->setData("batch_num",$index);
                $batchmod->setData("is_write_mode",'1');
                $batchmod->setData("error",'');
                $batchmod->save();
            }else{
                $batchmod='';
                $batchmod=Mage::getModel('jet/batcherror');
                $batchmod->setData('product_id',$pid);
                $batchmod->setData('product_sku',$model->getSku());
                $batchmod->setData("is_write_mode",'1');
                $batchmod->setData("error",'');
                $batchmod->setData("batch_num",$index);
                $batchmod->save();
            }
            $batcherror[$pid]['error']="";
            $batcherror[$pid]['sku']=$model->getSku();
            $batcherror[$pid]['batch_num']=$index;
            Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
    }
    /* load Attribute details */
    public function getattributeType($attrcode){
        try{
            $load_attr = Mage::getModel('catalog/resource_eav_attribute')
                ->loadByCode('catalog_product',$attrcode);
            if(!$load_attr->getId()){
                return false;
            }else{
                return $load_attr->getFrontendInput();
            }
        }catch(Exception $e){}
    }
    
    public function moreProductAttributesData($product){
        //$config_amtype = Mage::getStoreConfig('jet_options/productextra_infomap/amazon_item_type_keyword');
        //$config_num_unit = Mage::getStoreConfig('jet_options/productextra_infomap/number_units_for_ppu');
        //$config_type_unit = Mage::getStoreConfig('jet_options/productextra_infomap/type_of_unit_for_ppu');
        
        //$config_pl = Mage::getStoreConfig('jet_options/productextra_infomap/package_length_inches');
        //$config_pw = Mage::getStoreConfig('jet_options/productextra_infomap/package_width_inches');
        //$config_ph = Mage::getStoreConfig('jet_options/productextra_infomap/package_height_inches');
        //$config_dl = Mage::getStoreConfig('jet_options/productextra_infomap/display_length_inches');
        //$config_dw = Mage::getStoreConfig('jet_options/productextra_infomap/display_width_inches');
        //$config_dh = Mage::getStoreConfig('jet_options/productextra_infomap/display_height_inches');
        
        //$config_lgldesclaim = Mage::getStoreConfig('jet_options/productextra_infomap/legal_disclaimer_description');
        //$config_Styw = Mage::getStoreConfig('jet_options/productextra_infomap/safety_warning');
        //$config_msrp = Mage::getStoreConfig('jet_options/productextra_infomap/msrp');
        //$config_filtime = Mage::getStoreConfig('jet_options/productextra_infomap/fullfillment_time');
        //$confi_retun_fee = Mage::getStoreConfig('jet_options/productextra_infomap/noreturnfee_adjustment');
        //$config_coo = Mage::getStoreConfig('jet_options/productextra_infomap/country_of_origin');
        //$var_coo = $this->getattributeType($this->_jetExtraCountryOfOrigin);
        
        //$config_bullets = Mage::getStoreConfig('jet_options/productinfo_map/jbullets');
        //$var_bullets = $this->getattributeType($this->_jetBullets);
        
        //$config_ship_weight = Mage::getStoreConfig('jet_options/productinfo_map/jshipping_weight_pounds');
        //$config_map_price = Mage::getStoreConfig('jet_options/productinfo_map/jmap_price');
        
        
        $more=array();
        
        if(trim($this->_jetExtraAmazontype)!="" && $amazonType = $this->getProductGeneralAttribute($product,$this->_jetExtraAmazontype)){
            if($amazonType != null && $amazonType !='')
                $more['amazon_item_type_keyword']= trim($amazonType);
        }
        if($product->getData('category_path')!=""){
            $more['category_path']=$product->getData('category_path');
        }
        
        if($this->_jetBullets == 'bullets' && $product->getData('bullets')!=""){
            $bullet_data=array();
            $bullets=$product->getData('bullets');
            preg_match_all("/\{(.*?)\}/", $bullets, $matches);
            $new_bullets=array();
            $new_bullets=$matches[1];
            $j=0;
            for($i=0;$i<count($new_bullets);$i++){
                $string=trim($new_bullets[$i]);
                    if(strlen($string)<=500 && strlen($string)>0){
                            $bullet_data[$j]=$string;
                            $j++;
                    }
                    if($j>4){
                        break;
                    }
                    
            }
            if(count($bullet_data)>0){
                $more['bullets']=$bullet_data;  
            }
        
        }else if($this->_jetBullets !=''){
                $buldata= $this->getProductGeneralAttribute($product, $this->_jetBullets);
                if($buldata!=""){
                    $explode_data = explode(",",$buldata);
                    if(count($explode_data)>0){
                            $more['bullets']=$explode_data; 
                    }
                }
        }
        
        $ppu = (trim($this->_jetExtraNumUnitPPU)!="") ? $this->getProductGeneralAttribute($product, $this->_jetExtraNumUnitPPU) :"";
        if($ppu!="" && is_numeric($ppu)){
            $more['number_units_for_price_per_unit']=(float)$ppu;
        }
        
        if(trim($this->_jetExtraTypeUnitPPU)!="" && $typeUnitPPU = $this->getProductGeneralAttribute($product,$this->_jetExtraTypeUnitPPU)){
            if($typeUnitPPU != "")
                $more['type_of_unit_for_price_per_unit']=$product->getData($this->_jetExtraTypeUnitPPU);
        }
        
        $ship_weight = (trim($this->_jetShippingWeight)!="") ? $this->getProductGeneralAttribute($product,$this->_jetShippingWeight): "";
        if($ship_weight!="" && is_numeric($ship_weight)){
            $more['shipping_weight_pounds']=(float)$ship_weight;
        }

        $pli = (trim($this->_jetExtraPackageLength)!="") ? $this->getProductGeneralAttribute($product,$this->_jetExtraPackageLength) : "";
        if($pli!=""  && is_numeric($pli)){
            $more['package_length_inches']=(float)$pli;
        }

        $plw = (trim($this->_jetExtraPackageWidth)!="") ? $this->getProductGeneralAttribute($product,$this->_jetExtraPackageWidth) : "";
        if($plw!=""  && is_numeric($plw)){
            $more['package_width_inches']=(float)$plw;
        }

        $plh = (trim($this->_jetExtraPackageHeight)!="") ?  $this->getProductGeneralAttribute($product,$this->_jetExtraPackageHeight) : "";
        if($plh !=""  && is_numeric($plh)){
            $more['package_height_inches']=(float)$plh;
        }

        $dli = (trim($this->_jetExtraDisplayLength)!="") ? $this->getProductGeneralAttribute($product,$this->_jetExtraDisplayLength) : "";
        if($dli!=""  && is_numeric($dli)){
            $more['display_length_inches']=(float)$dli;
        }

        $dlw = (trim($this->_jetExtraDisplayWidth)!="") ? $this->getProductGeneralAttribute($product,$this->_jetExtraDisplayWidth) : "";
        if($dlw!=""  && is_numeric($dlw)){
            $more['display_width_inches']=(float)$dlw;
        }

        $dlh = (trim($this->_jetExtraDisplayHeight)!="") ? $this->getProductGeneralAttribute($product,$this->_jetExtraDisplayHeight) : "";
        if($dlh!=""  && is_numeric($dlh)){
            $more['display_height_inches']=(float)$dlh;
        }

        if($product->getData('prop_65')!=""){
            if($product->getData('prop_65')){
                    $more['prop_65']=true;
            }else{
                    $more['prop_65']=false;
            }
        }
        $lgl_desclaim = (trim($this->_jetExtraLegalDesclaim)!="") ? $this->getProductGeneralAttribute($product, $this->_jetExtraLegalDesclaim) : "";
        if($lgl_desclaim!=""){
            $string=trim($lgl_desclaim);
            if(strlen($string)<=500 && strlen($string)>0){
                    $more['legal_disclaimer_description']=$string;
            }
        }
        if($product->getData('cpsia_cautionary_statements')!=""){
            $string="";
            $string=$product->getData('cpsia_cautionary_statements');
            $arr=explode(',',$string);
            if(count($arr)>0){
                if($arr[0]=='no warning applicable')
                    array_shift($arr);  
                    
                $more['cpsia_cautionary_statements']=$arr;  
            }
        }
        
        if(trim($this->_jetExtraSafetyWarning)!="" && $safetyWarning = $this->getProductGeneralAttribute($product,$this->_jetExtraSafetyWarning)){
            if($safetyWarning != null && $safetyWarning!='') {
                $string = trim($safetyWarning);
                if (strlen($string) > 0) {
                    $more['safety_warning'] = substr($string, 0, 1999);
                }
            }
        }
        if($product->getData('start_selling_date')!=""){
            $string=$product->getData('start_selling_date');
            $offset_end = $this->getStandardOffsetUTC(); 
            if(empty($offset_end) || trim($offset_end)==''){
                $offset = '.0000000-00:00';
            }else{
                $offset = '.0000000'.trim($offset_end);
            }
            $shipTodatetime="";
            $shipTodatetime=strtotime($string);
            $Ship_todate ="";
            $Ship_todate = date("Y-m-d", $shipTodatetime) . 'T' . date("H:i:s", $shipTodatetime).$offset;
            $more['start_selling_date']= $Ship_todate;
            
        }
        
        $fill_time = (trim($this->_jetExtraFulfillmentTime)!="") ? $this->getProductGeneralAttribute($product,$this->_jetExtraFulfillmentTime) : "";
        if($fill_time!=""  && is_numeric($fill_time)){
            $more['fulfillment_time']=(int)$fill_time;
        }
        $jmap_price = (trim($this->_jetMapPrice)!="") ? $this->getProductGeneralAttribute($product,$this->_jetMapPrice) : "";
        if($jmap_price!="" && is_numeric($jmap_price)){
            $more['map_price']=(float)$jmap_price;
        }
        $dmsrp = (trim($this->_jetExtraMsrp)!="") ? $this->getProductGeneralAttribute($product,$this->_jetExtraMsrp) : "";
        if($dmsrp!="" && is_numeric($dmsrp)){
            $more['msrp']=(float)$dmsrp;
        }

        if($product->getData('map_implementation')!=""){
                $more['map_implementation']=$product->getData('map_implementation');
        }
        if($product->getData('product_tax_code')!=""){
                $more['product_tax_code']=$product->getData('product_tax_code');
        }
        //no_return_fee_adjustment
        $dretun_fee = (trim($this->_jetExtraNoReturnFeeAdjustment)!="") ? $this->getProductGeneralAttribute($product,$this->_jetExtraNoReturnFeeAdjustment) : "";
        if($dretun_fee!="" && is_numeric($dretun_fee)){
                $more['no_return_fee_adjustment']=(float)$dretun_fee;
        }
        if($product->getData('exclude_from_fee_adjust')!=""){
            if($product->getData('exclude_from_fee_adjust')){
                    $more['exclude_from_fee_adjustments']=true;
            }else{
                    $more['exclude_from_fee_adjustments']=false;
            }
        }
        if($product->getData('ships_alone')!=""){
            if($product->getData('ships_alone')){
                    $more['ships_alone']=true;
            }else{
                    $more['ships_alone']=false;
            }
        }
        $this->_jetRequiredManufacturer = 'country_of_manufacture';
        if($this->_jetRequiredManufacturer =='country_of_manufacture' &&  $product->getData('country_of_manufacture')!=""){
                $country_name = Mage::getModel('directory/country')->loadByCode($product->getData('country_of_manufacture'))->getName();
                $country_name=trim($country_name);
                if(strlen($country_name)<=50 && strlen($country_name)>0){
                        $more['country_of_origin']= substr($country_name, 0,50);
                }

        }else{
                $country_manufact = $this->getProductGeneralAttribute($product,$this->_jetExtraCountryOfOrigin);
                    
                if($country_manufact!=''){  
                    $country_manufact = substr($country_manufact,0,50);
                    $SKU_Array['country_of_origin']="$country_manufact";
                }   

        }
        return $more;
    }

    public function getProductBrand($product){
        $par_brand_name = '';
        $brand_attr_type = $this->getattributeType($this->_jetRequiredBrand);
        if($brand_attr_type!=false){
            if($brand_attr_type == 'select'){
                $par_brand_name =$product->getAttributeText($this->_jetRequiredBrand);
            }else{
                $par_brand_name = $product->getData($this->_jetRequiredBrand);
            }
        }else{
            $par_brand_name = $product->getData('jet_brand');
        }
        return $par_brand_name;
    }


    public function getProductTitle($product,$parent_name){
        $productTitle = '';
        if($parent_name!='' && $parent_name!=null)
        {
            $productTitle = substr($parent_name,0,500);
        }
        else
        {
            if(trim($this->_jetRequiredTitle)!="" && $product->getData($this->_jetRequiredTitle)!="")
            $productTitle = substr($product->getData($this->_jetRequiredTitle), 0,500);
            else
            $productTitle = substr($product->getName(),0,500);  
        }
        

        return $productTitle;
    }

    public  function getProductDescription($product,$parent_desc){
        $description="";
         if($parent_desc!='' && $parent_desc!='')
        {
            $description = $parent_desc;
        }
        else
        {   
            if(trim($this->_jetRequiredDescription) && $product->getData($this->_jetRequiredDescription)!=""){
            $description = $product->getData($this->_jetRequiredDescription);
            }else{
            $description = $product->getDescription();
                }
        }
        
        $description = (strlen($description) > 1999) ? substr($description,0,1999) : $description;

        return $description;
    }

    public function getProductMFPN($product){
        $manu_part_number = '';
        $val_mfpn = $this->getattributeType($this->_jetRequiredMFPN);
        if($val_mfpn!=false){
            if($val_mfpn=='select'){
                $manu_part_number = $product->getAttributeText($this->_jetRequiredMFPN);
            }else{
                $manu_part_number = $product->getData($this->_jetRequiredMFPN);
            }
        }
        return $manu_part_number;
    }

    public function getProductGeneralAttribute($product, $attributeCode){
        $attribute_value = '';
        $attribute = $product->getResource()->getAttribute($attributeCode);
        if ($attribute && $product->getData($attributeCode))
        {
            $attribute_value = $attribute ->getFrontend()->getValue($product);
        }
        return $attribute_value;
    }

    public  function getGlobalVariantAttributeMapping(){
        $data =  Mage::getStoreConfig('jet_options/productinfo_map/variant_attributes');

        if($data!=''){
            $data = unserialize($data);
            unset($data['__empty']);

            $data = array_filter(array_map(function ($n) {
                if (isset($n['jet_attribute_id']) && $n['jet_attribute_id'] != '-') return $n;
            }, $data));
            return $data;
        }
        return array();
    }

    public  function getGlobalIdentifierAttributeMapping(){
        $data =  Mage::getStoreConfig('jet_options/productinfo_map/identifiers');

        if($data!=''){
            $data = unserialize($data);
            unset($data['__empty']);
            $mappedData = array();
            foreach ($data as $val){
                if(isset($val['magento_attribute_code']) && $val['magento_attribute_code']!=""){
                    $mappedData[$val['identifier']]= $val['magento_attribute_code'];
                }
            }
            return $mappedData;
        }
        return array();
    }


    public function getProductProfile($product){
        $productId = false;
        $currentProfile = false;

        if(is_numeric($product))
            $productId = $product;
        else if($product instanceof Mage_Catalog_Model_Product)
            $productId = $product->getId();

        //first check if the product is found in the same product
        $profileProducts =  Mage::getModel('jet/profileproducts')->loadByField('product_id', $productId);
        if($profileId = $profileProducts->getProfileId()){
            if(!isset($this->_profile[$profileId])){
                $profile =  Mage::getModel('jet/profile')->load($profileId);
                $this->_profile[$profileId] = $profile;
            }
            $currentProfile = $this->_profile[$profileId];
        }else{//second check if the product is found in the same product it exist in parent product
            $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')
                ->getParentIdsByChild($product->getId());
            foreach ($parentIds as $id){
                if($profile = $this->getProductProfile($id)){
                    return $profile;
                    break;
                }
            }
        }
        return $currentProfile;
    }



    public function prepareProductJetAttribute($product, $profile,$parent_prod_id){

        if(!$product || !$profile)
            return false;
$Attribute_array = array();
        $globalVariantAttribute = $this->getGlobalVariantAttributeMapping();

        $profileAttribute = json_decode($profile->getProfileAttributeMapping(), true);
        //build attribute mapping from profile attribute
        if(isset($profileAttribute['mapped_attribute']) && isset($profileAttribute['jet_attribute'])){
            $mappedAttribute = $profileAttribute['mapped_attribute'];
            $jetAttribute = $profileAttribute['jet_attribute'];
            foreach ($jetAttribute as $jetAt){
                $attribute_value = '';
                //check if attribute is mapped
                if(isset($jetAt['attribute_id']) && isset($mappedAttribute[$jetAt['attribute_id']])
                    && isset($mappedAttribute[$jetAt['attribute_id']]['magento_attribute_code'])){


                    $magentoAttribute = $mappedAttribute[$jetAt['attribute_id']]['magento_attribute_code'];
                    $setAttributeId = $jetAt['attribute_id'];

                    $attribute_value = $this->getProductGeneralAttribute($product, $magentoAttribute);

                    if($attribute_value == null || $attribute_value == '')
                        continue;
                    //if Attributes of Units type
                    if(isset($jetAt['units'])){
                        $code_before_space = explode(" ",$attribute_value);
                        array_pop($code_before_space);
                        $first_half = trim($code_before_space[0]);

                        $getUnit_value = end(explode(" ",$attribute_value));
                        $getUnit_value = trim($getUnit_value);

                        if(isset($first_half) && $first_half!=''){
                            $units = $jetAt['units'];
                            if(count($units)>0){
                                if(!empty($getUnit_value) || $getUnit_value!=''){

                                    if(in_array($getUnit_value,$units)){
                                        $Attribute_array[] = array(
                                            'attribute_id'=> $setAttributeId,
                                            'attribute_value'=>$first_half,
                                            'attribute_value_unit'=>$getUnit_value);
                                    }else{
                                        //if no unit is available
                                        $emsg= 'Unit value is required for this attribute '.$magentoAttribute.' from one of these comma seperated values: '.implode(',',$units).' for example : '.$code_before_space[0].'{space}'.$units[0].' ie. '.$code_before_space[0].' '.$units[0];

                                        $batcherror=array();
                                        $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
                                        $err_msg="";
                                        $err_msg=$emsg.' for product '. $product->getName().' So this is skipped from upload.';

                                        if(count($batcherror)>0){
                                            $msg['type'] = 'error';
                                            $msg['data'] = $err_msg ;
                                            return $msg;
                                        }
                                    }
                                }
                            }
                        }

                    }else{
                        $Attribute_array[] = array(
                            'attribute_id'=> $setAttributeId,
                            'attribute_value'=>$attribute_value);

                    }
                }
            }
        }


        //build attribute mapping for variant attributes
       
        if ($parent_prod_id!='') {
            $rr = Mage::getModel('catalog/product')->load($parent_prod_id);
            $productAttributeOptions = $rr->getTypeInstance(true)->getConfigurableAttributesAsArray($rr);


            $mapping = json_decode($profile->getProfileAttributeMapping(), true);
            $mappedA = (isset($mapping['mapped_attribute']))?$mapping['mapped_attribute']:array();
            $mappedAttribute = array_filter(array_map(function ($n) {
                if (isset($n['magento_attribute_code']) && $n['magento_attribute_code'] != '') return $n;
            }, $mappedA));


            $childjetAttributeMapping = array();

            foreach ($productAttributeOptions as $productAttribute) {
                $found = false;
                //check if already mapped in profile level
                foreach ($mappedAttribute as $map) {
                    if ($productAttribute['attribute_code'] == $map['magento_attribute_code']) {
                        $found = true;
                        break;
                    }
                }


                if (!$found)
                    foreach ($globalVariantAttribute as $map) {
                        if ($productAttribute['attribute_code'] == $map['magento_attribute_code']) {

                            $setAttributeId = $map['jet_attribute_id'];
                            $magentoAttribute = $map['magento_attribute_code'];
                            $attribute_value = $this->getProductGeneralAttribute($product, $magentoAttribute);

                            if ($attribute_value == null || $attribute_value == '')
                                continue;

                            $Attribute_array[] = array(
                                'attribute_id' => $setAttributeId,
                                'attribute_value' => $attribute_value);

                            break;
                        }
                    }
            }
        }

        return $Attribute_array;
    }

    public function createProduct($product , $childPrice , $parent_image,$parent_prod_id,$parent_name,$parent_desc){

        $globalVariantAttribute = $this->getGlobalVariantAttributeMapping();
        $fullfillmentnodeid = $this->_jetFulfillmentNode;
        $result=array();
        $node=array();
        $inventory=array();
        $price=array();
        $relationship = array();
        $msg = array();
        $profile = false;
        //$brand_attr_type = $this->getattributeType($this->_jetRequiredBrand);
        //$val_count_manufacture =  $this->getattributeType($this->_jetRequiredManufacturer);
        //$val_multipack = $this->getattributeType($this->_jetRequiredMultipackQuantity);

        if(is_numeric($product))
            $product = Mage::getModel('catalog/product')->load($product);


        if($product instanceof Mage_Catalog_Model_Product) {
            $profile = $this->getProductProfile($product);
            $mapping = json_decode($profile->getProfileAttributeMapping(), true);
            $mappedAttribute = array_filter(array_map(function ($n) {
                if (isset($n['magento_attribute_code']) && $n['magento_attribute_code'] != '') return $n;
            }, $mapping['mapped_attribute']));


            if ($product->getTypeId() == "configurable") {
                $jetAttrId = array();
                $sProductSku = array();

                $sku = $this->getMainProductSku($product);
                $par_brand_name = "";
                if (!$sku) {
                    $msg['type'] = 'error';
                    $msg['data'] = 'Does not contain the main product ' . $product->getName() . ' So this is skipped from upload';
                    return $msg;
                }

                $par_pro = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

                if ($par_pro instanceof Mage_Catalog_Model_Product) {
                    $par_brand_name = $this->getProductBrand($par_pro);
                }

                if ($par_brand_name == "") {
                    $msg['type'] = 'error';
                    $msg['data'] = 'Does not contain Brand Name in Child Product  main product ' . $par_pro->getName() . ' of Parent product ' . $product->getName() . ' So this is skipped from upload.';
                    return $msg;

                }


                $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
                //If only one product in child no need to make relation variation
                foreach ($childProducts as $chp) {
                    $chd_pro = Mage::getModel('catalog/product')->load($chp->getId());
                    $chd_brand_name = $this->getProductBrand($chd_pro);
                    if ($par_brand_name != trim($chd_brand_name)) {
                        $msg['type'] = 'error';
                        $msg['data'] = 'Does not matching Brand Name in Child Product "' . $chd_pro->getName() . '" with Product "' . $par_pro->getName() . '" of Parent product ' . $product->getName() . ' .So this is skipped from upload.';
                        return $msg;
                    }
                }
                foreach ($childProducts as $chp) {
                    if ($resultData = $this->createProduct($chp->getId(), $childPrice, $parent_image,$parent_prod_id,$parent_name,$parent_desc)) {
                        $result = Mage::helper('jet/jet')->Jarray_merge($result, $resultData['merchantsku']);
                        $price = Mage::helper('jet/jet')->Jarray_merge($price, $resultData['price']);
                        $inventory = Mage::helper('jet/jet')->Jarray_merge($inventory, $resultData['inventory']);
                        $sProductSku[] = $chp->getSku();
                    }
                }
                $sku = current($sProductSku);
                $sProductSku = array_values(array_diff($sProductSku, array($sku)));

                if (count($sProductSku) > 0) {

                    $productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
                    $jetAttributeMapping = array();

                    foreach ($productAttributeOptions as $productAttribute) {
                        $found = false;
                        foreach ($mappedAttribute as $map) {
                            if ($productAttribute['attribute_code'] == $map['magento_attribute_code']) {
                                $jetAttributeMapping[$map['jet_attribute_id']] = $map['magento_attribute_code'];
                                $jetAttrId[] = $map['jet_attribute_id'];
                                $found = true;
                                break;
                            }
                        }
                        if (!$found)
                            foreach ($globalVariantAttribute as $map) {
                                if ($productAttribute['attribute_code'] == $map['magento_attribute_code']) {
                                    $jetAttributeMapping[$map['jet_attribute_id']] = $map['magento_attribute_code'];
                                    $jetAttrId[] = $map['jet_attribute_id'];
                                    $found = true;
                                    break;
                                }
                            }
                        if (!$found) {
                            //not found any attribute map
                            $msg['type'] = 'error';
                            $msg['data'] = 'Magento Attribute ' . $productAttribute['attribute_code'] . ' does not mapped with Jet Attribute ' . $productAttribute['attribute_code'] . ' Kindly map attribute from Jet->Configuration->Variant Attribute ->' . $productAttribute['attribute_code'] . ' Note : Product ' . $product->getName() . ' .variation is skipped from upload.';
                            return $msg;
                        }

                    }


                    $relationship[$sku]['relationship'] = "Variation";
                    $relationship[$sku]['variation_refinements'] = $jetAttrId;
                    $relationship[$sku]['children_skus'] = $sProductSku;

                }
                $prodData = array();
                $prodData['merchantsku'] = $result;
                $prodData['price'] = $price;
                $prodData['inventory'] = $inventory;
                $prodData['type'] = 'success';
                if (count($relationship) > 0)
                    $prodData['relationship'] = $relationship;

                return $prodData;

            } else if ($product->getTypeId() == "grouped") {
                $msg['type'] = 'error';
                $msg['data'] = 'product type not supported on Jet .';
                return $msg;

            } else if ($product->getTypeId() == "bundle") {
                $msg['type'] = 'error';
                $msg['data'] = 'product type not supported on Jet .';
                return $msg;
            }
            
            $SKU_Array = array();
            $ean = NULL;


            $is_variation = false;

            $identifier =  $product->getData('standard_identifier');
            $standardCodes =array();
            $asin = "";

           
            if(is_array($identifier) || count($identifier)>0){
                foreach ($identifier as $value){
                    $barcode  = Mage::helper('jet/barcodevalidator');
                    $localValid = true;
                    if(isset($value['identifier']) && $value['identifier'] == 'ASIN' ) {

                        if (!$barcode->isAsin($value['value'])) {
                            $validate = false;
                            $err_msg = "Error in Product: " . $product->getName() . " ASIN should be correct and 10 digit alphanumeric";
                            $localValid = false;
                        } else {
                            $asin = $value['value'];
                        }
                    }
                    else if(isset($value['identifier']) && ($value['identifier'] == 'ISBN-10' || $value['identifier'] == 'ISBN-13')){
                        if(!$barcode->findIsbn($value['value'])){
                            $validate = false;
                            $err_msg = "Error in Product Sku: ".$product->getSku()." <b>".$value['identifier']."</b> is not valid";
                            $localValid = false;
                        }
                    }else{
                        $barcode->setBarcode($value['value']);
                        if(!$barcode->isValid()){
                            $validate = false;
                            $err_msg = "Error in Product Sku: ".$product->getSku()." <b>".$value['identifier']."</b> is not valid";
                            $localValid = false;
                        }
                    }

                    if($localValid){ 
                        $standardCodes[] = array('standard_product_code_type' => $value['identifier'],
                            'standard_product_code' => $value['value']);

                    }
                }
            }


            //check identifier if not found at product level then check on global
            if(count($standardCodes)==0){
                foreach ($this->getGlobalIdentifierAttributeMapping() as $code => $attribute){
                    $value = $product->getData($attribute);
                    if(!$value || $value=='')
                        continue;
                    $barcode  = Mage::helper('jet/barcodevalidator');
                    $localValid = true;
                    if($code == 'ASIN' ) { 
                        if (!$barcode->isAsin($value)) { 
                            $validate = false;
                            $err_msg = "Error in Product: " . $product->getName() . " ASIN should be correct and 10 digit alphanumeric";
                            $localValid = false;
                        } else { 
                            $asin = $value;
                        }
                    }
                    else if( $code == 'ISBN-10' || $code == 'ISBN-13'){
                        if(!$barcode->findIsbn($value)){
                            $validate = false;
                            $err_msg = "Error in Product Sku: ".$product->getSku()." <b>".$code."</b> is not valid";
                            $localValid = false;
                        }
                    }else{
                        $barcode->setBarcode($value);
                        if(!$barcode->isValid()){
                            $validate = false;
                            $err_msg = "Error in Product Sku: ".$product->getSku()." <b>".$code."</b> is not valid";
                            $localValid = false;
                        }
                    }

                    if($localValid){
                        $standardCodes[] = array('standard_product_code_type' => $code,
                            'standard_product_code' => $value);
                    }
                }
            }

            //set mfpn
            $mfrp_exist = false;
            $manu_part_number = $this->getProductMFPN($product);
            if ($manu_part_number != null) {
                $mfrp_exist = true;
            }

            $brand = $this->getProductBrand($product);

            $validate = true;

            if ($brand == NULL || trim($brand) == '') {
                $validate = false;
                $err_msg = "Error in Product: " . $product->getName() . " Brand information Required & One of these values(UPC, EAN,GTIN-14,ISBN-13,ISBN-10) OR ASIN OR Manufacturer Part Number are Required.";

            }


            $cat_error = '';
            $cate_validate = true;
            $cats = $product->getCategoryIds();
            $prd_browser_nodeid = $profile->getNodeId();
            $profileAttribute = json_decode($profile->getProfileAttributeMapping());

            if($validate==false){
                $msg['type'] = 'error';
                $msg['data'] = $err_msg ;
                return $msg;

                /*$batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();

                if(count($batcherror)>0){
                    if(array_key_exists($product->getId(),$batcherror)){
                        $batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
                        $batcherror[$product->getId()]['sku']=$product->getSku();
                    }else{
                        $batcherror[$product->getId()]['error']=$err_msg;
                        $batcherror[$product->getId()]['sku']=$product->getSku();
                    }
                    Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
                }
                Mage::getSingleton('core/session')->addError($err_msg);*/

            } else {  
                $more = array();
                $more = $this->moreProductAttributesData($product);
                $SKU_Array = Mage::helper('jet/jet')->Jarray_merge($SKU_Array, $more);

                $sku = $product->getSku();

                $SKU_Array['product_title'] = $this->getProductTitle($product,$parent_name);

                if (strlen($SKU_Array['product_title']) < 5) {
                    $msg['type'] = 'error';
                    $msg['data'] = 'product title length must be equal or greater than 5';
                    return $msg;
                }

                //set description
                $description = $this->getProductDescription($product,$parent_desc);
                if (strlen($description) == 0) {
                    $msg['type'] = 'error';
                    $msg['data'] = 'product description not found';
                    return $msg;
                }


                $SKU_Array['product_description'] = addslashes($description);
                $SKU_Array['brand'] = substr($brand, 0, 100);
                if ($asin != null && $asin!='') {
                    $SKU_Array['ASIN'] = $asin;
                }
                if (is_array($standardCodes) && count($standardCodes)>0) {
                    if (is_array($standardCodes) && count($standardCodes)>0) {
                        foreach ($standardCodes as $key => $value) {
                           if($value['standard_product_code_type']!='ASIN')
                                {
                                 $SKU_Array['standard_product_codes'] = $standardCodes;
                                }
                        }
                    
                }
                }
                
                if ($mfrp_exist) {
                    $SKU_Array['mfr_part_number'] = substr($manu_part_number, 0, 50);
                }

                $SKU_Array['jet_browse_node_id'] = $prd_browser_nodeid;

                $multipack = $this->getProductGeneralAttribute($product, $this->_jetRequiredMultipackQuantity);
                if (is_numeric($multipack) && $multipack > 0 && $multipack < 129) {
                    $SKU_Array['multipack_quantity'] = (int)$multipack;
                } else {
                    $SKU_Array['multipack_quantity'] = 1;
                }


                //set manufacturer
                if ($this->_jetRequiredManufacturer == 'country_of_manufacture') {
                    $country_name = Mage::app()->getLocale()->getCountryTranslation($product->getData('country_of_manufacture'));
                    $country_name = substr($country_name, 0, 100);
                    if ($country_name != '') {
                        $SKU_Array['manufacturer'] = "$country_name";
                    }
                } else {
                    $country_manufact = $this->getProductGeneralAttribute($product, $this->_jetRequiredManufacturer);

                    if ($country_manufact != '') {
                        $SKU_Array['manufacturer'] = "$country_manufact";
                    }
                }

                //set main image
                $image = Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage());
                $image1 = explode("/", $image);
              
                if ($parent_image != '') {
                        $SKU_Array['main_image_url'] = $parent_image;
                    } 
                    else
                    {
                        if (end($image1) != 'no_selection') 
                        {
                            $SKU_Array['main_image_url'] = $image;
                        } 
                        else {
                        $msg['type'] = 'error';
                        $msg['data'] = 'product image not found ';
                        return $msg;
                        }
                    }

                //set alternate images
                $_images = $product->getMediaGalleryImages();
                $jet_image_slot = 1;
                $jetalternat_image = array();
                foreach ($_images as $alternat_image) {
                    if ($alternat_image->getUrl() != '' && $alternat_image->getUrl() != $image) {
                        $SKU_Array['alternate_images'][] = array('image_slot_id' => $jet_image_slot,
                            'image_url' => $alternat_image->getUrl()
                        );
                        $jet_image_slot++;
                        if ($jet_image_slot > 7)
                            break;

                    }
                }




                $compt_price_config = $this->_jetRePriceActive;

                $Attribute_array = $this->prepareProductJetAttribute($product, $profile,$parent_prod_id);

                if (!empty($SKU_Array)) {
                    $SKU_Array['attributes_node_specific'] = $Attribute_array;
                    $result[$sku] = $SKU_Array;
                    $product_price = Mage::helper('jet/jet')->getJetPrice($product);

                    $node['fulfillment_node_id'] = "$fullfillmentnodeid";

                    if ($childPrice) {
                        //code start for compt price

                        if ($compt_price_config) {

                            $compt_price = Mage::helper('jet/jet')->getComptPrice($childPrice[$sku], $sku, $product);
                            $node['fulfillment_node_price'] = $compt_price;
                            $price[$sku]['price'] = $compt_price;

                        } else {
                            $node['fulfillment_node_price'] = $childPrice[$sku];
                            $price[$sku]['price'] = $childPrice[$sku];
                        }

                    } else {

                        //code start for compt price

                        if ($compt_price_config) {

                            $compt_price = Mage::helper('jet/jet')->getComptPrice($product_price, $sku, $product);
                            $node['fulfillment_node_price'] = $compt_price;
                            $price[$sku]['price'] = $compt_price;

                        } else {
                            $node['fulfillment_node_price'] = $product_price;
                            $price[$sku]['price'] = $product_price;
                        }


                    }
                    $price[$sku]['fulfillment_nodes'][] = $node;

                    $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                    $qty = 0;
                    if($stock->getIsInStock())
                        $qty = (int)$stock->getQty();

                    $node1['fulfillment_node_id'] = "$fullfillmentnodeid";

                    if ($qty < 0) {
                        $node1['quantity'] = 0;
                        $msg['type'] = 'error';
                        $msg['data'] = 'product is out of stock';
                        return $msg;
                    } else {
                        $node1['quantity'] = $qty;
                    }
                    $inventory[$sku]['fulfillment_nodes'][] = $node1;

                    $prodData = array();
                    $prodData['merchantsku'] = $result;
                    $prodData['price'] = $price;
                    $prodData['inventory'] = $inventory;
                    $prodData['type'] = 'success';

                    return $prodData;
                }
            }
        }
        else{
            return false;
        }
    }

    public function createProductOnJet($product , $childPrice , $parent_image,$parent_prod_id,$parent_name,$parent_desc){

        $globalVariantAttribute = $this->getGlobalVariantAttributeMapping();

        $profile = false;
        $fullfillmentnodeid = $this->_jetFulfillmentNode;

        $result=array();
        $node=array();
        $inventory=array();
        $price=array();
        $relationship = array();
        $msg = array();

        //$brand_attr_type = $this->getattributeType($this->_jetRequiredBrand);

        if(is_numeric($product))
            $product = Mage::getModel('catalog/product')->load($product);


        if($product instanceof Mage_Catalog_Model_Product){
            $profile = $this->getProductProfile($product);
            if($product->getTypeId() == "configurable"){
                $jetAttrId = array();
                $sProductSku = array();
                $sku = false;
                $sku = $this->getMainProductSku($product);
                
                $par_brand_name="";
                if(!$sku){
                    $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
                    $err_msg="Does not contain the main product ".$product->getName().". So this is skipped from upload.";
                    if(count($batcherror)>0){
                        if(array_key_exists($product->getId(),$batcherror)){
                            $batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
                            $batcherror[$product->getId()]['sku']=$product->getSku();
                        }else{
                            $batcherror[$product->getId()]['error']=$err_msg;
                            $batcherror[$product->getId()]['sku']=$product->getSku();
                        }
                        Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
                    }
                    Mage::getSingleton('core/session')->addError('Does not contain the main product '.$product->getName().' So this is skipped from upload');

                    return;
                }

                $par_pro=Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

                if($par_pro instanceof Mage_Catalog_Model_Product){
                    $par_brand_name =  $this->getProductBrand($par_pro);
                }

                if($par_brand_name==""){
                    $batcherror=array();
                    $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
                    $err_msg="";
                    $err_msg='Does not contain Brand Name in Child Product  main product '.$par_pro->getName().' of Parent product '.$product->getName().' .So this is skipped from upload.';
                    if(count($batcherror)>0){
                        if(array_key_exists($product->getId(),$batcherror)){
                            $batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
                            $batcherror[$product->getId()]['sku']=$product->getSku();
                        }else{
                            $batcherror[$product->getId()]['error']=$err_msg;
                            $batcherror[$product->getId()]['sku']=$product->getSku();
                        }
                        Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
                    }

                    Mage::getSingleton('core/session')->addError('Does not contain Brand Name in Child Product  main product '.$par_pro->getName().' of Parent product '.$product->getName().' So this is skipped from upload.');
                    return;
                }


                $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
                //If only one product in child no need to make relation variation
                foreach($childProducts as $chp){
                    $chd_pro=Mage::getModel('catalog/product')->load($chp->getId());
                    $chd_brand_name =  $this->getProductBrand($chd_pro);

                    if($par_brand_name != trim($chd_brand_name) ){
                        $batcherror=array();
                        $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
                        $err_msg="";
                        $err_msg='Does not matching Brand Name in Child Product "'.$chd_pro->getName().'" with Product "'.$par_pro->getName().'" of Parent product "'.$product->getName().'" .So this is skipped from upload.';
                        if(count($batcherror)>0){
                            if(array_key_exists($product->getId(),$batcherror)){
                                $batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
                                $batcherror[$product->getId()]['sku']=$product->getSku();
                            }else{
                                $batcherror[$product->getId()]['error']=$err_msg;
                                $batcherror[$product->getId()]['sku']=$product->getSku();
                            }
                            Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
                        }
                        Mage::getSingleton('core/session')->addError('Does not matching Brand Name in Child Product "'.$chd_pro->getName().'" with Product "'.$par_pro->getName().'" of Parent product "'.$product->getName().'" .So this is skipped from upload.');

                        return;
                    }
                }

                foreach($childProducts as $chp){
                    if($resultData = $this->createProductOnJet($chp->getId() , $childPrice ,$parent_image,$parent_prod_id,$parent_name,$parent_desc)){
                        if($resultData['type']=='error')
                        {
                            Mage::getSingleton('core/session')->addError($resultData['data']);
                        }
                        else
                        {
                            $result = Mage::helper('jet/jet')->Jarray_merge ($result, $resultData['merchantsku']);
                            $price = Mage::helper('jet/jet')->Jarray_merge($price, $resultData['price']);
                            $inventory =  Mage::helper('jet/jet')->Jarray_merge($inventory, $resultData['inventory']);
                            $sProductSku[] = $chp->getSku();
                        }
                    }
                }

                $sProductSku = array_values(array_diff( $sProductSku, array($sku))) ;
                
                
                

                if(count($sProductSku)>0){

                    $productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
                    $mapping = json_decode($profile->getProfileAttributeMapping(), true);
                    $mappedA = (isset($mapping['mapped_attribute']))?$mapping['mapped_attribute']:array();
                    $mappedAttribute = array_filter(array_map(function($n) { if(isset($n['magento_attribute_code']) && $n['magento_attribute_code']!='') return $n; }, $mappedA));


                    $jetAttributeMapping = array();
                    foreach ($productAttributeOptions as $productAttribute) {
                        $found = false;
                        foreach ($mappedAttribute as $map){
                            if($productAttribute['attribute_code'] == $map['magento_attribute_code']){
                                $jetAttrId[] = (int)$map['jet_attribute_id'];
                                $found = true;
                                break;
                            }
                        }
                        if(!$found)
                            foreach ($globalVariantAttribute as $map){
                                if($productAttribute['attribute_code'] == $map['magento_attribute_code']){
                                    $jetAttrId[] = (int)$map['jet_attribute_id'];
                                    $found = true;
                                    break;
                                }
                            }
                    if(!$found) {
                        //not found any attribute map
                        $msg['type'] = 'error';
                        $msg['data'] = 'Magento Attribute ' . $productAttribute['attribute_code'] . ' does not mapped with Jet Attribute ' . $productAttribute['attribute_code'] . ' Kindly map attribute from Jet->Configuration->Variant Attribute ->' . $productAttribute['attribute_code'] . ' Note : Product ' . $product->getName() . ' .variation is skipped from upload.';
                        return $msg;
                    }
                }
                    $relationship[$sku]['relationship']= "Variation";
                    $relationship[$sku]['variation_refinements']= $jetAttrId;
                    $relationship[$sku]['children_skus']= $sProductSku;

                }
                $prodData = array();
                $prodData['merchantsku'] = $result;
                $prodData['price'] = $price;
                $prodData['inventory'] = $inventory;

                if(count($relationship)>0)
                    $prodData['relationship'] = $relationship;
                
                return $prodData;

            }
            else if($product->getTypeId() == "grouped"){
                $msg['type'] = 'error';
                $msg['data'] = 'product type not supported on Jet .' ;
                return $msg;

            }
            else if($product->getTypeId() == "bundle"){
                $jetAttrId = array();
                $sProductSku = array();
                $sku =$product->getSku();
                $par_brand_name="";
                $par_pro="";
                $par_pro=Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

                if($par_pro instanceof Mage_Catalog_Model_Product){
                    $par_brand_name =  $this->getProductBrand($par_pro);
                }

                if($par_brand_name==""){
                    $batcherror=array();
                    $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
                    $err_msg="";
                    $err_msg='Does not contain Brand Name in Child Product  main product '.$par_pro->getName().' of Parent product '.$product->getName().' .So this is skipped from upload.';
                    if(count($batcherror)>0){
                        if(array_key_exists($product->getId(),$batcherror)){
                            $batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
                            $batcherror[$product->getId()]['sku']=$product->getSku();
                        }else{
                            $batcherror[$product->getId()]['error']=$err_msg;
                            $batcherror[$product->getId()]['sku']=$product->getSku();
                        }
                        Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
                    }

                    Mage::getSingleton('core/session')->addError('Does not contain Brand Name in Child Product  main product '.$par_pro->getName().' of Parent product '.$product->getName().' So this is skipped from upload.');
                    return;
                }

                $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product), $product);
 
                $bundled_items = array();
                foreach($selectionCollection as $option) 
                {
                    $bundled_items[] = $option->product_id;
                }
                $childProducts = $bundled_items;
    
                
                //If only one product in child no need to make relation variation
                foreach($childProducts as $chp){
                    $chd_pro=Mage::getModel('catalog/product')->load($chp);

                    $chd_brand_name =  $this->getProductBrand($chd_pro);

                    if($par_brand_name != trim($chd_brand_name) ){
                        $batcherror=array();
                        $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
                        $err_msg="";
                        $err_msg='Does not matching Brand Name in Child Product "'.$chd_pro->getName().'" with Product "'.$par_pro->getName().'" of Parent product "'.$product->getName().'" .So this is skipped from upload.';
                        if(count($batcherror)>0){
                            if(array_key_exists($product->getId(),$batcherror)){
                                $batcherror[$product->getId()]['error']=$batcherror[$product->getId()]['error'].'<br/>'.$err_msg;
                                $batcherror[$product->getId()]['sku']=$product->getSku();
                            }else{
                                $batcherror[$product->getId()]['error']=$err_msg;
                                $batcherror[$product->getId()]['sku']=$product->getSku();
                            }
                            Mage::getSingleton('adminhtml/session')->setBatcherror($batcherror);
                        }
                        Mage::getSingleton('core/session')->addError('Does not matching Brand Name in Child Product "'.$chd_pro->getName().'" with Product "'.$par_pro->getName().'" of Parent product "'.$product->getName().'" .So this is skipped from upload.');

                        return;
                    }
                }

                foreach($bundled_items as $chp){
                    
                $chd_pro=Mage::getModel('catalog/product')->load($chp);
                    if($resultData = $this->createProductOnJet($chp , $childPrice ,$parent_image,$parent_prod_id,$parent_name,$parent_desc)){     

                        $result = Mage::helper('jet/jet')->Jarray_merge ($result, $resultData['merchantsku']);
                        $price = Mage::helper('jet/jet')->Jarray_merge($price, $resultData['price']);
                        $inventory =  Mage::helper('jet/jet')->Jarray_merge($inventory, $resultData['inventory']);
                        $sProductSku[] = $chd_pro->getSku();
                        
                    }
                }

                $prodData = array();
                $prodData['merchantsku'] = $result;
                $prodData['price'] = $price;
                $prodData['inventory'] = $inventory;
                
                return $prodData;
            }
           
            
            $SKU_Array= array();
            $ean = NULL;

            $validate = true;
            $identifier =  $product->getData('standard_identifier');
            $standardCodes =array();
            $asin = "";
            $err_msg = '';
            if(is_array($identifier) || count($identifier)>0){
                foreach ($identifier as $value){
                    $barcode  = Mage::helper('jet/barcodevalidator');
                    $localValid = true;
                    if(isset($value['identifier']) && $value['identifier'] == 'ASIN' ) {
                            if (!$barcode->isAsin($value['value'])) {
                                $validate = false;
                                $err_msg .= "Error in Product: " . $product->getName() . " ASIN must be of 10 digits <br />";
                                $localValid = false;
                            } else {
                                $asin = $value['value'];
                            }
                        }
                    else if(isset($value['identifier']) && ($value['identifier'] == 'ISBN-10' || $value['identifier'] == 'ISBN-13')){
                        if(!$barcode->findIsbn($value['value'])){
                            $validate = false;
                            $err_msg .= "Error in Product Sku: ".$product->getSku()." <b>".$value['identifier']."</b> is not valid <br />";
                            $localValid = false;
                        }
                    }else{
                        $barcode->setBarcode($value['value']);
                        if(!$barcode->isValid()){
                            $validate = false;
                            $err_msg .= "Error in Product Sku: ".$product->getSku()." <b>".$value['identifier']."</b> is not valid <br />";
                            $localValid = false;
                        }
                    }

                    if($localValid){
                        $standardCodes[] = array('standard_product_code_type' => $value['identifier'],
                            'standard_product_code' => $value['value']);
                    }
                }
            }


            //check identifier if not found at product level then check on global
            if(count($standardCodes)==0){
                foreach ($this->getGlobalIdentifierAttributeMapping() as $code => $attribute){
                    $value = $product->getData($attribute);
                    if(!$value || $value=='')
                        continue;
                    $barcode  = Mage::helper('jet/barcodevalidator');
                    $localValid = true;
                    if($code == 'ASIN' ) {
                        if (!$barcode->isAsin($value)) {
                            $validate = false;
                            $err_msg .= "Error in Product: " . $product->getName() . " ASIN must be of 10 digits";
                            $localValid = false;
                        } else {
                            $asin = $value;
                        }
                    }
                    else if( $code == 'ISBN-10' || $code == 'ISBN-13'){
                        if(!$barcode->findIsbn($value)){
                            $validate = false;
                            $err_msg .= "Error in Product Sku: ".$product->getSku()." <b>".$code."</b> is not valid";
                            $localValid = false;
                        }
                    }else{
                        $barcode->setBarcode($value);
                        if(!$barcode->isValid()){
                            $validate = false;
                            $err_msg .= "Error in Product Sku: ".$product->getSku()." <b>".$code."</b> is not valid";
                            $localValid = false;
                        }
                    }

                    if($localValid){
                        $validate =true;
                        $standardCodes[] = array('standard_product_code_type' => $code,
                            'standard_product_code' => $value);
                    }
                }
            }

            $mfrp_exist =false;

            $manu_part_number  = $this->getProductMFPN($product);

            if($manu_part_number!=null){
                $mfrp_exist = true;
            }

            $brand =  $this->getProductBrand($product);



            if($brand==NULL || trim($brand)==''){
                $validate =false;
                $err_msg .= "Error in Product: ".$product->getName()." Brand information Required & One of these values(UPC, EAN,GTIN-14,ISBN-13,ISBN-10) OR ASIN OR Manufacturer Part Number are Required. <br />";

            }

            if(count($standardCodes)==0){
                $validate =false;
                $err_msg .= "Error in Product Sku: <b>'".$product->getSku()."'</b> Standard Identifier is required please set Identifier values(UPC, EAN,GTIN-14,ISBN-13,ISBN-10) OR ASIN <br>";

            }

            $cat_error ='';
            $cate_validate =true;

            if($profile){
                $prd_browser_nodeid = $profile->getNodeId();
            }else{
                $cate_validate =false;
                $cat_error = 'SKU: <b>'.$product->getSku().'</b></br> Rejected : Product is not Assigned to Any Profile ';
            }
            $batcherror=array();
            if($validate==false){

                $msg['type'] = 'error';
                $msg['data'] = $err_msg ;
                return $msg;


            } else {
                $more = $this->moreProductAttributesData($product);
                $SKU_Array = Mage::helper('jet/jet')->Jarray_merge($SKU_Array, $more);

                $sku = $product->getSku();


                $SKU_Array['product_title'] = $this->getProductTitle($product,$parent_name);
                if (strlen($SKU_Array['product_title']) < 5) {
                    $msg['type'] = 'error';
                    $msg['data'] = 'product title length must be equal or greater than 5';
                    return $msg;
                }

                $description = $this->getProductDescription($product,$parent_desc);
                //$description = strip_tags($description);

                if (strlen($description) == 0) {
                    $msg['type'] = 'error';
                    $msg['data'] = 'product description not found';
                    return $msg;
                }


                $SKU_Array['product_description'] = addslashes($description);
                $SKU_Array['brand'] = substr($brand, 0, 100);
                if ($asin != null && $asin!='') {
                    $SKU_Array['ASIN'] = $asin;
                }
                if (is_array($standardCodes) && count($standardCodes)>0) {
                    foreach ($standardCodes as $key => $value) {
                    if($value['standard_product_code_type']!='ASIN')
                    {
                        $SKU_Array['standard_product_codes'] = $standardCodes;
                    }
                }
                }

                if ($mfrp_exist) {
                    $SKU_Array['mfr_part_number'] = substr($manu_part_number, 0, 50);
                }
                //set category
                $SKU_Array['jet_browse_node_id'] = $prd_browser_nodeid;

                //set multipack
                $multipack = $this->getProductGeneralAttribute($product, $this->_jetRequiredMultipackQuantity);
                if (is_numeric($multipack) && $multipack > 0 && $multipack < 129) {
                    $SKU_Array['multipack_quantity'] = (int)$multipack;
                } else {
                    $SKU_Array['multipack_quantity'] = 1;
                }

                //set Country of Manufacturer
                if ($this->_jetRequiredManufacturer == 'country_of_manufacture') {
                    $country_name = Mage::app()->getLocale()->getCountryTranslation($product->getData('country_of_manufacture'));
                    $country_name = substr($country_name, 0, 100);
                    if ($country_name != '') {
                        $SKU_Array['manufacturer'] = "$country_name";
                    }
                } else {
                    $country_manufact = $this->getProductGeneralAttribute($product, $this->_jetRequiredManufacturer);
                    if ($country_manufact != '') {
                        $SKU_Array['manufacturer'] = "$country_manufact";
                    }
                }


                //set main media image
                $image = Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage());
                $image1 = explode("/", $image);

                if ($parent_image != '') {
                        $SKU_Array['main_image_url'] = $parent_image;
                    } 
                    else
                    {
                        if (end($image1) != 'no_selection') 
                        {
                            $SKU_Array['main_image_url'] = $image;
                        } 
                        else {
                        $msg['type'] = 'error';
                        $msg['data'] = 'product image not found ';
                        return $msg;
                        }
                    }
                


                //set alternate images
                $_images = $product->getMediaGalleryImages();
                $jet_image_slot = 1;
                foreach ($_images as $alternat_image) {
                    if ($alternat_image->getUrl() != '' && $alternat_image->getUrl() != $image) {
                        $SKU_Array['alternate_images'][] = array('image_slot_id' => $jet_image_slot,
                            'image_url' => $alternat_image->getUrl()
                        );
                        $jet_image_slot++;
                        if ($jet_image_slot > 7)
                            break;
                    }
                }


                //get all attributes
                $Attribute_array = $this->prepareProductJetAttribute($product, $profile,$parent_prod_id);

                $compt_price_config = $this->_jetRePriceActive;
                if (!empty($SKU_Array)) {
                    $SKU_Array['attributes_node_specific'] = $Attribute_array;
                    $result[$sku] = $SKU_Array;
                    $product_price = Mage::helper('jet/jet')->getJetPrice($product);

                    $node['fulfillment_node_id'] = "$fullfillmentnodeid";

                    if ($childPrice) {
                        //Set Price for the product
                        if ($compt_price_config) {
                            $compt_price = Mage::helper('jet/jet')->getComptPrice($childPrice[$sku], $sku, $product);
                            $node['fulfillment_node_price'] = $compt_price;
                            $price[$sku]['price'] = $compt_price;
                        } else {
                            $node['fulfillment_node_price'] = $childPrice[$sku];
                            $price[$sku]['price'] = $childPrice[$sku];
                        }
                    } else {
                        //code start for compt price
                        if ($compt_price_config) {
                            $compt_price = Mage::helper('jet/jet')->getComptPrice($product_price, $sku, $product);
                            $node['fulfillment_node_price'] = $compt_price;
                            $price[$sku]['price'] = $compt_price;
                        } else {
                           
                            
                            
                            $node['fulfillment_node_price'] = $product_price;
                            $price[$sku]['price'] = $product_price;
                            
                           
                        }
                    }
                    $price[$sku]['fulfillment_nodes'][] = $node;


                    $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);

                    //if out of stock send 0 quantity
                    $qty = 0;
                    if($stock->getIsInStock())
                        $qty = (int)$stock->getQty();

                    $node1['fulfillment_node_id'] = "$fullfillmentnodeid";

                    if ($qty < 0) {
                        $node1['quantity'] = 0;
                        $msg['type'] = 'error';
                        $msg['data'] = 'product is out of stock';
                        return $msg;
                    } else {
                        $node1['quantity'] = $qty;
                    }
                    $inventory[$sku]['fulfillment_nodes'][] = $node1;

                    $prodData = array();
                    $prodData['merchantsku'] = $result;
                    $prodData['price'] = $price;
                    $prodData['inventory'] = $inventory;
                    $prodData['type'] = 'success';

                    return $prodData;
                }
            }
        }
        else{
            return false;
        }

    }


    public function saveBatchData($index=""){
                $date="";
                $date = date('Y-m-d H:i:s');
                $batcherror=array();
                $batcherror=Mage::getSingleton('adminhtml/session')->getBatcherror();
                foreach($batcherror as $key=>$val){
                        $id=$key;
                        if($val['error'] !=""){
                            $batch_id=0;
                            $batch_id=$this->getBatchIdFromProductId($id);
                            //$model="";
                            //$model=Mage::getModel('catalog/product')->load($id);
                            if($batch_id){
                                        $batchmod='';

                                        $batchmod=Mage::getModel('jet/batcherror')->load($batch_id);
                                        $batchmod->setData("batch_num",$index);
                                        $batchmod->setData("is_write_mode",'0');
                                        $batchmod->setData("error",$val['error']);
                                        $batchmod->setData('product_sku',$val['sku']);
                                        $batchmod->setData("date_added",$date);
                                        $batchmod->save();
                            }else{
                                        $batchmod='';
                                        $batchmod=Mage::getModel('jet/batcherror');
                                        $batchmod->setData('product_id',$id);
                                        $batchmod->setData('product_sku',$val['sku']);
                                        $batchmod->setData("is_write_mode",'0');
                                        $batchmod->setData("error",$val['error']);
                                        $batchmod->setData("batch_num",$index);
                                        $batchmod->setData("date_added",$date);
                                        $batchmod->save();
                            }
                        }else{
                            $batch_id=0;
                            $batch_id=$this->getBatchIdFromProductId($id);
                            $err="";
                            if($batch_id){
                                        $batchmod='';
                                        $batchmod=Mage::getModel('jet/batcherror')->load($batch_id);
                                        $batchmod->setData("batch_num",$index);
                                        $batchmod->setData("is_write_mode",'0');
                                        $batchmod->setData("error",$err);
                                        $batchmod->setData('product_sku',$val['sku']);
                                        $batchmod->setData("date_added",$date);
                                        $batchmod->save();
                            }
                        }
                }
                Mage::getSingleton('adminhtml/session')->unsBatcherror();
    }
    
    public function generateCreditMemoForRefund($details_after_saved=''){
        if($details_after_saved && count($details_after_saved)>0){
            $sku_details="";
            $sku_details=$details_after_saved['sku_details'];
            $item_details=array();
            $merchant_order_id="";
            $merchant_order_id=$details_after_saved['refund_orderid'];
            $shipping_amount=0;
            $adjustment_positive=0;
            foreach($sku_details as $detail){
                    if($detail['refund_quantity']>0 && $detail['return_quantity']>=$detail['refund_quantity'] && $detail['refund_quantity']<=$detail['available_to_refund_qty']){
                        $item_details[]=array('sku'=>$detail['merchant_sku'],'refund_qty'=>$detail['refund_quantity']);
                        $return_shipping_cost=0;
                        $return_shipping_tax=0;
                        $return_tax=0;
                        if($detail['return_shipping_cost']!="" && is_numeric ($detail['return_shipping_cost'])){
                                $return_shipping_cost=(float)trim($detail['return_shipping_cost']);
                        }
                        if($detail['return_tax']!="" && is_numeric ($detail['return_tax'])){
                                $return_tax=(float)trim($detail['return_tax']);
                        }
                        if($detail['return_shipping_tax']!="" && is_numeric ($detail['return_shipping_tax'])){
                                $return_shipping_tax=(float)trim($detail['return_shipping_tax']);
                        }
                        $shipping_amount=$shipping_amount+$return_shipping_cost+$return_shipping_tax;
                        $adjustment_positive=$adjustment_positive+$return_tax;
                    }
            }
            $collection="";
            $magento_order_id='';
            $collection=Mage::getModel('jet/jetorder')->getCollection();
            $collection->addFieldToFilter( 'merchant_order_id', $merchant_order_id );
            if($collection->count()>0){
                foreach($collection as $coll){
                            $magento_order_id=$coll->getData('magento_order_id');
                            break;
                }   
            }
            if($magento_order_id !=''){
                try {
                        $order ="";
                        $order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
                        if (!$order->getId()) {
                            Mage::getSingleton('adminhtml/session')->addError("Order not Exists.Can't generate Credit Memo.");
                            return false;
                            
                        }
                        $data=array();
                        $data['shipping_amount']=0;
                        $data['adjustment_positive']=0;
                        if($shipping_amount>0){
                                $data['shipping_amount']=$shipping_amount;
                        }
                        if($adjustment_positive>0){
                                $data['adjustment_positive']=$adjustment_positive;
                        }
                        //$data['adjustment_positive']=1;
                        //$data['adjustment_negative']=2;
                        foreach ($item_details as $key => $value) {
                            $orderItem="";
                            $orderItem = $order->getItemsCollection()->getItemByColumnValue('sku', $value['sku']);
                            $data['qtys'][$orderItem->getId()]=$value['refund_qty'] ;
                        }

                        if(!array_key_exists("qtys",$data)){
                            Mage::getSingleton('adminhtml/session')->addError("Problem in Credit Memo Data Preparation.Can't generate Credit Memo.");
                            return false;
                        }
                        if($data['shipping_amount']==0)
                                    {
                                        Mage::getSingleton('adminhtml/session')->addError("Amount is 0 .So Credit Memo Not Generated.");
                                        
                                    }
                                    else
                                    {
                                        $creditmemo_api="";
                                        $creditmemo_id="";
                                        $creditmemo_api=Mage::getModel('sales/order_creditmemo_api');
                                        $comment="";
                                        $comment="Credit memo generated from Jet.com refund functionality.";
                                        $creditmemo_id=$creditmemo_api->create($magento_order_id,$data,$comment);
                                        if($creditmemo_id !="")
                                        {
                                                Mage::getSingleton('adminhtml/session')->addSuccess("Credit Memo ".$creditmemo_id." is Successfully generated for Order :".$magento_order_id.".");
                                                return true;
                                        }
                                    }
                        
                }
                catch (Mage_Core_Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage().".Can't generate Credit Memo.");
                    return false;
                    
                }

            }else{
                Mage::getSingleton('adminhtml/session')->addError("Order not found.Can't generate Credit Memo.");
                return false;
                
            }
        }else{
            Mage::getSingleton('adminhtml/session')->addError("Can't generate Credit Memo.");
            return false;
        }
    }
    

    public function getMagentoIncrementOrderId($merchant_order_id=""){
                $merchant_order_id=trim($merchant_order_id);
                if($merchant_order_id==""){
                        return 0;
                }
                try{
                        
                        $collection=Mage::getModel('jet/jetorder')->getCollection();
                        $collection->addFieldToFilter( 'merchant_order_id', $merchant_order_id );
                        if($collection->count()>0){
                            foreach($collection as $coll){
                                        $magento_order_id=$coll->getData('magento_order_id');
                                        return $magento_order_id;
                            }   
                        }
                        return 0;
                }catch(Exception $e){
                        return 0;
                }
                
    }
    public function getRefundedQtyInfo($order="",$item_sku=""){
            $item_sku=trim($item_sku);
            $check=array();
            $check['error']=1;
            if($order==""){
                    $check['error_msg']="Order not found for current item.";
                    return $check;
            }
            if($item_sku==""){
                    $check['error_msg']="Item Sku not found for current item.";
                    return $check;
            }
            if($order instanceof Mage_Sales_Model_Order){
                    $orderItem="";
                    $orderItem = $order->getItemsCollection()->getItemByColumnValue('sku',$item_sku);
                    if($orderItem instanceof Mage_Sales_Model_Order_Item){
                            $qty_ordered=0;
                            $qty_refunded=0;
                            $qty_ordered=(int)$orderItem->getData('qty_shipped');


                            $qty_refunded=(int)$orderItem->getData('qty_refunded');


                            /*$available_to_refund_qty=0;
                            $available_to_refund_qty=$qty_ordered-$qty_refunded;
                            if($available_to_refund_qty<=0){
                                    $check['error_msg']="No Qty available to refund for current item.";
                                    return $check;
                            }*/
                            //else{
                                $check['error']=0;
                                $check['qty_already_refunded']=$qty_refunded;
                                $check['available_to_refund_qty']=$qty_ordered;
                                $check['qty_ordered']=$qty_ordered;
                                return $check;
                            //}
                    }else{
                        $check['error_msg']="Item Data not available for current item.";
                        return $check;
                    }
            }else{
                $check['error_msg']="Order Data not available for current item.";
                return $check;
            }
    }

    public function prepareDataAfterSubmitReturn($details_saved_after="",$id=""){
                $skus="";
                $skus=$details_saved_after['sku_details'];
                $returnModel ="";
                $returnModel = Mage::getModel('jet/jetreturn')->load($id);
                $return_ser_data="";
                $return_ser_data=$returnModel->getData('return_details');
                $return_data="";
                $return_data=unserialize($return_ser_data);
                $magento_order_id=0;
                $magento_order_id=$this->getMagentoIncrementOrderId($return_data->merchant_order_id);
                $order ="";
                $order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
                

                $result=array();
                $result['id']=$returnModel->getData('id');
                $result['returnid']=$returnModel->getData('returnid');
                $result['merchant_order_id']=$return_data->merchant_order_id;
                $result['agreeto_return']=$details_saved_after['agreeto_return'];
                $i=0;
                foreach($return_data->return_merchant_SKUs as $sku_detail){
                            $check=array();
                            $check=$this->getRefundedQtyInfo($order,$sku_detail->merchant_sku);
                            if($check['error']=='1'){
                                    continue;
                            }
                            $flag=false;
                            foreach($skus as $key=>$detail){
                                        if($sku_detail->merchant_sku==$detail['merchant_sku'] && $detail['want_to_return'] =='1'){
                                            $result['sku_details']["sku$i"]['refund_quantity']=trim($detail['refund_quantity']);    
                                            $result['sku_details']["sku$i"]['return_refundfeedback']=trim($detail['return_refundfeedback']);
                                            $result['sku_details']["sku$i"]['return_actual_principal']=trim($detail['return_actual_principal']);
                                            $result['sku_details']["sku$i"]['want_to_return']=$detail['want_to_return'];
                                            $result['sku_details']["sku$i"]['changes_made']=0;
                                            $result['sku_details']["sku$i"]['qty_already_refunded']=$detail['qty_already_refunded'];
                                            $result['sku_details']["sku$i"]['available_to_refund_qty']=$detail['available_to_refund_qty'];
                                            $result['sku_details']["sku$i"]['qty_ordered']=$detail['qty_ordered'];
                                            $result['sku_details']["sku$i"]['order_item_id']=$detail['order_item_id'];
                                            $result['sku_details']["sku$i"]['return_quantity']=$detail['return_quantity'];
                                            $result['sku_details']["sku$i"]['merchant_sku']=$detail['merchant_sku'];
                                            $result['sku_details']["sku$i"]['return_principal']=trim($detail['return_principal']);
                                            $result['sku_details']["sku$i"]['return_tax']=trim($detail['return_tax']);
                                            $result['sku_details']["sku$i"]['return_shipping_cost']=trim($detail['return_shipping_cost']);
                                            $result['sku_details']["sku$i"]['return_shipping_tax']=trim($detail['return_shipping_tax']);
                                            $flag=true;
                                            break;
                                        }
                            }
                    if($flag){
                            $i++;
                            continue;
                    }   
                    $result['sku_details']["sku$i"]['refund_quantity']=0;   
                    $result['sku_details']["sku$i"]['return_refundfeedback']="";
                    $result['sku_details']["sku$i"]['return_actual_principal']=$sku_detail->requested_refund_amount->principal;
                    $result['sku_details']["sku$i"]['want_to_return']=0;
                    $result['sku_details']["sku$i"]['changes_made']=0;
                    $result['sku_details']["sku$i"]['qty_already_refunded']=$check['qty_already_refunded'];
                    $result['sku_details']["sku$i"]['available_to_refund_qty']=$check['available_to_refund_qty'];
                    $result['sku_details']["sku$i"]['qty_ordered']=$check['qty_ordered'];
                    $result['sku_details']["sku$i"]['order_item_id']=$sku_detail->order_item_id;
                    $result['sku_details']["sku$i"]['return_quantity']=$sku_detail->return_quantity;
                    $result['sku_details']["sku$i"]['merchant_sku']=$sku_detail->merchant_sku;
                    $result['sku_details']["sku$i"]['return_principal']=$sku_detail->requested_refund_amount->principal;
                    $result['sku_details']["sku$i"]['return_tax']=$sku_detail->requested_refund_amount->tax;
                    $result['sku_details']["sku$i"]['return_shipping_cost']=$sku_detail->requested_refund_amount->shipping_cost;
                    $result['sku_details']["sku$i"]['return_shipping_tax']=$sku_detail->requested_refund_amount->shipping_tax;
                    $i++;
                }
                return $result;
    }
    public function saveChangesMadeValue($details_saved_after=""){
            $skus="";
            $skus=$details_saved_after['sku_details'];
            foreach($skus as $key=>$detail){
                    if($detail['want_to_return'] =='1'){
                        $details_saved_after['sku_details'][$key]['changes_made']=1;
                    }
            }
            return $details_saved_after;
    }
    public function checkOrderInRefund($merchant_order_id=""){
            $merchant_order_id=trim($merchant_order_id);
            try{
                        $collection="";
                        $collection=Mage::getModel('jet/jetrefund')->getCollection()->addFieldToFilter('refund_orderid', $merchant_order_id );
                        if($collection->count()>0){
                            return true;
                        }
                        return false;
                }catch(Exception $e){
                        return false;
                }
    }
    public function checkViewCaseForReturn($details_saved_after=""){
            $skus="";
            $skus=$details_saved_after['sku_details'];
            $count=0;
            $count=count($skus);
            $i=0;
            foreach($skus as $key=>$detail){
                    if($detail['changes_made'] =='1'){
                        $i++;
                    }
            }
            if($count >0 && $count==$i){
                    return true;
            }
            return false;
    }

    public function generateCreditMemoForReturn($return_id=''){
        if($return_id !=""){
                $model ="";
                $orderId ="";
                $magento_order_id="";
                $details_after_saved="";
                $return_details="";
                $details_after_saved_result=array();
                $model = Mage::getModel('jet/jetreturn')->load($return_id);
                $return_details=$model->getData('return_details');
                $details_after_saved=$model->getData('details_saved_after');
                $details_after_saved_result=unserialize($details_after_saved);
                if(count($details_after_saved_result)==0){
                    Mage::getSingleton('adminhtml/session')->addError("Details of Return Submission are not saved.Can't generate Credit Memo.");
                    return false;
                }
                $sku_details="";
                $sku_details=$details_after_saved_result['sku_details'];
                $item_details=array();
                $shipping_amount=0;
                $adjustment_positive=0;
                foreach($sku_details as $detail){
                        if($detail['changes_made']>0 && $detail['refund_quantity']>0 && $detail['return_quantity']>=$detail['refund_quantity'] && $detail['refund_quantity']<=$detail['available_to_refund_qty']){
                            $item_details[]=array('sku'=>$detail['merchant_sku'],'refund_qty'=>$detail['refund_quantity']);
                            $return_shipping_cost=0;
                            $return_shipping_tax=0;
                            $return_tax=0;
                            if($detail['return_shipping_cost']!="" && is_numeric ($detail['return_shipping_cost'])){
                                    $return_shipping_cost=(float)trim($detail['return_shipping_cost']);
                            }
                            if($detail['return_tax']!="" && is_numeric ($detail['return_tax'])){
                                    $return_tax=(float)trim($detail['return_tax']);
                            }
                            if($detail['return_shipping_tax']!="" && is_numeric ($detail['return_shipping_tax'])){
                                    $return_shipping_tax=(float)trim($detail['return_shipping_tax']);
                            }
                            $shipping_amount=$shipping_amount+$return_shipping_cost+$return_shipping_tax;
                            $adjustment_positive=$adjustment_positive+$return_tax;
                        }
                }
                $return_details=unserialize($return_details);
                if( !is_object ($return_details)){
                        Mage::getSingleton('adminhtml/session')->addError("Details of Return are not saved.Can't generate Credit Memo.");
                        return false;
                        
                }
                $merchant_order_id="";
                $merchant_order_id=$return_details->merchant_order_id;
                $collection="";
                $collection=Mage::getModel('jet/jetorder')->getCollection();
                $collection->addFieldToFilter( 'merchant_order_id', $merchant_order_id );
                if($collection->count()>0){
                    foreach($collection as $coll){
                                $magento_order_id=$coll->getData('magento_order_id');
                                break;
                    }   
                }
                if($magento_order_id !=''){
                            try {
                                    $order ="";
                                    $order = Mage::getModel('sales/order')->loadByIncrementId($magento_order_id);
                                    if (!$order->getId()) {
                                        Mage::getSingleton('adminhtml/session')->addError("Order not Exists.Can't generate Credit Memo.");
                                        return false;
                                        
                                    }
                                    $data=array();
                                    $data['shipping_amount']=0;
                                    $data['adjustment_positive']=0;
                                    if($shipping_amount>0){
                                            $data['shipping_amount']=$shipping_amount;
                                    }
                                    if($adjustment_positive>0){
                                            $data['adjustment_positive']=$adjustment_positive;
                                    }
                                    //$data['adjustment_positive']=1;
                                    //$data['adjustment_negative']=2;
                                    foreach ($item_details as $key => $value) {
                                        $orderItem="";
                                        $orderItem = $order->getItemsCollection()->getItemByColumnValue('sku', $value['sku']);
                                        $data['qtys'][$orderItem->getId()]=$value['refund_qty'] ;
                                    }
                                    
                                    if(!array_key_exists("qtys",$data)){
                                        Mage::getSingleton('adminhtml/session')->addError("Problem in Credit Memo Data Preparation.Can't generate Credit Memo.");
                                        return false;
                                    }
                                    
                                    if($data['shipping_amount']==0)
                                    {
                                        Mage::getSingleton('adminhtml/session')->addError("Amount is 0 .So Credit Memo Not Generated.");
                                        
                                    }
                                    else
                                    {
                                        $creditmemo_api="";
                                    $creditmemo_id="";
                                    $creditmemo_api=Mage::getModel('sales/order_creditmemo_api');
                                    $comment="";
                                    $comment="Credit memo generated from Jet.com return functionality.";
                                    $creditmemo_id=$creditmemo_api->create($magento_order_id,$data,$comment);
                                    if($creditmemo_id !=""){
                                            Mage::getSingleton('adminhtml/session')->addSuccess("Credit Memo ".$creditmemo_id." is Successfully generated for Order :".$magento_order_id.".");
                                            return true;
                                        }   
                                    }
                                    
                            }
                            catch (Mage_Core_Exception $e) {
                                Mage::getSingleton('adminhtml/session')->addError($e->getMessage().".Can't generate Credit Memo.");
                                return false;
                                
                            }

                }else{
                    Mage::getSingleton('adminhtml/session')->addError("Order not found.Can't generate Credit Memo.");
                    return false;
                    
                }
                        
            }else{
                Mage::getSingleton('adminhtml/session')->addError("Can't generate Credit Memo.");
                return false;
                
            }
    }
    public function checkOrderForReturn($merchant_order_id=""){
        $merchant_order_id=trim($merchant_order_id);
        $collection=Mage::getModel('jet/jetreturn')->getCollection();
        $collection->addFieldToFilter( 'merchant_order_id', $merchant_order_id );
        if($collection->count()>0){
            foreach($collection as $coll){
                        $magento_order_id=$coll->getData('magento_order_id');
                        return true;
            }   
        }
        return false;
    }

    /** Check if Current mode is Sandbox Mode
     * @return mixed
     */
    public function  isSandboxMode(){
        return Mage::getStoreConfig('jet_options/ced_jet/sandbox');
    }

    /**
     * Check Credentials as per state
     *
     */
    public function  getCredentials(){
        if($this->isSandboxMode()){
            $this->user = Mage::getStoreConfig('jet_options/ced_jet/jet_sandbox_user');
            $this->pass = Mage::getStoreConfig('jet_options/ced_jet/jet_sandbox_userpwd');
        }else{
          $this->user = Mage::getStoreConfig('jet_options/ced_jet/jet_user');
          $this->pass = Mage::getStoreConfig('jet_options/ced_jet/jet_userpwd');
        }
        return ['user' => $this->user, 'pass' => $this->pass];
    }

    public function checkIfCredentialsValid(){
        return Mage::getStoreConfig('jet_options/ced_jet/is_credentials_valid');
    }


    public function JrequestTokenCurlVerify($usr = "", $pass = ""){

        $ch = curl_init();
        $url= $this->_apiHost.'/Token';
        $postFields='{"user":"'.$usr.'","pass":"'.$pass.'"}';

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json;"));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $server_output = curl_exec ($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($server_output, 0, $header_size);
        $body = substr($server_output, $header_size);
        curl_close ($ch);
        $token_data =json_decode($body, true);
        if(is_array($token_data) && isset($token_data['id_token'])){
            $data = new Mage_Core_Model_Config();
            $data->saveConfig('jetcom/token', $body, 'default', 0);
            return json_decode($body);
        }else{
            return false;
        }

    }

    public function getNodeAttributes($nodeId){
        $result = "{}";
        //$response =$this->CGetRequest('/taxonomy/nodes/'.$nodeId.'/attributes');
        if (!empty($nodeId)) {
            $response = Mage::getModel('jet/catlist')->load($nodeId, 'csv_cat_id')
                        ->getData();

                        if(json_decode($response['attributes'], true) == null)

                        {
                            $response =$this->CGetRequest('/taxonomy/nodes/'.$nodeId.'/attributes');
                            $result = json_decode($response,true);
                        }
                        else
                        {
                             $result = json_decode($response['attributes'],true);
                        }
           
        }
        return $result;
    }



    public function updateonjetSync($product, $childPrice)
    {

        $product_available = true;

        $fullfillmentnodeid = $this->getFulfillmentNode();

        $sku = trim($product->getSku());
        $response =$this->getProductDetail($sku);

        if (!is_array($response)) {
            $product_available = false;
        }

        if ($product_available) {
            $is_in_stock = true;

            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
            if ($stock && $stock->getIsInStock() != '1') {
                $is_in_stock = false;
            }

            if ($stock && $stock->getQty() > 0 && $is_in_stock) {
                $product_qty = $stock->getQty();
            }else{
                $product_qty = 0;
            }

            $price = Mage::helper('jet/jet')->getJetPrice($product);



            if ($product->getStatus() == false) {
                $arr = array();
                $arr['is_archived'] = true;
               $this->CPutRequest('/merchant-skus/' . $sku . '/status/archive', json_encode($arr));
            } else {
                $arr = array();
                //check if product qty is 0 then is archieve true and redirect to previous page
                $arr['is_archived'] = false;
                 $this->CPutRequest('/merchant-skus/' . $sku . '/status/archive', json_encode($arr));
            }
            if ($product->getStatus()) {

                /*-----all data update code starts----*/
                $update_image = true;
                    $alldataupdate = $this->createProductOnJet($product,false,'');

                    if (isset($alldataupdate['merchantsku']) && $alldataupdate['merchantsku'][$product->getSku()]) {
                        $updatedata = $alldataupdate['merchantsku'][$product->getSku()];
                        $finalskujson = json_encode($updatedata);
                        $newJsondata = $this->ConvertNodeInt($finalskujson);
                        $this->CPutRequest('/merchant-skus/' . $sku, $newJsondata);
                        $update_image = false;
                    }
                /*-----all data update code ends----*/
                /*-----price update code starts----*/
                    $data_var = array();
                    $fulfillment_arr = array();
                    if($childPrice)
                    {
                        $fulfillment_arr[0]['fulfillment_node_id'] = $fullfillmentnodeid;
                        $fulfillment_arr[0]['fulfillment_node_price'] = $childPrice[$sku];
                        $data_var['price'] = (float)$childPrice[$sku];
                    }
                    else
                    {
                        $fulfillment_arr[0]['fulfillment_node_id'] = $fullfillmentnodeid;
                        $fulfillment_arr[0]['fulfillment_node_price'] = $price;
                        $data_var['price'] = (float)$price;
                    }

                    $data_var['fulfillment_nodes'] = $fulfillment_arr;
                    $this->CPutRequest('/merchant-skus/' . $sku . '/price', json_encode($data_var));

                /*-----price update code ends----*/
                /*-----inventory update code starts----*/
                    $data_var = array();
                    $fulfillment_arr = array();
                    $data = "";
                    $response = '';
                    $fulfillment_arr[0]['fulfillment_node_id'] = $fullfillmentnodeid;
                    $fulfillment_arr[0]['quantity'] = (int)$product_qty;
                    if (!$is_in_stock) {
                        $fulfillment_arr[0]['quantity'] = 0;
                    }
                    $data_var['fulfillment_nodes'] = $fulfillment_arr;
                    $this->CPutRequest('/merchant-skus/' . $sku . '/inventory', json_encode($data_var));

                /*-----inventory update code ends----*/
                /*-----images update code starts----*/

                    $no_image = false;
                    if ($product->getImage() == "no_selection") {
                        $no_image = true;
                    }
                    $main_image_url = "";
                    $alt_images = array();
                    if (!$no_image) {
                        $main_image_url = $product->getImageUrl();
                    }
                    if ($main_image_url != "") {
                        $alt_images["main_image_url"] = $main_image_url;
                    }
                    $all_images = $product->getMediaGalleryImages();
                    $jet_image_slot = 1;
                    $slot = 1;
                    foreach ($all_images as $key => $alternat_image) {
                        if ($alternat_image->getUrl() != '') {
                            if (count($alt_images) == 0) {
                                $alt_images["main_image_url"] = $alternat_image->getUrl();
                            }
                            $alt_images['alternate_images'][] = array('image_slot_id' => $slot,
                                'image_url' => $alternat_image->getUrl()
                            );
                            $slot++;
                            if ($jet_image_slot > 7) {
                                break;
                            }
                            $jet_image_slot++;
                        }
                    }
                    $this->CPutRequest('/merchant-skus/' . $sku . '/image', json_encode($alt_images));

                /*-----images update code ends----*/
            }
        }
    }

    public function updateLogFileStatus($jFile){
        $response = Mage::helper('jet')->CGetRequest('/files/' . $jFile->getJetFileId());
        $resvalue = json_decode($response);
        if (trim($resvalue->status) == 'Processed with errors') {
            $status = trim($resvalue->status);
            $error = $resvalue->error_excerpt;
            $comma_separatederrors = implode(",", $error);

            $update = array('status' => trim($resvalue->status));
            $data = array(
                'status' => $status,
                'error' => $comma_separatederrors,
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $jFile->addData($data);
            $jFile->save();
        }
        else {
            $update = array('status' => trim($resvalue->status), 'error' => trim($resvalue->status));
            $jFile->addData($update);
            $jFile->save();

        }
        return $jFile;
    }
}   
