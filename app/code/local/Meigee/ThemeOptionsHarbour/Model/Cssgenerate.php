<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Cssgenerate extends Mage_Core_Model_Abstract
{
    private $baseColors;
	private $catlabelsColors;
    private $appearance;
    private $mediaPath;
    private $dirPath;
    private $filePath;
	private $buttonsColors;
	private $productsColors;
	private $contentColors;
	private $socialLinksColors;
	private $headerColors;
	private $stickyHeaderColors;
	private $footerColors;
	private $menuColors;
	private $revSliderButtonsColors;
	private $parallaxBannersColors;
	private $pageNotFoundColors;

    private function setParams () {
        $this->baseColors = Mage::getStoreConfig('meigee_harbour_design/base');
		$this->catlabelsColors = Mage::getStoreConfig('meigee_harbour_design/catlabels');
        $this->appearance = Mage::getStoreConfig('meigee_harbour_design/appearance');
		$this->headerColors = Mage::getStoreConfig('meigee_harbour_design/header');
		$this->stickyHeaderColors = Mage::getStoreConfig('meigee_harbour_design/sticky_header');
		$this->menuColors = Mage::getStoreConfig('meigee_harbour_design/menu');
		$this->revSliderButtonsColors = Mage::getStoreConfig('meigee_harbour_design/rev_slider_but');
		$this->parallaxBannersColors = Mage::getStoreConfig('meigee_harbour_design/parallax_banners');
		$this->pageNotFoundColors = Mage::getStoreConfig('meigee_harbour_design/page_not_found');
		$this->contentColors = Mage::getStoreConfig('meigee_harbour_design/content');
		$this->buttonsColors = Mage::getStoreConfig('meigee_harbour_design/buttons');
		$this->productsColors = Mage::getStoreConfig('meigee_harbour_design/products');
		$this->socialLinksColors = Mage::getStoreConfig('meigee_harbour_design/social_links');
		$this->footerColors = Mage::getStoreConfig('meigee_harbour_design/footer');	
    }

    private function setLocation () {
        $this->mediaPath = Mage::getBaseDir('media') . 'images/';
        $this->dirPath = Mage::getBaseDir('skin') . '/frontend/harbour/default/css/';
        $this->filePath = $this->dirPath . 'skin.css';
    }

    public function saveCss()
    {

        $this->setParams();

$css = "/**
*
* This file is generated automaticaly. Please do no edit it directly cause all changes will be lost.
*
*/
";

if ($this->appearance['font_main'] == 1)
{
    $css .= '/*====== Font Replacement - Main Text =======*/ ';
    if ($this->appearance['main_default_sizes'] == 0)
        {
$css .= '
body{
    font-family: '. $this->appearance['gfontmain'] .', sans-serif; 
    font-size:'. $this->appearance['main_fontsize'] .'px !important;
    line-height:' . $this->appearance['main_lineheight'] .'px !important;
    font-weight:' .$this->appearance['main_fontweight'] .' !important;
}

';
	}else{
		$css .= '
		body{
			font-family: '. $this->appearance['gfontmain'] .', sans-serif;
		}
		
		';
	}
};


if ($this->appearance['font'] == 1)
{
    $css .= '/*====== Font Replacement - Titles =======*/ ';
    if ($this->appearance['default_sizes'] == 0)
        {
$css .= '
.widget-latest li h3 a,
.widget .widget-title h1,
.widget .widget-title h2,
.widget-title h2,
.page-title h1,
.page-title h2,
.page-title h3,
.page-title h4,
.page-title h5,
.page-title h6,
.nav-container a.level-top > span,
header#header .links li a.top-link-login,
header#header.header-2 .header-text-banners .item .text h3,
header#header.header-5 .header-phone,
.text-banner h2,
.text-banner h3,
.text-banner h4,
.product-tabs li,
.sorter label,
aside.sidebar .block-title strong span,
aside.sidebar .block.block-layered-nav dl dt.filter-label,
.block-layered-nav dl#narrow-by-list2 dt h2,
aside.sidebar .block .block-subtitle,
.product-name,
.product-name a,
.price,
button.button span span,
aside.sidebar .actions a,
.nav-wide ul.level0 li.level1 span.subtitle,
.nav-wide .top-content a,
.nav-wide .bottom-content span strong,
header.header .top-cart .block-title .title-cart,
header.header .top-cart .block-content .subtotal .label,
header.header .top-cart .block-content .subtotal .price,
header.header .top-cart .block-content .actions a,
.data-table .product-name a,
.cart header h2,
#cart-accordion h3.accordion-title span,
.fieldset .legend,
.product-options dt label,
.dashboard .welcome-msg .hello,
.dashboard .box-title h2,
.dashboard .box-title h3,
.dashboard .box-head h3,
.dashboard .box-head h2,
.opc h3,
.opc .step-title h2,
.cart .shipping .form-list label,
.widget-latest li .info-box,
header#header .language-currency-dropdown label,
.header-wrapper .header-menu .right-menu h3,
.header-wrapper .header-menu .right-menu li a,
.catalog-product-view .box-reviews h2,
.product-view .product-shop .product-name h1,
.product-view .product-shop .add-to-links-box a ,
.more-views h2,
.meigee-tabs a,
.block-related .block-title span,
.catalog-product-view .rating-title h2,
.text-blocks h3,
.text-blocks a,
.second-text-block .text,
.second-text-block .text h3,
.third-text-block,
.third-text-block h3,
.parallax-banners-wrapper .text-banner .banner-content h2,
.parallax-banners-wrapper .text-banner .banner-content h3,
.parallax-banners-wrapper .text-banner .banner-content h4,
.nav-container .nav-wide .bottom-content,
header.header .top-cart .cart-price-qt,
.label-new,
.label-sale,
.menu-button,
#popup-block .block-subscribe strong span,
.md-modal-header h4,
.availability.listing,
.product-options dt label,
.category-button a,
aside.sidebar .block.block-wishlist .link-cart,
.newsletter-line .block-subscribe h3,
a.aw-blog-read-more,
.cms-no-route .page-not-found h2,
.cms-no-route .page-not-found h3  {
    font-family: '. $this->appearance['gfont'] .', sans-serif; 
    font-size:'. $this->appearance['fontsize'] .'px !important;
    line-height:' . $this->appearance['lineheight'] .'px !important;
    font-weight:' .$this->appearance['fontweight'] .' !important;
}';
        } else {
        $css .= '
.widget-latest li h3 a,
.widget .widget-title h1,
.widget .widget-title h2,
.widget-title h2,
.page-title h1,
.page-title h2,
.page-title h3,
.page-title h4,
.page-title h5,
.page-title h6,
.nav-container a.level-top > span,
header#header .links li a.top-link-login,
header#header.header-2 .header-text-banners .item .text h3,
header#header.header-5 .header-phone,
.text-banner h2,
.text-banner h3,
.text-banner h4,
.product-tabs li,
.sorter label,
aside.sidebar .block-title strong span,
aside.sidebar .block.block-layered-nav dl dt.filter-label,
.block-layered-nav dl#narrow-by-list2 dt h2,
aside.sidebar .block .block-subtitle,
.product-name,
.product-name a,
.price,
button.button span span,
aside.sidebar .actions a,
.nav-wide ul.level0 li.level1 span.subtitle,
.nav-wide .top-content a,
.nav-wide .bottom-content span strong,
header.header .top-cart .block-title .title-cart,
header.header .top-cart .block-content .subtotal .label,
header.header .top-cart .block-content .subtotal .price,
header.header .top-cart .block-content .actions a,
.data-table .product-name a,
.cart header h2,
#cart-accordion h3.accordion-title span,
.fieldset .legend,
.product-options dt label,
.dashboard .welcome-msg .hello,
.dashboard .box-title h2,
.dashboard .box-title h3,
.dashboard .box-head h3,
.dashboard .box-head h2,
.opc h3,
.opc .step-title h2,
.cart .shipping .form-list label,
.widget-latest li .info-box,
header#header .language-currency-dropdown label,
.header-wrapper .header-menu .right-menu h3,
.header-wrapper .header-menu .right-menu li a,
.catalog-product-view .box-reviews h2,
.product-view .product-shop .product-name h1,
.product-view .product-shop .add-to-links-box a ,
.more-views h2,
.meigee-tabs a,
.block-related .block-title span,
.catalog-product-view .rating-title h2,
.text-blocks h3,
.text-blocks a,
.second-text-block .text,
.second-text-block .text h3,
.third-text-block,
.third-text-block h3,
.parallax-banners-wrapper .text-banner .banner-content h2,
.parallax-banners-wrapper .text-banner .banner-content h3,
.parallax-banners-wrapper .text-banner .banner-content h4,
.nav-container .nav-wide .bottom-content,
header.header .top-cart .cart-price-qt,
.label-new,
.label-sale,
.menu-button,
#popup-block .block-subscribe strong span,
.md-modal-header h4,
.availability.listing,
.product-options dt label,
.category-button a,
aside.sidebar .block.block-wishlist .link-cart,
.newsletter-line .block-subscribe h3,
a.aw-blog-read-more,
.cms-no-route .page-not-found h2,
.cms-no-route .page-not-found h3  {font-family: ' . $this->appearance['gfont'] .', sans-serif;}';
    }
}

if ($this->appearance['custompatern']) {
$css .= '

/*====== Custom Patern =======*/
body { background: url("' . MAGE::helper('ThemeOptionsHarbour')->getThemeOptionsHarbour('mediaurl') . $this->appearance['custompatern'] . '") center top repeat !important;}';
}
$css .= '

/*====== Site Bg =======*/
body,
body.boxed-layout,
#footer .footer-top,
#footer .footer-bottom,
body.boxed-layout #footer .footer-top .container_12,
body.boxed-layout #footer .footer-bottom  .container_12 {background-color:#' . $this->baseColors['sitebg'] . ';}

/*====== Skin Color #1 =======*/
button.button:hover > span,
aside.sidebar .actions a:hover,
.related-products-button a:hover,
.parallax-banners-wrapper .text-banner .banner-content .parallax-btn > span,
.parallax-banners-wrapper .text-banner .banner-content .parallax-btn-2:hover > span,
.cart .btn-proceed-checkout:hover > span,
#footer .block-tags li a:hover {border-color: #' . $this->baseColors['maincolor'] . ';}

.category-products .toolbar-bottom:before,
.sorter .view-mode a:hover,
.sorter .view-mode strong,
button.button:hover > span,
aside.sidebar .actions a:hover,
.pages li.current,
.catalog-product-view .box-reviews .data-table thead,
.product-view .product-prev:hover,
.product-view .product-next:hover,
.more-views .prev i:hover,
.more-views .next i:hover,
.meigee-tabs .active,
.related-wrapper .block-related .prev i:hover,
.related-wrapper .block-related .next i:hover,
.related-products-button a:hover,
.text-blocks ul li.item i,
.parallax-banners-wrapper .text-banner .banner-content .parallax-btn span span,
.parallax-banners-wrapper .text-banner .banner-content .parallax-btn-2:hover span span,
.catalog-product-view .box-reviews .full-review,
.menu-button.mobile,
.related-wrapper-bottom .block-related .next i:hover,
.related-wrapper-bottom .block-related .prev i:hover,
.slider-container .prev i:hover,
.slider-container .next i:hover,
div.quantity-decrease i:hover,
div.quantity-increase i:hover,
.cart .btn-proceed-checkout:hover span span,
.products-list li.item .fancybox,
.products-grid li.item .fancybox,
#footer .block-tags li a:hover {background-color: #' . $this->baseColors['maincolor'] . ';}

a,
header#header.header-5 .text-banner h2 strong,
.price,
.catalog-product-view .box-reviews .form-add h3 span,
.third-text-block h3 span,
.nav-wide .bottom-content .sale,
.products-grid li.item .product-buttons li i:hover,
.products-list li.item .add-to-links li i:hover,
.block-compare li.item .btn-remove i:hover,
.dashboard .box-reviews .product-name a:hover,
aside.sidebar .block.block-wishlist li.item .product-details .btn-remove i:hover,
aside.sidebar .block-subscribe .actions .button:hover span i,
#footer .footer-products-list .list-small-buttons .add-to-links li i:hover,
#footer .footer-products-list .product-shop .price-box .price,
#footer .footer-products-list .product-shop .price-box .special-price .price,
.sorter a.asc:hover,
.sorter a.desc:hover {color: #' . $this->baseColors['maincolor'] . ';}

/*====== Skin Color #2 =======*/
.cart.cart-2 aside section.totals header,
.cart .btn-proceed-checkout span span,
.products-list li.item .fancybox:hover,
.products-grid li.item .fancybox:hover {background-color: #' . $this->baseColors['secondcolor'] . ';}

.cart .btn-proceed-checkout > span {border-color: #' . $this->baseColors['secondcolor'] . ';}

a:hover,
.dashboard .box-title a:hover,
.dashboard .box-head a:hover,
.dashboard a:hover,
#categories-accordion .btn-cat .fa-minus-square-o,
.block-vertical-nav li.active > a,
.block-vertical-nav a:hover,
aside.sidebar .block.block-layered-nav ol li a:hover,
aside.sidebar .block-tags li a:hover,
nav.breadcrumbs li span:hover,
.data-table .c_actions a i:hover,
.product-view .add-to-links-box a:hover span,
.crosssell .product-details .add-to-links i:hover,
.crosssell .product-details button.button:hover span,
.cart .totals .checkout-types li a:hover,
aside.sidebar .product-name a:hover,
aside.sidebar .block.block-wishlist li.item .product-details .product-name a:hover,
.block-account li a:hover,
.block-account li strong,
.my-wishlist .data-table .table-buttons a i:hover,
#footer .footer-products-list .product-shop .product-name a:hover {color: #' . $this->baseColors['secondcolor'] . ';}

/*====== Category Labels =======*/
.nav-wide li.level-top .category-label.label_one { 
    background-color: #' . $this->catlabelsColors['label_one'] . ';
    color: #' . $this->catlabelsColors['label_one_color'] . ';
}
.nav-wide li.level-top.over .category-label.label_one { 
    background-color: #' . $this->catlabelsColors['label_one_h'] . ';
    color: #' . $this->catlabelsColors['label_one_color_h'] . ';
}
.nav-wide li.level-top .category-label.label_two { 
    background-color: #' . $this->catlabelsColors['label_two'] . ';
    color: #' . $this->catlabelsColors['label_two_color'] . ';
}
.nav-wide li.level-top.over .category-label.label_two { 
    background-color: #' . $this->catlabelsColors['label_two_h'] . ';
    color: #' . $this->catlabelsColors['label_two_color_h'] . ';
}
.nav-wide li.level-top .category-label.label_three { 
    background-color: #' . $this->catlabelsColors['label_three'] . ';
    color: #' . $this->catlabelsColors['label_three_color'] . ';
}
.nav-wide li.level-top.over .category-label.label_three { 
    background-color: #' . $this->catlabelsColors['label_three_h'] . ';
    color: #' . $this->catlabelsColors['label_three_color_h'] . ';
}
';
if ($this->baseColors['base_override'] == 1) {
$css .= '
/*====== Header =======*/
header.header,
body.boxed-layout header.header .container_12 {
	background-color: #' . $this->headerColors['header_bg'] . ';
	color: #' . $this->headerColors['header_color'] . ';
}
header.header .welcome-msg {color: #' . $this->headerColors['header_color'] . ';}
header.header a {color: #' . $this->headerColors['header_link_color'] . ';} 
header.header a:hover {color: #' . $this->headerColors['header_link_color_h'] . ';}
header#header .header-top,
body.boxed-layout header#header .header-top .container_12,
header#header .header-top .language-currency-block {background-color: #' . $this->headerColors['header_top_bg'] . ';}
header.header .header-right .bottom-block,
header#header.header-2 .menu-line .grid_12,
header#header.header-3 .menu-line .grid_12,
header#header.header-4 .menu-line .grid_12,
header#header.header-5 .menu-line .grid_12 {
	border-color: #' . $this->headerColors['header_border'] . ';
	border-width: ' . $this->headerColors['header_border_width'] . 'px;
}

/* Header language and currency switchers */
header#header .language-currency-block {
	background-color: #' . $this->headerColors['header_lang_currency_bg'] . ';
	color: #' . $this->headerColors['header_lang_currency_text'] . ';
}
header#header .language-currency-block:hover,
header#header .language-currency-block.open {
	background-color: #' . $this->headerColors['header_lang_currency_bg_h'] . ';
	color: #' . $this->headerColors['header_lang_currency_text_h'] . ';
}
header#header .language-currency-dropdown {background-color: #' . $this->headerColors['header_lang_currency_dropdown_bg'] . ';}
header#header .language-currency-dropdown label {color: #' . $this->headerColors['header_lang_currency_label'] . ';}
header#header .language-currency-dropdown .sbSelector {
	color: #' . $this->headerColors['header_lang_currency_swither_color'] . ';
	background-color: #' . $this->headerColors['header_lang_currency_swither_bg'] . ';
	border-color: #' . $this->headerColors['header_lang_currency_swither_border'] . ';
}
header#header .language-currency-dropdown .sbOptions {background-color: #' . $this->headerColors['header_lang_currency_swither_dropdown'] . ';}
header#header .language-currency-dropdown .sbOptions li a,
header#header .language-currency-dropdown > div > a {color: #' . $this->headerColors['header_lang_currency_swither_dropdown_link'] . ';}
header#header .language-currency-dropdown .sbOptions li:hover {background-color: #' . $this->headerColors['header_lang_currency_swither_dropdown_link_bg_h'] . ';}
header#header .language-currency-dropdown .sbOptions li:hover a, 
header#header .language-currency-dropdown > div > a:hover {color: #' . $this->headerColors['header_lang_currency_swither_dropdown_link_h'] . ';}

/**** Header Search ****/
body header.header .search_mini_form input {
	background-color: #' . $this->headerColors['header_search_bg'] . ';
	border-color: #' . $this->headerColors['header_search_border'] . ';
	color: #' . $this->headerColors['header_search_color'] . ';
	border-width: ' . $this->headerColors['header_search_border_width'] . 'px;
}
body header.header .search_mini_form button > span,
body header#header .search_mini_form.floating button > span {
	border-color: #' . $this->headerColors['header_search_button_border'] . ';
	background-color: #' . $this->headerColors['header_search_button_bg'] . ';
	color: #' . $this->headerColors['header_search_button_color'] . ';
	border-width: ' . $this->headerColors['header_search_button_border_width'] . 'px;
}
body header.header .search_mini_form button:hover > span,
body header#header .search_mini_form.floating button:hover > span {
	background-color: #' . $this->headerColors['header_search_button_bg_h'] . ';
	border-color: #' . $this->headerColors['header_search_button_border_h'] . ';
	color: #' . $this->headerColors['header_search_button_color_h'] . ';
}
body header.header .search_mini_form .focus input {
	background-color: #' . $this->headerColors['header_search_active_bg'] . ';
	border-color: #' . $this->headerColors['header_search_active_border'] . ';
	color: #' . $this->headerColors['header_search_active_color'] . ';
}
body header.header .search_mini_form .focus button > span,
body header#header .search_mini_form.floating .focus button > span {
	border-color: #' . $this->headerColors['header_search_active_button_border'] . ';
	background-color: #' . $this->headerColors['header_search_active_button_bg'] . ';
	color: #' . $this->headerColors['header_search_active_button_color'] . ';
}

/**** Header Toolbar ****/
header.header .top-cart .block-title .title-cart,
header.header .search-button,
header.header .right-menu-button {
	background-color: #' . $this->headerColors['header_toolbar_buttons_bg'] . ';
	color: #' . $this->headerColors['header_toolbar_buttons_color'] . ';
} 
header.header .top-cart .block-title .title-cart:hover,
header.header .top-cart .block-title.active .title-cart,
header.header .search-button:hover,
header.header .search-button.open,
header.header .right-menu-button:hover,
header.header .right-menu-button.open {
	background-color: #' . $this->headerColors['header_toolbar_buttons_bg_h'] . ';
	color: #' . $this->headerColors['header_toolbar_buttons_color_h'] . ';
}
header.header .top-cart .block-title .cart-qty {
	color: #' . $this->headerColors['header_toolbar_cart_counter'] . ';
	border-color: #' . $this->headerColors['header_toolbar_cart_counter_border'] . ';
}
header.header .top-cart .block-title .price {color: #' . $this->headerColors['header_toolbar_cart_price_color'] . ';}
header.header .top-cart .block-title .title-cart:hover .cart-qty,
header.header .top-cart .block-title.active .title-cart .cart-qty {
	color: #' . $this->headerColors['header_toolbar_cart_counter_h'] . ';
	border-color: #' . $this->headerColors['header_toolbar_cart_counter_border_h'] . ';
}
header.header .top-cart .block-title .title-cart:hover .price,
header.header .top-cart .block-title.active .title-cart .price {color: #' . $this->headerColors['header_toolbar_cart_price_color_h'] . ';}

/**** Header Cart ****/
header.header .top-cart .block-content {
	background-color: #' . $this->headerColors['cart_dropdown_bg'] . ';
	box-shadow: 0 0 5px '.MAGE::helper('ThemeOptionsHarbour')->RgbaColors($this->headerColors["cart_dropdown_shadow"], 1, 10) .';
}
header.header .top-cart .product-name a {color: #' . $this->headerColors['cart_dropdown_product_title'] . ';}
header.header .top-cart .product-name a:hover {color: #' . $this->headerColors['cart_dropdown_product_title_h'] . ';}
header.header .top-cart .block-content .mini-products-list .product-details .price {color: #' . $this->headerColors['cart_dropdown_product_price'] . ';}
header.header .top-cart .cart-price-qt {color: #' . $this->headerColors['cart_dropdown_count_color'] . ';}
header.header .top-cart .cart-price-qt strong {color: #' . $this->headerColors['cart_dropdown_count_strong_color'] . ';}
header.header .top-cart .block-content .item-options dt {color: #' . $this->headerColors['cart_dropdown_label_color'] . ';}
header.header .top-cart .block-content .item-options dd {color: #' . $this->headerColors['cart_dropdown_options_color'] . ';}
header.header .top-cart .btn-edit i,
header.header .top-cart .btn-remove i {color: #' . $this->headerColors['cart_dropdown_icons_color'] . ';}
header.header .top-cart .btn-edit i:hover,
header.header .top-cart .btn-remove i:hover {color: #' . $this->headerColors['cart_dropdown_icons_color_h'] . ';}
header.header .top-cart .block-content .subtotal .label {color: #' . $this->headerColors['cart_dropdown_total_color'] . ';}
header.header .top-cart .block-content .subtotal .price {color: #' . $this->headerColors['cart_dropdown_total_price_color'] . ';}
header.header .top-cart .block-content .actions {
	border-color: #' . $this->headerColors['cart_dropdown_total_border'] . ';
	border-width: ' . $this->headerColors['cart_dropdown_total_border_width'] . 'px;
}

/**** Account Block ****/
header#header .links li a.top-link-login,
header#header .customer-name {
	border-color: #' . $this->headerColors['account_border'] . ';
	border-width: ' . $this->headerColors['account_border_width'] . 'px;
	color: #' . $this->headerColors['account_color'] . ';
	background-color: #' . $this->headerColors['account_bg'] . ';
}
header#header .links li a.top-link-login:hover,
header#header .customer-name.open,
header#header .customer-name:hover {
	border-color: #' . $this->headerColors['account_border_h'] . ';
	color: #' . $this->headerColors['account_color_h'] . ';
	background-color: #' . $this->headerColors['account_bg_h'] . ';
}
header#header .customer-name + .links {background-color: #' . $this->headerColors['account_submenu_bg'] . ';}
header#header .customer-name + .links li {
	border-color: #' . $this->headerColors['account_submenu_link_divider'] . ';
	border-width: ' . $this->headerColors['account_submenu_link_divider_width'] . 'px;
} 
header#header .customer-name + .links li a {
	color: #' . $this->headerColors['account_submenu_link_color'] . ';
	background-color: #' . $this->headerColors['account_submenu_link_bg'] . ';
} 
header#header .customer-name + .links li a:hover {
	color: #' . $this->headerColors['account_submenu_link_color_h'] . ';
	background-color: #' . $this->headerColors['account_submenu_link_bg_h'] . ';
} 

/**** Right Menu ****/
.header-wrapper .header-menu {
	background-color: #' . $this->headerColors['menu_right_bg'] . ';
	color: #' . $this->headerColors['menu_right_color'] . ';
}
.header-wrapper .header-menu .right-menu h3 {
	color: #' . $this->headerColors['menu_right_title_color'] . ';
	border-color: #' . $this->headerColors['menu_right_title_border'] . ';
	border-width: ' . $this->headerColors['menu_right_title_border_w'] . 'px;
}
.header-wrapper .header-menu .right-menu li a {
	background-color: #' . $this->headerColors['menu_right_link_bg'] . ';
	color: #' . $this->headerColors['menu_right_link_color'] . ';
}
.header-wrapper .header-menu .right-menu li a:hover {
	background-color: #' . $this->headerColors['menu_right_link_bg_h'] . ';
	color: #' . $this->headerColors['menu_right_link_color_h'] . ';
}
.header-wrapper .header-menu .btn-close {color: #' . $this->headerColors['menu_right_icon_color'] . ';}
.header-wrapper .header-menu .btn-close:hover {color: #' . $this->headerColors['menu_right_icon_color_h'] . ';}

/*====== Sticky Header ======*/
body .header-wrapper header#sticky-header,
header#sticky-header .menu-line,
body.boxed-layout .header-wrapper header#sticky-header .menu-line .container_12,
header#sticky-header.floating .search_mini_form {background-color: #' . $this->stickyHeaderColors['sticky_header_bg'] . ';}

/**** Menu ****/
header#sticky-header .nav-container a.level-top {
	background-color: #' . $this->stickyHeaderColors['sticky_menu_link_bg'] . ';
	border-color: #' . $this->stickyHeaderColors['sticky_menu_link_border'] . ';
	border-width: ' . $this->stickyHeaderColors['sticky_menu_link_border_width'] . 'px;
}
header#sticky-header .nav-container a.level-top > span {color: #' . $this->stickyHeaderColors['sticky_menu_link_color'] . ';}
header#sticky-header .nav-container a.level-top:hover,
header#sticky-header .nav-container .over a.level-top {
	background-color: #' . $this->stickyHeaderColors['sticky_menu_link_bg_h'] . ';
	border-color: #' . $this->stickyHeaderColors['sticky_menu_link_border_h'] . ';
}
header#sticky-header .nav-container a.level-top:hover > span,
header#sticky-header .nav-container .over a.level-top > span {color: #' . $this->stickyHeaderColors['sticky_menu_link_color_h'] . ';}
header#sticky-header .nav-container .active a.level-top {
	background-color: #' . $this->stickyHeaderColors['sticky_menu_link_bg_a'] . ';
	border-color: #' . $this->stickyHeaderColors['sticky_menu_link_border_a'] . ';
}
header#sticky-header .nav-container .active a.level-top > span {color: #' . $this->stickyHeaderColors['sticky_menu_link_color_a'] . ';}

/**** Cart ****/
header#sticky-header  .top-cart .block-title .title-cart {
	background-color: #' . $this->stickyHeaderColors['sticky_cart_bg'] . ';
	color: #' . $this->stickyHeaderColors['sticky_cart_color'] . ';
}
header#sticky-header  .top-cart .block-title .cart-qty {
	color: #' . $this->stickyHeaderColors['sticky_cart_text_color'] . ';
	border-color: #' . $this->stickyHeaderColors['sticky_cart_text_border'] . ';
}
header#sticky-header  .top-cart .block-title .price  {color: #' . $this->stickyHeaderColors['sticky_cart_price_color'] . ';
}
header#sticky-header  .top-cart .block-title .title-cart:hover,
header#sticky-header  .top-cart .block-title.active .title-cart {
	background-color: #' . $this->stickyHeaderColors['sticky_cart_bg_h'] . ';
	color: #' . $this->stickyHeaderColors['sticky_cart_color_h'] . ';
}
header#sticky-header  .top-cart .block-title .title-cart:hover .cart-qty,
header#sticky-header  .top-cart .block-title.active .title-cart .cart-qty {
	color: #' . $this->stickyHeaderColors['sticky_cart_text_color_h'] . ';
	border-color: #' . $this->stickyHeaderColors['sticky_cart_text_border_h'] . ';
}
header#sticky-header  .top-cart .block-title .title-cart:hover .price,
header#sticky-header  .top-cart .block-title.active .title-cart .price {color: #' . $this->stickyHeaderColors['sticky_cart_price_color_h'] . ';}

/**** Search Button ****/
header#sticky-header .search-button {
	background-color: #' . $this->stickyHeaderColors['sticky_search_bg'] . '; 
	color: #' . $this->stickyHeaderColors['sticky_search_color'] . ';
}
header#sticky-header .search-button:hover,
header#sticky-header .search-button.open {
	background-color: #' . $this->stickyHeaderColors['sticky_search_bg_h'] . ';
	color: #' . $this->stickyHeaderColors['sticky_search_color_h'] . ';
}

/*====== Menu =======*/

/**** Top Level ****/
.nav-container a.level-top {
	background-color: #' . $this->menuColors['top_link_bg'] . ';
	border-color: #' . $this->menuColors['top_link_border'] . ';
	border-width: ' . $this->menuColors['top_link_border_width'] . 'px;
}
.nav-container a.level-top > span {color: #' . $this->menuColors['top_link_color'] . ';} 
.nav-container a.level-top:hover,
.nav-container .over a.level-top {
	background-color: #' . $this->menuColors['top_link_bg_h'] . ';
	border-color: #' . $this->menuColors['top_link_border_h'] . ';
}
.nav-container a.level-top:hover > span,
.nav-container .over a.level-top > span {color: #' . $this->menuColors['top_link_color_h'] . ';}
.nav-container .active a.level-top {
	background-color: #' . $this->menuColors['top_link_bg_a'] . ';
	border-color: #' . $this->menuColors['top_link_border_a'] . ';
}
.nav-container .active a.level-top > span {color: #' . $this->menuColors['top_link_color_a'] . ';}

/**** Submenu ****/
.nav-wide .menu-wrapper,
.nav ul {background-color: #' . $this->menuColors['submenu_bg'] . ';} 
.nav-wide ul.level0 li.level1 span.subtitle {
	border-width: ' . $this->menuColors['submenu_top_link_border_width'] . 'px;
	border-color: #' . $this->menuColors['submenu_top_link_border'] . ';
	color: #' . $this->menuColors['submenu_top_link_color'] . ';
}
.nav-wide ul.level0 li.level1 span.subtitle:hover {
	border-color: #' . $this->menuColors['submenu_top_link_border_h'] . ';
	color: #' . $this->menuColors['submenu_top_link_color_h'] . ';
} 
.nav-wide ul.level1 a,
.nav ul li a,
.nav-wide .menu-wrapper.default-menu ul.level0 li.level1 a {
	background-color: #' . $this->menuColors['submenu_link_bg'] . ';
	color: #' . $this->menuColors['submenu_link_color'] . ';
}
.nav-wide ul li,
.nav-wide ul.level1 ul,
.nav ul li,
.nav-wide .menu-wrapper.default-menu ul.level0 li {
	border-width: ' . $this->menuColors['submenu_link_border_width'] . 'px;
	border-color: #' . $this->menuColors['submenu_link_border'] . ';
}
.nav-wide .menu-wrapper.default-menu ul.level0 li.level1 a:hover span,
.nav-wide ul.level1 a:hover,
.nav ul li a:hover,
.nav-wide .menu-wrapper.default-menu ul.level0 li.level1 a:hover {
	background-color: #' . $this->menuColors['submenu_link_bg_h'] . ';
	color: #' . $this->menuColors['submenu_link_color_h'] . ';
}
.nav-wide ul li:hover,
.nav-wide ul.level1 ul:hover,
.nav ul li:hover,
.nav-wide .menu-wrapper.default-menu ul.level0 li:hover {border-color: #' . $this->menuColors['submenu_link_border_h'] . ';}
.nav-wide .menu-banners .text-banner .text-banner-content h4 {color: #' . $this->menuColors['submenu_banner_1_subtitle'] . ';}
.nav-wide .menu-banners .text-banner .text-banner-content h3 {color: #' . $this->menuColors['submenu_banner_1_title'] . ';}
.nav-wide .menu-banners .text-banner .text-banner-content.skin-2 h4 {color: #' . $this->menuColors['submenu_banner_2_subtitle'] . ';}
.nav-wide .menu-banners .text-banner .text-banner-content.skin-2 h3 {color: #' . $this->menuColors['submenu_banner_2_title'] . ';}
.nav-wide .menu-banners .text-banner .text-banner-content.skin-3 h4 {color: #' . $this->menuColors['submenu_banner_3_subtitle'] . ';} 
.nav-wide .menu-banners .text-banner .text-banner-content.skin-3 h3 {color: #' . $this->menuColors['submenu_banner_3_title'] . ';}
.nav-wide .menu-wrapper {color: #' . $this->menuColors['submenu_text_color'] . ';}
.nav-wide .bottom-content .quick-links {color: #' . $this->menuColors['submenu_quick_links'] . ';}
.nav-wide .bottom-content .quick-links a {color: #' . $this->menuColors['submenu_quick_links_link'] . ';}
.nav-wide .bottom-content .quick-links a:hover {color: #' . $this->menuColors['submenu_quick_links_link_h'] . ';}
.nav-wide .bottom-content .sale {color: #' . $this->menuColors['submenu_sale'] . ';}

/*====== Revolution Slider Buttons =======*/
.rev_slider_wrapper .tp-leftarrow.default,
.rev_slider_wrapper .tp-rightarrow.default {
	background-color: '.MAGE::helper('ThemeOptionsHarbour')->RgbaColors($this->revSliderButtonsColors["buttons_bg"], $this->revSliderButtonsColors['buttons_transparent_bg'], $this->revSliderButtonsColors["buttons_transparent_bg_value"]).';
	border-color:  #' . $this->revSliderButtonsColors['buttons_border'] . ';
	border-width: ' . $this->revSliderButtonsColors['buttons_border_width'] . 'px;
}
.rev_slider_wrapper .tp-leftarrow.default:after, 
.rev_slider_wrapper .tp-rightarrow.default:after {color: #' . $this->revSliderButtonsColors['buttons_color'] . ';}
.rev_slider_wrapper .tp-leftarrow.default:hover,
.rev_slider_wrapper .tp-rightarrow.default:hover {
	background-color: #' . $this->revSliderButtonsColors['buttons_bg_h'] . ';
	border-color: #' . $this->revSliderButtonsColors['buttons_border_h'] . ';
}
.rev_slider_wrapper .tp-leftarrow.default:hover:after, 
.rev_slider_wrapper .tp-rightarrow.default:hover:after {color: #' . $this->revSliderButtonsColors['buttons_color_h'] . ';} 

/*====== Parallax Banners ======*/
.parallax-banners-wrapper .text-banner .banner-content h2 {color: #' . $this->revSliderButtonsColors['colors1_h2'] . ';}
.parallax-banners-wrapper .text-banner .banner-content h3 {color: #' . $this->revSliderButtonsColors['colors1_h3'] . ';}
.parallax-banners-wrapper .text-banner .banner-content h4 {color: #' . $this->revSliderButtonsColors['colors1_h4'] . ';}
.parallax-banners-wrapper .text-banner .banner-content p {color: #' . $this->revSliderButtonsColors['colors1_p'] . ';}
.parallax-banners-wrapper .text-banner .banner-content .divider {background-color: #' . $this->revSliderButtonsColors['colors1_divider'] . ';}
.parallax-banners-wrapper .text-banner .banner-content button > span,
.parallax-banners-wrapper .text-banner .banner-content .parallax-btn:hover > span {
	border-color: #' . $this->revSliderButtonsColors['colors1_button_border'] . ';
	border-width: ' . $this->revSliderButtonsColors['colors1_button_border_width'] . 'px;
}
.parallax-banners-wrapper .text-banner .banner-content button span span,
.parallax-banners-wrapper .text-banner .banner-content .parallax-btn:hover span span {
	color: #' . $this->revSliderButtonsColors['colors1_button_text'] . ';
	background: none;
}
.parallax-banners-wrapper .text-banner .banner-content button:hover > span,
.parallax-banners-wrapper .text-banner .banner-content .parallax-btn > span {border-color: #' . $this->revSliderButtonsColors['colors1_button_border_h'] . ';}
.parallax-banners-wrapper .text-banner .banner-content button:hover span span,
.parallax-banners-wrapper .text-banner .banner-content .parallax-btn span span {
	background-color: #' . $this->revSliderButtonsColors['colors1_button_bg_h'] . ';
	color: #' . $this->revSliderButtonsColors['colors1_button_text_h'] . ';
}
.parallax-banners-wrapper .text-banner .banner-content.colors-2 h2 {color: #' . $this->revSliderButtonsColors['colors2_h2'] . ';}
.parallax-banners-wrapper .text-banner .banner-content.colors-2 h3 {color: #' . $this->revSliderButtonsColors['colors2_h3'] . ';}
.parallax-banners-wrapper .text-banner .banner-content.colors-2 h4 {color: #' . $this->revSliderButtonsColors['colors2_h4'] . ';}
.parallax-banners-wrapper .text-banner .banner-content.colors-2 .divider {background-color: #' . $this->revSliderButtonsColors['colors2_divider'] . ';}
.parallax-banners-wrapper .text-banner .banner-content.colors-2 p {color: #' . $this->revSliderButtonsColors['colors2_p'] . ';}
.parallax-banners-wrapper .text-banner .banner-content.colors-2 button > span,
.parallax-banners-wrapper .text-banner .banner-content.colors-2 .parallax-btn:hover > span {
	border-color: #' . $this->revSliderButtonsColors['colors2_button_border'] . ';
	border-width: ' . $this->revSliderButtonsColors['colors2_button_border_width'] . 'px;
}
.parallax-banners-wrapper .text-banner .banner-content.colors-2 button span span,
.parallax-banners-wrapper .text-banner .banner-content.colors-2 .parallax-btn:hover span span {
	color: #' . $this->revSliderButtonsColors['colors2_button_text'] . ';
	background: none;
}
.parallax-banners-wrapper .text-banner .banner-content.colors-2 button:hover > span,
.parallax-banners-wrapper .text-banner .banner-content.colors-2 .parallax-btn > span {border-color: #' . $this->revSliderButtonsColors['colors2_button_border_h'] . ';}
.parallax-banners-wrapper .text-banner .banner-content.colors-2 button:hover span span,
.parallax-banners-wrapper .text-banner .banner-content.colors-2 .parallax-btn span span {
	background-color: #' . $this->revSliderButtonsColors['colors2_button_bg_h'] . ';
	color: #' . $this->revSliderButtonsColors['colors2_button_text_h'] . ';
}

/*====== 404 page ======*/
.page-not-found h2 {color: #' . $this->pageNotFoundColors['title_color'] . ';}
.page-not-found h3 {color: #' . $this->pageNotFoundColors['subtitle_color'] . ';}
.page-not-found p {color: #' . $this->pageNotFoundColors['text_color'] . ';}
.page-not-found .button > span {
	border-color: #' . $this->pageNotFoundColors['button_border'] . ';
	border-width: ' . $this->pageNotFoundColors['button_border_width'] . 'px;
}
.page-not-found .button span span {color: #' . $this->pageNotFoundColors['button_text_color'] . ';}
.page-not-found .button:hover > span {border-color: #' . $this->pageNotFoundColors['button_border_h'] . ';}
.page-not-found .button:hover span span {
	color: #' . $this->pageNotFoundColors['button_text_color_h'] . ';
	background-color: #' . $this->pageNotFoundColors['button_bg_h'] . ';
}
.page-not-found .form-search input {
	background-color: '.MAGE::helper('ThemeOptionsHarbour')->RgbaColors($this->pageNotFoundColors["search_bg"], $this->pageNotFoundColors['search_transparent_bg'], $this->pageNotFoundColors["search_transparent_bg_value"]).';
	color: #' . $this->pageNotFoundColors['search_text'] . ';
}
.page-not-found .form-search button > span {background-color: '.MAGE::helper('ThemeOptionsHarbour')->RgbaColors($this->pageNotFoundColors["search_button_bg"], $this->pageNotFoundColors['search_button_transparent_bg'], $this->pageNotFoundColors["search_button_transparent_bg_value"]).';}
.page-not-found .form-search button > span i {color: #' . $this->pageNotFoundColors['search_icon'] . ';}
.page-not-found .form-search button:hover > span {background-color: '.MAGE::helper('ThemeOptionsHarbour')->RgbaColors($this->pageNotFoundColors["search_button_bg_h"], $this->pageNotFoundColors['search_button_transparent_bg_h'], $this->pageNotFoundColors["search_button_transparent_bg_h_value"]).';}
.page-not-found .form-search button:hover > span i {color: #' . $this->pageNotFoundColors['search_icon_h'] . ';}
body.cms-no-route #not-found-footer  address,
body.cms-no-route #not-found-footer  address a {color: #' . $this->pageNotFoundColors['copyright_color'] . ';}
body.cms-no-route #not-found-footer .footer-links a {color: #' . $this->pageNotFoundColors['footer_links'] . ';}
body.cms-no-route #not-found-footer .footer-links a:hover {
	color: #' . $this->pageNotFoundColors['footer_links_h'] . ';
	background-color: '.MAGE::helper('ThemeOptionsHarbour')->RgbaColors($this->pageNotFoundColors["footer_links_bg_h"], $this->pageNotFoundColors['footer_links_transparent_bg_h'], $this->pageNotFoundColors["footer_links_transparent_bg_h_value"]).';
}

/*====== Content =======*/
body .widget .widget-title h1,
body .widget .widget-title h2,
.widget-title h2,
.page-title h1,
.page-title h2,
.page-title h3,
.page-title h4,
.page-title h5,
.page-title h6,
.related-wrapper-bottom .block-title strong span,
.rating-title h2 {color: #' . $this->contentColors['title_color'] . ';}
.page-title .left-divider,
.page-title .right-divider,
.widget .widget-title .left-divider,
.widget .widget-title .right-divider,
.widget-title .left-divider,
.widget-title .right-divider,
.cart header .left-divider,
.cart header .right-divider {
	border-color: #' . $this->contentColors['title_border'] . ';
	border-width: ' . $this->contentColors['title_border_width'] . 'px;
}

/**** Toolbar ****/
.toolbar {border-color: #' . $this->contentColors['toolbar_border'] . ';}
.sorter label {color: #' . $this->contentColors['toolbar_label'] . ';}
.toolbar .sbSelector,
.toolbar .sbOptions,
.toolbar .sbHolder .sbToggleOpen + .sbSelector {background-color: #' . $this->contentColors['toolbar_select_bg'] . ';}
.toolbar .sbSelector > span,
.toolbar .sbOptions a,
.toolbar .sbHolder .sbToggleOpen + .sbSelector,
.toolbar .sbHolder .sbToggleOpen + .sbSelector > span {
	color: #' . $this->contentColors['toolbar_select_text'] . ';
	border-top-color: #' . $this->contentColors['toolbar_select_text'] . ';
}
.toolbar .sbHolder .sbToggleOpen + .sbSelector {border-color: #' . $this->contentColors['toolbar_select_text'] . ';}

/**** Pager ****/
.pages li.current {
	background-color: #' . $this->contentColors['pager_active_button_bg'] . ';
	color: #' . $this->contentColors['pager_active_button'] . ';
}
.pages li a {background-color: #' . $this->contentColors['pager_buttons_bg'] . ';}
.pages li a,
.pager .pages li a.i-previous i,
.pager .pages li a.i-next i  {color: #' . $this->contentColors['pager_buttons_color'] . ';}
.pages li a:hover,
.pager .pages li a.i-previous i:hover,
.pager .pages li a.i-next i:hover {
	color: #' . $this->contentColors['pager_buttons_color_h'] . ';
	background-color: #' . $this->contentColors['pager_buttons_bg_h'] . ';
}

/*====== Buttons =======*/

aside.sidebar .actions a,
header.header .top-cart .block-content .actions a,
a.aw-blog-read-more,
.add-to-cart-success a {
	background-color: #' . $this->buttonsColors['buttonsbg'] . ';
	color: #' . $this->buttonsColors['buttons_link'] . ';
	border-width: ' . $this->buttonsColors['buttons_border_width'] . 'px;
	border-color: #' . $this->buttonsColors['buttons_border'] . ';
}
body .button-wrapper .text-block-button:hover span span,
#popup-block .block-subscribe .button:hover span span,
#product-addtocart-button:hover span span,
.products-grid li.item .button-holder .btn-cart span span i,
aside.sidebar .block-poll .actions button span span,
.cart-table .buttons-row .buttons .btn-clear span span,
.cart-table .buttons-row .buttons .btn-update span span,
.my-wishlist .buttons-set .btn-share span span,
.my-wishlist .buttons-set .btn-add span span,
body .text-banner .banner-content button span span,
body button.button span span {
	background-color: #' . $this->buttonsColors['buttonsbg'] . ';
	color: #' . $this->buttonsColors['buttons_link'] . ';
}
body .button-wrapper .text-block-button:hover > span,
#popup-block .block-subscribe .button:hover > span,
#product-addtocart-button:hover > span,
.products-grid li.item .button-holder .btn-cart > span,
aside.sidebar .block-poll .actions button > span,
.cart-table .buttons-row .buttons .btn-clear > span,
.cart-table .buttons-row .buttons .btn-update > span,
.my-wishlist .buttons-set .btn-share > span,
.my-wishlist .buttons-set .btn-add > span,
body .text-banner .banner-content button > span,
body button.button > span {
	border-width: ' . $this->buttonsColors['buttons_border_width'] . 'px;
	border-color: #' . $this->buttonsColors['buttons_border'] . ';
}
aside.sidebar .actions a:hover,
header.header .top-cart .block-content .actions a:hover,
a.aw-blog-read-more:hover,
.add-to-cart-success a:hover {
	color: #' . $this->buttonsColors['buttons_link_h'] . ';
	border-color: #' . $this->buttonsColors['buttons_border_h'] . ';
	background-color: #' . $this->buttonsColors['buttonsbg_h'] . ';
}
body .button-wrapper .text-block-button span span,
#popup-block .block-subscribe .button span span,
#product-addtocart-button span span,
.products-grid li.item .button-holder .btn-cart:hover span span i,
aside.sidebar .block-poll .actions button:hover span span,
.cart-table .buttons-row .buttons .btn-clear:hover span span,
.cart-table .buttons-row .buttons .btn-update:hover span span,
.my-wishlist .buttons-set .btn-share:hover span span,
.my-wishlist .buttons-set .btn-add:hover span span,
body .text-banner .banner-content button:hover span span,
body button.button:hover span {
	background-color: #' . $this->buttonsColors['buttonsbg_h'] . ';
	color: #' . $this->buttonsColors['buttons_link_h'] . ';
}
body .button-wrapper .text-block-button > span,
#popup-block .block-subscribe .button > span,
#product-addtocart-button > span,
.products-grid li.item .button-holder .btn-cart:hover > span,
aside.sidebar .block-poll .actions button:hover > span,
.cart-table .buttons-row .buttons .btn-clear:hover > span,
.cart-table .buttons-row .buttons .btn-update:hover > span,
.my-wishlist .buttons-set .btn-share:hover > span,
.my-wishlist .buttons-set .btn-add:hover > span,
body .text-banner .banner-content button:hover > span,
body button.button:hover > span {
	border-color: #' . $this->buttonsColors['buttons_border_h'] . ';
}

/**** Buttons Type 2 ****/
.cart .btn-proceed-checkout > span {
	border-width: ' . $this->buttonsColors['buttons_2_border_width'] . 'px;
	border-color: #' . $this->buttonsColors['buttons_2_border'] . ';
}
.cart .btn-proceed-checkout span span {
	background-color: #' . $this->buttonsColors['buttons_2_bg'] . ';
	color: #' . $this->buttonsColors['buttons_2_link'] . ';
}
.cart .btn-proceed-checkout:hover > span {border-color: #' . $this->buttonsColors['buttons_2_border_h'] . ';}
.cart .btn-proceed-checkout:hover span span {
	background-color: #' . $this->buttonsColors['buttons_2_bg_h'] . ';
	color: #' . $this->buttonsColors['buttons_2_link_h'] . ';
}

/*====== Products ======*/
.products-list li.item .product-img-box,
.products-grid li.item .product-img-box {
	background-color: #' . $this->productsColors['products_bg'] . ';
	border-width: ' . $this->productsColors['products_border_width'] . 'px;
	border-color: #' . $this->productsColors['products_border'] . ';
}
.products-grid .product-name a,
.products-list .product-name a {color: #' . $this->productsColors['products_title_color'] . ';}
.products-grid .product-name a:hover,
.products-list .product-name a:hover {color: #' . $this->productsColors['products_title_color_h'] . ';}
.products-list .desc,
.products-grid .desc {color: #' . $this->productsColors['produst_text_color'] . ';}
.products-list .desc a,
.products-grid .desc a {color: #' . $this->productsColors['products_links_color'] . ';}
.products-list .desc a:hover,
.products-grid .desc a:hover {color: #' . $this->productsColors['products_links_color_h'] . ';}
.price-box .price {color: #' . $this->productsColors['produst_price_color'] . ';}
.old-price .price,
.price-box .old-price .price {color: #' . $this->productsColors['produst_old_price_color'] . ';}
.special-price .price {color: #' . $this->productsColors['produst_special_price_color'] . ';}
.products-list .desc,
.products-list .price-box,
.products-list .ratings,
.products-list .product-name {
	border-color: #' . $this->productsColors['products_divider_color'] . ';
	border-width: ' . $this->productsColors['products_divider_width'] . 'px;
}

/**** Product Labels ****/
.products-grid .availability-only,
.products-list .availability-only,
.label-sale {
	background-color: #' . $this->productsColors['label_sale_bg'] . ';
	color: #' . $this->productsColors['label_sale_color'] . ';
}
.label-type-5 .label-sale:before,
.products-grid.label-type-5 .availability-only:before,
.products-list.label-type-5 .availability-only:before{
	border-top-color: #' . $this->productsColors['label_sale_bg'] . ';
}
.label-type-5 .label-sale:after,
.products-grid.label-type-5 .availability-only:after,
.products-list.label-type-5 .availability-only:after{
	border-bottom-color: #' . $this->productsColors['label_sale_bg'] . ';
}
.label-new {
	background-color: #' . $this->productsColors['label_new_bg'] . ';
	color: #' . $this->productsColors['label_new_color'] . ';
}
.label-type-5 .label-new:before{
    border-top-color: #' . $this->productsColors['label_new_bg'] . ';
}
.label-type-5 .label-new:after{
    border-bottom-color: #' . $this->productsColors['label_new_bg'] . ';
}

/*====== Social Links ======*/
ul.social-links li a {
	background-color: #' . $this->socialLinksColors['social_links_bg'] . ';
	border-color: #' . $this->socialLinksColors['social_links_border'] . ';
	border-width: ' . $this->socialLinksColors['social_links_border_width'] . 'px;
}
ul.social-links li a:hover {
	background-color: #' . $this->socialLinksColors['social_links_bg_h'] . ';
	border-color: #' . $this->socialLinksColors['social_links_border_h'] . ';
}
ul.social-links li a i {
	color: #' . $this->socialLinksColors['social_links_color'] . ';
	border-color: #' . $this->socialLinksColors['social_links_divider'] . ';
	border-width: ' . $this->socialLinksColors['social_links_divider_width'] . 'px;
}
ul.social-links li a:hover i {
	color: #' . $this->socialLinksColors['social_links_color_h'] . ';
	border-color: #' . $this->socialLinksColors['social_links_divider_h'] . ';
}

/*====== Footer ======*/

/**** Top Block ****/
#footer .footer-top,
body.boxed-layout #footer .footer-top .container_12 {
	background-color: #' . $this->footerColors['top_block_bg'] . ';
	color: #' . $this->footerColors['top_block_text'] . ';
}
#footer .footer-links-block,
body.boxed-layout #' . $this->footerColors['social_links_divider_h'] . ' .footer-links-block .container_12 {
	border-color: #' . $this->footerColors['top_block_border'] . ';
	border-width: ' . $this->footerColors['top_block_border_width'] . 'px;
}
#footer a {color: #' . $this->footerColors['top_block_link'] . ';}
#footer a:hover {color: #' . $this->footerColors['top_block_link_h'] . ';}
#footer .button > span {
	border-color: #' . $this->footerColors['top_block_button_border'] . ';
	border-width: ' . $this->footerColors['top_block_button_border_width'] . 'px;
}
#footer .button span span {
	background-color: #' . $this->footerColors['top_block_button_bg'] . ';
	color: #' . $this->footerColors['top_block_button_color'] . ';
}
#footer .button:hover > span {border-color: #' . $this->footerColors['top_block_button_border_h'] . ';}
#footer .button:hover span span {
	background-color: #' . $this->footerColors['top_block_button_bg_h'] . ';
	color: #' . $this->footerColors['top_block_button_color_h'] . ';
}
#footer .footer-block-title {
	border-color: #' . $this->footerColors['top_block_title_divider'] . ';
	border-width: ' . $this->footerColors['top_block_title_divider_width'] . 'px;
}
#footer .footer-block-title h2 {color: #' . $this->footerColors['top_block_title_color'] . ';}
#footer .links li:before {background-color: #' . $this->footerColors['top_block_list_links_bg'] . ';}
#footer .links li a {color: #' . $this->footerColors['top_block_list_links_color'] . ';}
#footer .links li:after {background-color: #' . $this->footerColors['top_block_list_links_bg_h'] . ';}
#footer .links li a:hover {color: #' . $this->footerColors['top_block_list_links_color_h'] . ';}
#footer .footer-links li a {
	color: #' . $this->footerColors['top_block_default_links_color'] . ';
	background-color: #' . $this->footerColors['top_block_default_links_bg'] . ';
}
#footer .footer-links li a:hover {
	color: #' . $this->footerColors['top_block_default_links_color_h'] . ';
	background-color: #' . $this->footerColors['top_block_default_links_bg_h'] . ';
}

/**** Bottom Block ****/
#footer .footer-bottom,
body.boxed-layout #footer .footer-bottom .container_12 {
	background-color: #' . $this->footerColors['bottom_block_bg'] . ';
	color: #' . $this->footerColors['bottom_block_text'] . ';
}
#footer address,
#footer address a {color: #' . $this->footerColors['bottom_block_text'] . ';}
#footer .store-switcher label,
#footer .form-language label,
#footer .form-currency label {color: #' . $this->footerColors['bottom_block_labels'] . ';}
#footer .sbSelector {
	background-color: #' . $this->footerColors['bottom_block_select_bg'] . ';
	color: #' . $this->footerColors['bottom_block_select_color'] . ';
	border-color: #' . $this->footerColors['bottom_block_select_border'] . ';
	border-width: ' . $this->footerColors['bottom_block_select_border_width'] . 'px;
}

/**** Contact Form ****/
#footer #AjaxcontactForm li .input-box input,
#footer #AjaxcontactForm li textarea {
	background-color: #' . $this->footerColors['contact_bg'] . ';
	border-color: #' . $this->footerColors['contact_border'] . ';
	color: #' . $this->footerColors['contact_text'] . ';
}
#footer #AjaxcontactForm li label {color: #' . $this->footerColors['contact_text'] . ';}

/**** Newsletter ****/
#footer .block-subscribe label {color: #' . $this->footerColors['newsletter_label'] . ';}
#footer .block-subscribe .input-box input {
	background-color: #' . $this->footerColors['newsletter_input_bg'] . ';
	border-color: #' . $this->footerColors['newsletter_input_border'] . ';
	color: #' . $this->footerColors['newsletter_input_color'] . ';
}
#footer .block-subscribe .button span i {
	background-color: #' . $this->footerColors['newsletter_button_bg'] . ';
	color: #' . $this->footerColors['newsletter_button_color'] . ';
}
#footer .block-subscribe .button:hover span i {
	background-color: #' . $this->footerColors['newsletter_button_bg_h'] . ';
	color: #' . $this->footerColors['newsletter_button_color_h'] . '; 
}

/**** Facebook Widget ****/
.facebook-widget-wraper {background-color: #' . $this->footerColors['facebook_bg'] . ';}

/**** Footer Products List ****/
#footer .footer-products-list .product-shop .product-name a {color: #' . $this->footerColors['products_name'] . ';}
#footer .footer-products-list .product-shop .product-name a:hover {color: #' . $this->footerColors['products_name_h'] . ';}
#footer .footer-products-list .product-shop .price-box .price {color: #' . $this->footerColors['products_price_color'] . ';}
#footer .footer-products-list .product-shop .price-box .old-price .price {color: #' . $this->footerColors['products_old_price_color'] . ';}
#footer .footer-products-list .product-shop .price-box .special-price .price {color: #' . $this->footerColors['products_special_price_color'] . ';}
#footer .footer-products-list .list-small-buttons .add-to-links li i {color: #' . $this->footerColors['products_icons'] . ';}
#footer .footer-products-list .list-small-buttons .add-to-links li i:hover {color: #' . $this->footerColors['products_icons_h'] . ';}

/**** Footer Tags ****/
#footer .block-tags li a {
	color: #' . $this->footerColors['tags_color'] . ';
	border-color: #' . $this->footerColors['tags_border'] . ';
	background-color: #' . $this->footerColors['tags_bg'] . ';
}
#footer .block-tags li a:hover {
	color: #' . $this->footerColors['tags_color_h'] . ';
	border-color: #' . $this->footerColors['tags_border_h'] . ';
	background-color: #' . $this->footerColors['tags_bg_h'] . ';
}
';
}

    	$this->saveData($css);
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ThemeOptionsHarbour')->__("CSS file with custom styles has been created"));
        
        return true;
    }

    private function saveData($data)
    {
        $this->setLocation ();

        try {
	        /*$fh = fopen($file, 'w');
	       	fwrite($fh, $data);
	        fclose($fh);*/

            $fh = new Varien_Io_File(); 
            $fh->setAllowCreateFolders(true); 
            $fh->open(array('path' => $this->dirPath));
            $fh->streamOpen($this->filePath, 'w+'); 
            $fh->streamLock(true); 
            $fh->streamWrite($data); 
            $fh->streamUnlock(); 
            $fh->streamClose(); 
    	}
    	catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ThemeOptionsHarbour')->__('Failed creation custom css rules. '.$e->getMessage()));
        }
    }

}