<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Helper_Data extends Mage_Core_Helper_Abstract
{
 public function getThemeOptionsHarbour ($themeOption) {
 	switch ($themeOption) {
		case 'meigee_harbour_general':
		    return Mage::getStoreConfig('meigee_harbour_general');
		break;
		case 'meigee_harbour_appearance':
		    return Mage::getStoreConfig('meigee_harbour_appearance');
		break;
		case 'meigee_harbour_design':
		    return Mage::getStoreConfig('meigee_harbour_design');
		break;
		case 'meigee_harbour_productpage':
		    return Mage::getStoreConfig('meigee_harbour_productpage');
		break;
		case 'meigee_harbour_sidebar':
		    return Mage::getStoreConfig('meigee_harbour_sidebar');
		break;
		case 'meigee_harbour_bgslider':
		    return Mage::getStoreConfig('meigee_harbour_bgslider');
		break;
		case 'mediaurl':
		    return Mage::getBaseUrl('media') . 'images/';
		break;
 	}
 }

public function getProductLabels ($_product, $type) {
 	switch ($type) {
		case 'new':
		 	if (Mage::getStoreConfig('meigee_harbour_general/productlabels/labelnew')):
				$from = $_product->getNewsFromDate();
				$to = new Zend_Date($_product->getNewsToDate());
				$now = new Zend_Date(Mage::getModel('core/date')->timestamp(time()));
				if (isset($from) && $to->isLater($now)): 
					return '<span class="label-new"><strong>'.$this->__('New').'</strong></span>';
				else:
					return false;
				endif;
			else:
				return false;
			endif;
		break;
		case 'sale':
		    if(Mage::getStoreConfig('meigee_harbour_general/productlabels/labelonsale')):
				$_finalPrice = MAGE::helper('tax')->getPrice($_product, $_product->getFinalPrice());
				$_regularPrice = MAGE::helper('tax')->getPrice($_product, $_product->getPrice());
				if ($_regularPrice != $_finalPrice):
					if (Mage::getStoreConfig('meigee_harbour_general/productlabels/salepercentage')):
						$getpercentage = number_format($_finalPrice / $_regularPrice * 100, 2);
						$finalpercentage = 100 - $getpercentage;
						return '<div class="label-sale percentage">'.number_format($finalpercentage, 0).'% <span>'.$this->__('off').'</span></div>';
					else:
						return '<div class="label-sale"><strong>'.$this->__('Sale').'</strong></div>';
					endif;
				else:
					return false;
				endif;
			else:
				return false;
			endif;
		break;
	}
 	
 }
public function getProductOnlyXleft ($_product){
	if(Mage::getStoreConfig('meigee_harbour_general/productlabels/labelonlyxleft')){
		$stockThreshold = Mage::getStoreConfig('cataloginventory/options/stock_threshold_qty');
		$productQty = round(Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty());
		if($productQty != 0 and $productQty < $stockThreshold){
			return '<div class="availability-only">< '.($productQty+1).' <p>'.$this->__('Left').'</p></div>';
		}else{
			return false;
		}
	}else{
		return false;
	}
}

 public function isNew($product)
	{
		return $this->_nowIsBetween($product->getData('news_from_date'), $product->getData('news_to_date'));
	}
public function isOnSale($product)
{
	$specialPrice = number_format($product->getFinalPrice(), 2);
	$regularPrice = number_format($product->getPrice(), 2);
	
	if ($specialPrice != $regularPrice)
		return true;
	else
		return false;
}

 public function prevnext ($product) {
 	if ($product->getHarbourPrprevnext() < 2 ):
		$prevnext = $this->getThemeOptionsHarbour('meigee_harbour_productpage');
		if ($product->getHarbourPrprevnext() == 1 or $prevnext['general']['prevnext'] == 'prevnext'):
		
		 	$_helper = Mage::helper('catalog/output');
			$_product = $product->getProduct();
			$prev_url = $next_url = $url = $product->getProductUrl();
			 
			if (Mage::helper('catalog/data')->getCategory()) {
				$category = Mage::helper('catalog/data')->getCategory();
			} else {
				$_ccats = Mage::helper('catalog/data')->getProduct()->getCategoryIds();
				if(isset($_ccats[0])){
					$category = Mage::getModel('catalog/category')->load($_ccats[0]);
				}else{
					return false;
				}
			}
			 
			$children = $category->getProductCollection();
			$_count = is_array($children) ? count($children) : $children->count();
			if ($_count) {
			foreach ($children as $product) {
			$plist[] = $product->getId();
			}
			 
			/**
			* Determine the previous/next link and link to current category
			*/
			$current_pid  = Mage::helper('catalog/data')->getProduct()->getId();
			$curpos   = array_search($current_pid, $plist);
			// get link for prev product
			$previd   = isset($plist[$curpos+1])? $plist[$curpos+1] : $current_pid;
			$product  = Mage::getModel('catalog/product')->load($previd);
			$prevpos  = $curpos;
			while (!$product->isVisibleInCatalog()) {
			$prevpos += 1;
			$nextid   = isset($plist[$prevpos])? $plist[$prevpos] : $current_pid;
			$product  = Mage::getModel('catalog/product')->load($nextid);
			}
			$prev_url = $product->getProductUrl();
			// get link for next product
			$nextid   = isset($plist[$curpos-1])? $plist[$curpos-1] : $current_pid;
			$product  = Mage::getModel('catalog/product')->load($nextid);
			$nextpos  = $curpos;
			while (!$product->isVisibleInCatalog()) {
			$nextpos -= 1;
			$nextid   = isset($plist[$nextpos])? $plist[$nextpos] : $current_pid;
			$product  = Mage::getModel('catalog/product')->load($nextid);
			}
			$next_url = $product->getProductUrl();
			}
			
			$html ='';
			if ($url <> $next_url): 
				$html = '<a class="product-prev" title="' . $this->__('Previous Product') . '" href="' . $next_url . '"><i class="fa fa-chevron-left"></i></a>';
			endif;
			if ($url <> $prev_url):
				$html .= '<a class="product-next" title="' . $this->__('Next Product') . '" href="' . $prev_url . '"><i class="fa fa-chevron-right"></i></a>';
			endif;
			return $html;
		else: 
			return false;
		endif;
	else: 
		return false;
	endif;
 }

	public function isActive($attribute, $value){

	    $col = Mage::getModel('cms/block')->getCollection();
	    $col->addFieldToFilter($attribute, $value);
	    $item = $col->getFirstItem();
	    $id = $item->getData('is_active');

	    if($id == 1){
	        return true;
	    }else{
	        return false;
	    }

	}

	public function switchCart() {
		$cartType = $this->getThemeOptionsHarbour('meigee_harbour_appearance');
		$cart_qty = (int) Mage::getModel('checkout/cart')->getQuote()->getItemsQty();
		if($cart_qty){
			if($cartType['layout']['cartpage'] == 'cart_new_default') {
				return 'checkout/cart_2.phtml';
			} else {
				return 'checkout/cart.phtml';
			}
		} else {
			return 'checkout/cart/noItems.phtml';
		}
	}
	
	public function switchHeader() {
		$appearance = $this->getThemeOptionsHarbour('meigee_harbour_appearance');
		if($appearance['header']['headertype'] == 1) {
			return 'page/html/header_2.phtml';
		} elseif ($appearance['header']['headertype'] == 2) {
			return 'page/html/header_3.phtml';
		} elseif ($appearance['header']['headertype'] == 3) {
			return 'page/html/header_4.phtml';
		} elseif ($appearance['header']['headertype'] == 4) {
			return 'page/html/header_5.phtml';
		}else {
			return 'page/html/header.phtml';
		}
	}
	
	public function setCookie() {
		$popup = $this->getThemeOptionsHarbour('meigee_harbour_general');
		if ($popup['popup']['popup_status'] == 1){
			return 'js/jquery.cookie.js';
		}
	}
	
	public function switchGrid() {
		$switchGrid = $this->getThemeOptionsHarbour('meigee_harbour_appearance');
		if ((int)$switchGrid['layout']['responsiveness'] !== 1):
			return 'css/grid_' . $switchGrid['layout']['responsiveness'] . '.css';
		endif;
		return 'css/grid_responsive.css';
	}
	
	public function hitchedHeader() {
		$appearance = $this->getThemeOptionsHarbour('meigee_harbour_appearance');
		if($appearance['header']['transparent_header_home'] == 1 && $appearance['header']['transparent_header_home_bg'] == 1){
			return 'hitched-header';
		}
	}
	
	public function geIsotope(){
		$rtl = $this->getThemeOptionsHarbour('meigee_harbour_appearance');
		if ($rtl['layout']['rtl'] == 1){
			return 'js/jquery.isotope.min_rtl.js';
		}else{
			return 'js/jquery.isotope.min.js';
		}
	}
	
	public function geIosslider(){
		$rtl = $this->getThemeOptionsHarbour('meigee_harbour_appearance');
		if ($rtl['layout']['rtl'] == 1){
			return 'js/jquery.iosslider.min_rtl.js';
		}else{
			return 'js/jquery.iosslider.min.js';
		}
	}
	
	public function isShopBy() {
		$shopBy = $this->getThemeOptionsHarbour('meigee_harbour_sidebar');
		if ($shopBy['block_shop_by']['status'] == 0){
			return 'catalog.leftnav';
		}
	}
	
	public function isReorder() {
		$reorder = $this->getThemeOptionsHarbour('meigee_harbour_sidebar');
		if ($reorder['block_orders']['status'] == 0 ){
			return 'reorder';
		}else{
			return 'reorder_ok';
		}
	}
	
	public function fancySwitcher(){
		$fancy = $this->getThemeOptionsHarbour('meigee_harbour_general');
		if ($fancy['fancybox']['fancybox_status'] == 1 || $fancy['popup']['popup_status'] == 1):
			return 'css/fancybox.css';
		endif;
	}

	public function getPaternClass (){
		$patern = $this->getThemeOptionsHarbour('meigee_harbour_design');
		return $patern['appearance']['patern'];
	}
	
	public function getSidebarPos (){
		$sidePos = $this->getThemeOptionsHarbour('meigee_harbour_appearance');
		return $sidePos['productlisting']['sidebar'];
	}

	public function fancySwitcherJs(){
		$fancy = $this->getThemeOptionsHarbour('meigee_harbour_general');
		if ($fancy['fancybox']['fancybox_status'] == 1 || $fancy['popup']['popup_status'] == 1):
			return 'js/jquery.fancybox.pack.js';
		endif;
	}
	
	public function RgbaColors($color, $transparent, $transparentValue) {
		if($transparent == 0) {
			$result = '#'.$color;
		} else {
			$rgbcolor = '';
			if(strlen($color) == 3) {
				$rgbcolor .= hexdec(substr($color, 0, 1) . $r) . ',';
				$rgbcolor .= hexdec(substr($color, 1, 1) . $g) . ',';
				$rgbcolor .= hexdec(substr($color, 2, 1) . $b);
			}
			else if(strlen($color) == 6) {
				$rgbcolor .= hexdec(substr($color, 0, 2)) . ',';
				$rgbcolor .= hexdec(substr($color, 2, 2)) . ',';
				$rgbcolor .= hexdec(substr($color, 4, 2));
			}
			$transparentValue = $transparentValue/100;
			$result = 'rgba('.$rgbcolor.', '.$transparentValue.')';
		}
		return $result;
	}
	
	public function getIcon ($type) {
		return '<i class="fa '. Mage::getStoreConfig('meigee_harbour_design/icons/'. $type) .'"></i>';
    }
	
	public function getFbSidebar () {
		$fboptions = $this->getThemeOptionsHarbour('meigee_harbour_sidebar');
		$fbcontent .= 'data-width="300"';
        $fbcontent .= 'data-height="' . $fboptions['block_facebook']['height'] . '"';
        $fbcontent .= 'data-href="' . $fboptions['block_facebook']['href'] . '"';
        $fbcontent .= 'data-colorscheme="' . $fboptions['block_facebook']['colorscheme'] . '"';
        $fbcontent .= 'data-show-faces="' . $fboptions['block_facebook']['faces'] . '"';
        $fbcontent .= 'data-header="' . $fboptions['block_facebook']['header'] . '"';
        $fbcontent .= 'data-stream="' . $fboptions['block_facebook']['stream'] . '"';
        $fbcontent .= 'data-show-border="' . $fboptions['block_facebook']['border'] . '"';
        return $fbcontent;
    }
	
}
?>