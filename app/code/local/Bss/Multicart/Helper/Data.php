<?php
class Bss_Multicart_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getAddToCartUrl()
    {
        return $this->_getUrl('quick-order/index/addmultiplecart');
    }

    public function getCategory(){
        return Mage::getStoreConfig('multicart/multicart_settings/category_id');
    }

    public function getSortbyAttribute(){
        return Mage::getStoreConfig('multicart/multicart_settings/sort_order_attribute');
    }

    public function getSortbyType(){
        return Mage::getStoreConfig('multicart/multicart_settings/sortby_type');
    }

    public function isActive(){
        $storeview = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('multicart/multicart_settings/multicart_active',$storeview);
    }

    public function getProductOptionsHtml(Mage_Catalog_Model_Product $product)
    { 
            $blockOption = Mage::app()->getLayout()->createBlock("Mage_Catalog_Block_Product_View_Options");
            $blockOption->addOptionRenderer("default","catalog/product_view_options_type_default","bss/multicart/catalog/product/view/options/type/default.phtml");
            $blockOption->addOptionRenderer("text","catalog/product_view_options_type_text","bss/multicart/catalog/product/view/options/type/text.phtml");
            $blockOption->addOptionRenderer("file","catalog/product_view_options_type_file","bss/multicart/catalog/product/view/options/type/file.phtml");
            $blockOption->addOptionRenderer("select","catalog/product_view_options_type_select","bss/multicart/catalog/product/view/options/type/select.phtml");
            $blockOption->addOptionRenderer("date","catalog/product_view_options_type_date","bss/multicart/catalog/product/view/options/type/date.phtml") ;
     
            $blockOptionsHtml = null;
     
             if(($product->getTypeId()=="simple"||$product->getTypeId()=="virtual"||$product->getTypeId()=="configurable"))
             {  
                $blockOption->setProduct($product);
                if($product->getOptions())
                {  
                    foreach ($product->getOptions() as $o) 
                    {     
                        $blockOptionsHtml .= $blockOption->getOptionHtml($o); 
                    };    
                }  
             } 

             if($product->getTypeId()=="bundle")
             {  
                $optionCollection = $product->getTypeInstance()->getOptionsCollection();
                $selectionCollection = $product->getTypeInstance()->getSelectionsCollection($product->getTypeInstance()->getOptionsIds());
                $options = $optionCollection->appendSelections($selectionCollection);
                 foreach( $options as $option ){

                    $_selections = $option->getSelections();
                    $classtt = '';
                    $em = '';
                    if ($option->getRequired()) {
                        $classtt = 'required';
                        $em =  '<em>*</em>';
                    }
                    if (count($_selections) == 1 && $option->getRequired()){
                        $blockOptionsHtml.=  "<input type='hidden' name='bundle_option_".$product->getId()."[".$option->getId()."]' value='".$_selections[0]->getSelectionId()."'/>";
                    }else{
                        $blockOptionsHtml.= "<dt><label class='".$classtt."'>".$option->getDefaultTitle().$em."</label></dt>";
                        $blockOptionsHtml.= "<dd><select id='bundle-option-".$option->getId()."' name='bundle_option_".$product->getId()."[".$option->getId()."]'>";
                        $blockOptionsHtml.="<option value=''>".$this->__('Choose a selection...')."</option>";
                       foreach( $_selections as $selection ){
                        $blockOptionsHtml.="<option value='".$selection->getSelectionId()."'>".$selection->getName()."</option>";
                        }
                        $blockOptionsHtml.="</select></dd>";
                        $blockOptionsHtml.="<input type='text' name='bundle_option_qty_".$product->getId()."[".$option->getId()."]' value='1' style='width: 3.2em;' />";
                    }
                    
                }
                
                // $blockViewType = Mage::app()->getLayout()->createBlock("Mage_Bundle_Block_Catalog_Product_View_Type_Bundle");
             //     $blockViewType->setProduct($product); 
                //  foreach ($blockViewType->getOptions() as $option) 
          //        {     
                //  echo Mage::app()->getLayout()->createBlock('addmultipleproducts/bundle_catalog_product_view_type_option')->toHtml();
                        
          //        };    
                    
             }
             if($product->getTypeId()=="downloadable")
             {   
                $blockViewType = Mage::app()->getLayout()->createBlock("Mage_Downloadable_Block_Catalog_Product_Links");
                $blockViewType->setProduct($product);   
                $blockViewType->setTemplate("bss/multicart/catalog/product/downloadable/links.phtml");
                $blockOptionsHtml .= $blockViewType->toHtml(); 
             }

             if($product->getTypeId()=="configurable")
             {   
                $blockViewType = Mage::app()->getLayout()->createBlock("Mage_Catalog_Block_Product_View_Type_Configurable");
                $blockViewType->setProduct($product);   
                $blockViewType->setTemplate("bss/multicart/catalog/product/type/options/configurable.phtml");
                $blockOptionsHtml .= $blockViewType->toHtml(); 
             }  
             return $blockOptionsHtml; 
        }
}
	 