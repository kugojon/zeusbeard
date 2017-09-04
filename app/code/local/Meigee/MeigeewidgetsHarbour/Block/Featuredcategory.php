<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_MeigeewidgetsHarbour_Block_Featuredcategory
extends Mage_Catalog_Block_Product_Abstract
implements Mage_Widget_Block_Interface
{
    protected $products;

    protected function _construct() {
        parent::_construct();
    }

    protected function catId()
    {
        $cat = explode("/", $this->getData('featured_category'));     
        return $cat[1];
    }
    public function catName () {
        return Mage::getModel('catalog/category')->load($this->catId());
    }

    public function getProductsAmount() {
        return $this->getData('products_amount');
    }

	public function getGrid2Description() {
        return $this->getData('grid2_description');
    }

    public function getAddToCart($config) {
		return $this->getData($config);
	}

	public function getProductPrice($config) {
		return $this->getData($config);
	}

	public function getProductName($config) {
		return $this->getData($config);
	}

	public function getQuickView($config) {
		return $this->getData($config);
	}

	public function getWishlist($config) {
		return $this->getData($config);
	}
	
	public function getCompareProducts($config) {
		return $this->getData($config);
	}

	public function getRatingStars($config) {
		return $this->getData($config);
	}
	
	public function getRatingCustLink($config) {
		return $this->getData($config);
	}
	
	public function getRatingAddReviewLink($config) {
		return $this->getData($config);
	}

	public function getProductsPerRow() {
		return $this->getData('products_per_row');
	}

	public function getColumnsRatio(){
		return $this->getData('columns_ratio');
	}

    public function getMyCollection () {
		$this->products = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect(array('name', 'price', 'small_image', 'short_description'), 'inner')
			->addAttributeToSelect('news_from_date')
			->addAttributeToSelect('news_to_date')
			->addAttributeToSelect('special_price')
			->addAttributeToSelect('status')
			->addAttributeToFilter('visibility', array(2, 3, 4))
			->addAttributeToSelect('*')
			->addCategoryFilter(Mage::getModel('catalog/category')->load($this->catId()));
		return $this->products;
	}
	
    public function getSliderOptions () {
        
        if ($this->getData('template') == 'meigee/meigeewidgetsharbour/slider.phtml' and $this->getData('autoSlide') == 1) {
            $options =
            ', autoSlide: 1, '
            . 'autoSlideTimer:'.$this->getData('autoSlideTimer').','
            .'autoSlideTransTimer:'.$this->getData('autoSlideTransTimer');
			return $options;
        }
    }
	
	public function getWidgetId () {
        return $this->getData("widget_id");
    }
}