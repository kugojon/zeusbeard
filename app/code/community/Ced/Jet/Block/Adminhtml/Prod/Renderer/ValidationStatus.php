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

class Ced_Jet_Block_Adminhtml_Prod_Renderer_ValidationStatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row) 
    {
        $id=$row->getId();

        $error="";
        $date="";
        $date1="";
        $batchmod=Mage::getModel('catalog/product')->load($id);
        $status = $batchmod->getData('jet_product_validation');
        $errors = json_decode($batchmod->getData('jet_product_validation_error'), true);
        if(is_null($errors)) {
            $errors = $batchmod->getData('jet_product_validation_error');
        }

        $html='';
        if(!Mage::registry('jet_validation_flag_for_script')){
            // if($this->getRequest()->getParam('isAjax') == 'true'){
                Mage::register('jet_validation_flag_for_script', true);
                $html = '<script>
                               
                                function bindClick(){
                                    $elements = document.getElementsByClassName("jet_errors");
                                    for($index in $elements){
                                        if(typeof $elements[$index]!="function" && typeof $elements[$index]!="undefined"){
                                            $elements[$index].onclick=function(){
                                                e=this;
                                                var id = e.readAttribute("data-id");
                                                $("errors"+id).setStyle({display:"block"});
                                                oPopup = new Window({
                                                    id:"browser_window",
                                                    className: "magento",
                                                    windowClassName: "popup-window",
                                                    title: "Missing Attributes",
                                                    width: 750,
                                                    height: 200,
                                                    minimizable: false,
                                                    maximizable: false,
                                                    showEffectOptions: {
                                                    duration: 0.4
                                                    },
                                                    hideEffectOptions:{
                                                    duration: 0.4
                                                    },
                                                    destroyOnClose: true
                                                });
                                                oPopup.setZIndex(100);
                                                oPopup.showCenter(true);                    
                                                oPopup.setContent("errors"+id,false,false);
                                                $("browser_window_close").onclick = function(){
                                                    $("errors"+id).setStyle({display:"none"});
                                                    Windows.close("browser_window");
                                                }
                                            }
                                        }
                                    }
                                }
                                
                        </script>';
                        // }
        }

        if ($batchmod->getTypeId() == 'configurable' && $errors == '') {
            $sku = $batchmod->getSku();
            $productType = $batchmod->getTypeInstance();
            $products = $productType->getUsedProducts($batchmod);
            $config =false;
            $count=0;
            $validcounts = 0;
            $errors = '';
            $countOfConfigProducts = count($products);
            $childErrors = array();
            $parentValidationStatus = $batchmod->getJetProductValidation();



            foreach($products as $product)
            {
                $childProduct=Mage::getModel('catalog/product')->load($product->getId());   
                $childValidation = json_decode($childProduct->getJetProductValidationError(), true);
               if(($childProduct->getJetProductValidation() != '') && ($childProduct->getJetProductValidation() != 'valid')){
                    $temp = '<ul style="list-style:square;color:green;margin-left:50px">';
                   if(is_array($childValidation))
                    foreach ($childValidation as $key => $value) {
                       $temp .= '<li>'.$value.'</li>';
                    }

                    $temp .= '</ul>';
                    $errors[] = "SKU -- <span style='color:blue'>".$childProduct->getSku().'</span> : '.$temp;    
               }

                if($childProduct->getJetProductValidation() == '')
                    $childErrors[] = $childProduct->getSku();
            }
  
           if(count($childErrors) == $countOfConfigProducts) {
                $batchmod->setJetProductValidation('not-validated')->getResource()->saveAttribute($batchmod, 'jet_product_validation_error');
                $errors = '';
           } 
            elseif($errors == '' || is_null($errors)){
                $batchmod->setJetProductValidation('valid')->getResource()->saveAttribute($batchmod, 'jet_product_validation');
                $errors = 'valid';
            } 
        }

        if($status == 'valid'){
            $html .= '<span class="grid-severity-notice"><span>Valid</span></span>';
        }
        else if($status == 'not_validated'){
            $html .= '<span class="grid-severity-minor"><span>'.$this->__('Not Validated').'</span></span>';
        }
        else{
            // $errors = explode(',',$errors);
            $html .= "<div id='errors".$id."' style='padding:20px;display:none'><ul style='list-style:disc;color:red;'>";

            if(is_array($errors))
            foreach ($errors as $key => $error) {
                if(is_array($error)){
                    $prodId = Mage::getModel('catalog/product')->getIdBySKU($key);
                    $url= $this->getUrl('adminhtml/catalog_product/edit', array('id' => $prodId));

                    $html .= '<li style="margin-left: 30px;"><a style="color:blue" target="_blank" href="'.$url.'">'.$key.'</a></li>';
                    $html .=  '<ul style="list-style:disc;color:red; margin-left: 40px;" class="child_errors">';

                    //$html .= '<li>'.$key.'</li><ul class="child_errors">';
                    foreach ($error as $errorvalue){
                        $html .= '<li>'.$errorvalue.'</li>';
                    }

                    $html .='</ul>';
                }else{
                    $html .= '<li>'.$error.'</li>';
                }
            }

            $html .= '</ul></div>';
            $html .= '<a title="" class="jet_errors" data-id="'.$id.'" href="#"><span class="grid-severity-critical"><span>Invalid</span></span></a>';
        }
       
        return $html.'<script>bindClick();</script>';

    }
}