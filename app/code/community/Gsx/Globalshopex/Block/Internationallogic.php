<?php
class Gsx_Globalshopex_Block_Internationallogic extends Mage_Core_Block_Template {

	public $GSX_URL = "https://globalshopex.com/shoppingcart.asp";

	function getMerchantID(){
		$merchantid = Mage::getStoreConfig("checkout/globalshopex/gsxmerchatid");
		return $merchantid;
	}


	function getNameRestrictedItem(){
		$gsx_name_field_restricted = Mage::getStoreConfig("checkout/globalshopex/gsx_name_field_restricted");
		return $gsx_name_field_restricted;
	}



	//return 1 Iframe Integration
	//return  0 Cart To Cart
	
	function getTypeIntegration(){

		//$typeintegration = Mage::getStoreConfig("checkout/globalshopex/typeintegration");
		$typeintegration="1";
		return $typeintegration;
	}

	function getLocalshipping_EXP(){
		$GSX_Localshipping_EXP = Mage::getStoreConfig("checkout/globalshopex/gsx_local_shippingexp");
		if ($GSX_Localshipping_EXP=="") {
			$GSX_Localshipping_EXP="0";
		}
		$GSX_Localshipping_EXP = '<input type="hidden" name="LocalShippingEXP" value="'.$GSX_Localshipping_EXP.'" />';
		return $GSX_Localshipping_EXP;
	}

	function getLocalshipping(){
		$GSX_Localshipping = Mage::getStoreConfig('checkout/globalshopex/gsx_local_shipping'); 
		if ($GSX_Localshipping=="") {
			$GSX_Localshipping="0";
		}
		$GSX_Localshipping = '<input type="hidden" name="LocalShipping" value="'.$GSX_Localshipping.'" />';
		return $GSX_Localshipping;
	}


	function isiframeActive() {
		$active = Mage::getStoreConfig("checkout/globalshopex/gsx_iframeactive");
		return $active;
	}

	function isEnabledComponent() {
		$active = Mage::getStoreConfig("checkout/globalshopex/gsx_active");
		return $active;
	}

	
	function isLiveComponent() {
		$live = false;
		$is_live = Mage::getStoreConfig("checkout/globalshopex/gsx_is_live");
		if ($is_live=="0") {
			$live=true;
		}

		return $live;
	}

	function getNamebuttonCssClassName() {		
		$cssClassNameDefault = "";
		$gsx_image = trim(Mage::getStoreConfig("checkout/globalshopex/gsx_pathimage"));
		$cssclassbutton = Mage::getStoreConfig("checkout/globalshopex/gsx_cssclassbutton");
		if ($gsx_image == "") {
			$version = Mage::getVersion();
			if ($cssclassbutton != "") {
				$cssClassNameDefault = $cssclassbutton;

			}elseif ($version >= "1.4") {
				$cssClassNameDefault = "button btn-checkout";
			}
			else {
				$cssClassNameDefault = "form-button-alt";
			}
		}
		return $cssClassNameDefault;
	}

	function getStyleExtend() {		
		
		$gsx_style = "";
		$gsx_style = trim(Mage::getStoreConfig("checkout/globalshopex/gsx_styletag"));
		return $gsx_style;
	}

	function getPathToImageButton() {		
		
		$gsx_image = "";
		$gsx_image = trim(Mage::getStoreConfig("checkout/globalshopex/gsx_pathimage"));
		return $gsx_image;
	}

	function getCssForButtonImage() {		
		
		$style = "";
		$gsx_image = trim(Mage::getStoreConfig("checkout/globalshopex/gsx_pathimage"));
		
		if ($gsx_image != "") {
		
			$style= "style=\"background-repeat:no-repeat;";
            $style= $style ."px; outline:none; border:none; cursor:pointer;\"";
		}
		
		return $style;
	}
	

	function urlToIframePage() {
		$gsx_enablehttps = trim(Mage::getStoreConfig("checkout/globalshopex/gsx_enablehttps"));		
		$urlIFrame=Mage::getBaseUrl();
		if($gsx_enablehttps){
			$urlIFrame=str_replace('http:','https:',Mage::getBaseUrl());
		}
		$urlIFrame = $urlIFrame."GSXInternationalCheckout/GSX";
		return $urlIFrame;
	}
	
	function buttonTextInternationalCustomer() {
	
		if (trim(Mage::getStoreConfig("checkout/globalshopex/gsx_pathimage")) != "") {
			return "";
		}
		else {
			
			return $this->__('International Checkout');
		}
	}
	
	
	/*
	 * getBundleOptions - Mage_Bundle_Block_Checkout_Cart_Item_Renderer
	 *
	 */	
	protected function _getBundleOptions($item, $useCache = true)
    {
        $options = array();

        /**
         * @var Mage_Bundle_Model_Product_Type
         */
        $typeInstance = $item->getProduct()->getTypeInstance(true);

        // get bundle options
        $optionsQuoteItemOption =  $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = unserialize($optionsQuoteItemOption->getValue());
        if ($bundleOptionsIds) {
            /**
            * @var Mage_Bundle_Model_Mysql4_Option_Collection
            */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $item->getProduct());

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $selectionsCollection = $typeInstance->getSelectionsByIds(
                unserialize($selectionsQuoteItemOption->getValue()),
                $item->getProduct()
            );

            $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
            foreach ($bundleOptions as $bundleOption) {
                if ($bundleOption->getSelections()) {
                    $option = array('label' => $bundleOption->getTitle(), "value" => array());
                    $bundleSelections = $bundleOption->getSelections();

                    foreach ($bundleSelections as $bundleSelection) {
                        $option['value'][] = $this->_getSelectionQty($item, $bundleSelection->getSelectionId()).' x '. $this->htmlEscape($bundleSelection->getName()). ' ' .Mage::helper('core')->currency($this->_getSelectionFinalPrice($item, $bundleSelection));
                    }

                    $options[] = $option;
                }
            }
        }
        return $options;
    }
	
	
	function _getSelectionFinalPrice($item, $selectionProduct)
    {
        $bundleProduct = $item->getProduct();
        return $bundleProduct->getPriceModel()->getSelectionFinalPrice(
            $bundleProduct, $selectionProduct,
            $item->getQty(),
            $this->_getSelectionQty($item, $selectionProduct->getSelectionId())
        );
    }

    /**
     * Get selection quantity
     *    
     */
    function _getSelectionQty($item, $selectionId)
    {
        if ($selectionQty = $item->getProduct()->getCustomOption('selection_qty_' . $selectionId)) {
            return $selectionQty->getValue();
        }
        return 0;
    }
	
	
	
	/**
	 * get product attributes
	 *	
	 */
	
	function getProductAttributes($item)
    {
        $attributes = $item->getProduct()->getTypeInstance(true)
            ->getSelectedAttributesInfo($item->getProduct());
        return $attributes;
    }
	
	
	
	 function getLinks($item)
    {
        $itemLinks = array();
        if ($linkIds = $item->getOptionByCode('downloadable_link_ids')) {
            $productLinks = $item->getProduct()->getTypeInstance(true)
                ->getLinks($item->getProduct());
            foreach (explode(',', $linkIds->getValue()) as $linkId) {
                if (isset($productLinks[$linkId])) {
                    $itemLinks[] = $productLinks[$linkId];
                }
            }
        }
        return $itemLinks;
    }
	
	
	function getLinksTitle($item)
    {
        if ($item->getProduct()->getLinksTitle()) {
            return $item->getProduct()->getLinksTitle();
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }
	
	
	function getChildProduct($item)
    {
        if ($option = $item->getOptionByCode('simple_product')) {
            return $option->getProduct();
        }
        return $item->getProduct();
    }
	
	function getGroupedProduct($item)
    {
        $option = $item->getOptionByCode('product_type');
        if ($option) {
            return $option->getProduct();
        }
        return $item->getProduct();
    }
	
	
	
	function getProductThumbnail($item)
    {
        
		// begin configurable
		 if ($item->getProduct()->isConfigurable()) {
			$product = $this->getChildProduct($item);
			if (!$product || !$product->getData('thumbnail')
				|| ($product->getData('thumbnail') == 'no_selection')
				|| (Mage::getStoreConfig(Mage_Checkout_Block_Cart_Item_Renderer_Configurable::CONFIGURABLE_PRODUCT_IMAGE) == Mage_Checkout_Block_Cart_Item_Renderer_Configurable::USE_PARENT_IMAGE)) {
				$product = $item->getProduct();
			}
			return Mage::helper('catalog/image')->init($product, 'thumbnail');
		}
		// end configurable
		
		// begin grouped
		if ($item->getProduct()->isGrouped()) {
			$product = $item->getProduct();
			if (!$product->getData('thumbnail')
				||($product->getData('thumbnail') == 'no_selection')
				|| (Mage::getStoreConfig(Mage_Checkout_Block_Cart_Item_Renderer_Grouped::GROUPED_PRODUCT_IMAGE) == Mage_Checkout_Block_Cart_Item_Renderer_Grouped::USE_PARENT_IMAGE)) {
				$product = $this->getGroupedProduct($item);
			}
			return Mage::helper('catalog/image')->init($product, 'thumbnail');
		}
		// end grouped
		
		
		return Mage::helper('catalog/image')->init($item->getProduct(), 'thumbnail');
		
    }
	
	
	
	
	/**
	 * Returns product options and additional attrubutes.  
	 *	
	 */
	
	function getProductOptions($item)
    {
      $options = array();
      if ($optionIds = $item->getOptionByCode('option_ids')) {
          $options = array();
          foreach (explode(',', $optionIds->getValue()) as $optionId) {
              if ($option = $item->getProduct()->getOptionById($optionId)) {

                  $quoteItemOption = $item->getOptionByCode('option_' . $option->getId());

                  $group = $option->groupFactory($option->getType())
                      ->setOption($option)
                      ->setQuoteItemOption($quoteItemOption);

                  $options[] = array(
                      'label' => $option->getTitle(),
                      'value' => $group->getFormattedOptionValue($quoteItemOption->getValue()),
                      'print_value' => $group->getPrintableOptionValue($quoteItemOption->getValue()),
                      'option_id' => $option->getId(),
                      'option_type' => $option->getType(),
                      'custom_view' => $group->isCustomizedView()
                  );
              }
          }
      }
      if ($addOptions = $item->getOptionByCode('additional_options')) {
          $options = array_merge($options, unserialize($addOptions->getValue()));
      }
      
	  
	  if ($item->getProduct()->isConfigurable()) {
	  	$options = array_merge($this->getProductAttributes($item), $options); // configurable products
       }
	   
	   
	   if ($item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
	   	$options = array_merge($this->_getBundleOptions($item), $options); // bundle products
	   }
	  
	  
	  return $options;
    }
	

	/**
	 * Returns formatted string of item description and attributes.  
	 *	
	 */	

	function buildItemDescription($item) {
	
		$crlf = "\n";		
		$valueSeperator = " - ";
		$output = "";
		$output .= $this->htmlEscape($item->getName()).$crlf;
		
		$options = $this->getProductOptions($item);
		if (count($options)) {
			for ($c=0; $c<count($options); $c++) {
				
				if (is_array($options[$c]["value"])) {
					$output .= " [ ". $options[$c]["label"].": ".strip_tags(implode($valueSeperator,$options[$c]["value"]))." ] ";				
				}
				else {
					$output .= " [ ".$options[$c]["label"].": ".strip_tags($options[$c]["value"])." ] ";
				}
				
				$output .= $crlf;
			}
		}
		
		// addition of links for downloadable products
		//
		if ($links = $this->getLinks($item)) {
			$output .= " [ " . strip_tags($this->getLinksTitle($item));
			foreach ($links as $link) {
				$output .= " ( " . strip_tags($link->getTitle()) . " ) ";
			}
			$output .= " ] ";
			$output .= $crlf;
		}
		return $output;
	}
	
	function getProductUrl($item){

        if ($item->getRedirectUrl()) {
            return $item->getRedirectUrl();
        }
        $product = $item->getProduct();
        $option  = $item->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }

        return $product->getUrlModel()->getUrl($product);
		
}
}
