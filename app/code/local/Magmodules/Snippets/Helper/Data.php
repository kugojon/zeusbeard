<?php
/**
 * Magmodules.eu - http://www.magmodules.eu.
 *
 * NOTICE OF LICENSE
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.magmodules.eu/MM-LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magmodules.eu so we can send you a copy immediately.
 *
 * @category      Magmodules
 * @package       Magmodules_Richsnippets
 * @author        Magmodules <info@magmodules.eu>
 * @copyright     Copyright (c) 2017 (http://www.magmodules.eu)
 * @license       https://www.magmodules.eu/terms.html  Single Service License
 */

class Magmodules_Snippets_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @return bool|mixed
     */
    public function getProduct()
    {
        $product = Mage::registry('current_product');
        return ($product && $product->getEntityId()) ? $product : false;
    }

    /**
     * @return bool|mixed
     */
    public function getCategory()
    {
        $category = Mage::registry('current_category');
        return ($category && $category->getEntityId() && !Mage::registry('current_product')) ? $category : false;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function getSnippetsEnabled($type = 'product')
    {
        $extension = Mage::getStoreConfig('snippets/general/enabled');
        if ($type == 'product') {
            $enabled = Mage::getStoreConfig('snippets/products/enabled');
        } else {
            $enabled = Mage::getStoreConfig('snippets/category/enabled');
        }

        if ($extension && $enabled) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param null $product
     * @param null $simpleId
     * @param null $simplePrice
     *
     * @return array|bool
     */
    public function getProductSnippets($product = null, $simpleId = null, $simplePrice = null)
    {
        if (empty($product)) {
            $product = $this->getProduct();
        }

        if (!empty($product)) {
            $snippets = array();
            $snippets['name'] = $product->getName();
            if ($description = $this->getProductDescription($product)) {
                $snippets['description'] = $description;
            }

            if ($thumbnail = $this->getProductThumbnail($product)) {
                $snippets['thumbnail'] = $thumbnail;
            }

            if ($image = $this->getProductImage($product, $simpleId)) {
                $snippets['image'] = $image;
            }

            $snippets['offers'] = $this->getProductOffers($product, $simplePrice);
            $snippets['availability'] = $this->getAvailability($product, $simpleId);
            $snippets['condition'] = $this->getCondition($product, $simpleId);
            $snippets['rating'] = $this->getProductRatings($product);
            $snippets['extra'] = $this->getProductExtraFields($product, $simpleId);

            $hideNoPrice = Mage::getStoreConfig('snippets/products/hide_noprice');
            if (($snippets['offers']['price_only'] < 0.01) && $hideNoPrice) {
                return false;
            } else {
                return $snippets;
            }
        }

        return false;
    }

    /**
     * @return array|bool
     */
    public function getJsonProductSnippets()
    {
        $product = $this->getProduct();
        $snippetsType = Mage::getStoreConfig('snippets/products/type');
        $snippets = array();
        if (($snippetsType == 'json') && (!empty($product))) {
            if ($product->getTypeId() == 'configurable') {
                $simples = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
                $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
                $typePrices = array();
                $attPrices = array();
                $basePrice = $product->getFinalPrice();
                foreach ($attributes as $attribute) {
                    if ($prices = $attribute->getPrices()) {
                        foreach ($prices as $price) {
                            if ($price['is_percent']) {
                                $attPrices[$price['value_index']] = (float) $price['pricing_value'] * $basePrice / 100;
                            } else {
                                $attPrices[$price['value_index']] = (float) $price['pricing_value'];
                            }
                        }
                    }
                }

                $simple = $product->getTypeInstance()->getUsedProducts();
                foreach ($simple as $sProduct) {
                    $totalPrice = $basePrice;
                    foreach ($attributes as $attribute) {
                        $value = $sProduct->getData($attribute->getProductAttribute()->getAttributeCode());
                        if (isset($attPrices[$value])) {
                            $totalPrice += $attPrices[$value];
                        }
                    }

                    $taxHelper = Mage::helper('tax');
                    $typePrices[$sProduct->getEntityId()] =
                        number_format($taxHelper->getPrice($product, $totalPrice, true), 2);
                }

                foreach ($simples as $simple) {
                    if (!empty($typePrices[$simple->getEntityId()])) {
                        if ($simple->getIsSalable()) {
                            $snippetsData = $this->getProductSnippets(
                                $product, $simple->getEntityId(), $typePrices[$simple->getEntityId()]
                            );
                            $snippets[] = $this->getJsonProductSnippetsData($snippetsData);
                        }
                    }
                }
            } else {
                $snippetsData = $this->getProductSnippets();
                $snippets[] = $this->getJsonProductSnippetsData($snippetsData);
            }

            return $snippets;
        }

        return false;
    }

    /**
     * @param $snippetsData
     *
     * @return array
     */
    public function getJsonProductSnippetsData($snippetsData)
    {
        $snippets = array();
        $snippets['@context'] = 'http://schema.org';
        $snippets['@type'] = 'Product';
        $snippets['name'] = $snippetsData['name'];
        if (!empty($snippetsData['description'])) {
            $snippets['description'] = $snippetsData['description'];
        }

        if (!empty($snippetsData['image'])) {
            $snippets['image'] = $snippetsData['image'];
        }

        $snippets['offers']['@type'] = $snippetsData['offers']['type'];

        if (isset($snippetsData['availability'])) {
            $snippets['offers']['availability'] = $snippetsData['availability']['url'];
        }

        if (isset($snippetsData['offers']['price_low'])) {
            $snippets['offers']['lowprice'] = $snippetsData['offers']['clean_low'];
        } else {
            $snippets['offers']['price'] = $snippetsData['offers']['clean'];
        }

        $snippets['offers']['priceCurrency'] = $snippetsData['offers']['currency'];
        if (isset($snippetsData['condition'])) {
            if (isset($snippetsData['condition']['url'])) {
                $snippets['offers']['itemCondition'] = $snippetsData['condition']['url'];
            }
        }

        if (!empty($snippetsData['offers']['seller'])) {
            $snippets['offers']['seller']['@type'] = 'Organization';
            $snippets['offers']['seller']['name'] = $snippetsData['offers']['seller'];
        }

        if (isset($snippetsData['offers']['extra_offer'])) {
            $offers = array();
            $offers[] = $snippetsData['offers'];
            foreach ($snippetsData['offers']['extra_offer'] as $extraOffer) {
                if ($extraOffer['currency'] != $snippets['offers']['priceCurrency']) {
                    $offersExtra['@type'] = $snippetsData['offers']['type'];
                    $offersExtra['availability'] = $snippetsData['availability']['url'];
                    $offersExtra['price'] = $extraOffer['price'];
                    $offersExtra['priceCurrency'] = $extraOffer['currency'];
                    $offers[] = $offersExtra;
                }
            }

            $snippets['offers'] = $offers;
        }

        if ((isset($snippetsData['rating']['count'])) && ($snippetsData['rating']['percentage'] > 0)) {
            $snippets['aggregateRating']['@type'] = 'AggregateRating';
            $snippets['aggregateRating']['ratingValue'] = $snippetsData['rating']['avg'];
            $snippets['aggregateRating']['bestRating'] = $snippetsData['rating']['best'];
            $snippets['aggregateRating'][$snippetsData['rating']['type']] = $snippetsData['rating']['count'];
        }

        if ($extrafields = $snippetsData['extra']) {
            foreach ($extrafields as $field) {
                $snippets[$field['itemprop']] = $field['clean'];
            }
        }

        return $snippets;
    }

    /**
     * @return array|bool
     */
    public function getCategorySnippets()
    {
        if ($category = $this->getCategory()) {
            if (Mage::getStoreConfig('snippets/category/categories_filter')) {
                $cats = Mage::getStoreConfig('snippets/category/categories');
                if (!empty($cats)) {
                    $cats = explode(',', $cats);
                    if (!in_array($category->getId(), $cats)) {
                        return false;
                    }
                } else {
                    return false;
                }
            }

            $snippets = array();
            $snippets['name'] = $category->getName();
            if ($description = $this->getCategoryDescription($category)) {
                $snippets['description'] = $description;
            }

            if ($thumbnail = $this->getCategoryThumbnail($category)) {
                $snippets['thumbnail'] = $thumbnail;
            }

            $snippets['offers'] = $this->getCategoryOffers($category);
            $snippets['availability']['url'] = 'http://schema.org/InStock';
            $snippets['availability']['text'] = Mage::helper('snippets')->__('In stock');
            $snippets['rating'] = $this->getCategoryRatings($category);

            if (($snippets['offers']['clean_low'] < 0.01) && (Mage::getStoreConfig('snippets/category/noprice'))) {
                return false;
            } else {
                return $snippets;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getJsonCategorySnippets()
    {
        $snippetsData = $this->getCategorySnippets();
        $snippetsType = Mage::getStoreConfig('snippets/category/type');
        $category = $this->getCategory();
        if (!empty($snippetsData) && ($snippetsType == 'json') && (!empty($category))) {
            $snippets['@context'] = 'http://schema.org';
            $snippets['@type'] = 'Product';
            $snippets['name'] = $snippetsData['name'];
            if (isset($snippetsData['description'])) {
                $snippets['description'] = $snippetsData['description'];
            }

            if (isset($snippetsData['thumbnail'])) {
                $snippets['image'] = $snippetsData['thumbnail'];
            }

            if (isset($snippetsData['offers']['price_low'])) {
                $snippets['offers']['@type'] = 'AggregateOffer';
                if (isset($snippetsData['availability'])) {
                    $snippets['offers']['availability'] = 'http://schema.org/InStock';
                }

                if (isset($snippetsData['offers']['price_high'])) {
                    $snippets['offers']['lowprice'] = $snippetsData['offers']['clean_low'];
                    $snippets['offers']['highprice'] = $snippetsData['offers']['clean_high'];
                } else {
                    $snippets['offers']['lowprice'] = $snippetsData['offers']['clean_low'];
                }

                $snippets['offers']['priceCurrency'] = $snippetsData['offers']['currency'];
                if (!empty($snippetsData['offers']['seller'])) {
                    $snippets['offers']['seller']['@type'] = 'Organization';
                    $snippets['offers']['seller']['name'] = $snippetsData['offers']['seller'];
                }
            }

            if ((isset($snippetsData['rating']['count'])) && ($snippetsData['rating']['percentage'] > 0)) {
                $snippets['aggregateRating']['@type'] = 'AggregateRating';
                $snippets['aggregateRating']['ratingValue'] = $snippetsData['rating']['avg'];
                $snippets['aggregateRating']['bestRating'] = $snippetsData['rating']['best'];
                $snippets['aggregateRating'][$snippetsData['rating']['type']] = $snippetsData['rating']['count'];
            }

            if (($snippetsData['offers']['qty'] < 1) && (Mage::getStoreConfig('snippets/category/noprice'))) {
                return false;
            } else {
                return $snippets;
            }
        }

        return false;
    }

    /**
     * @return array|bool
     */
    public function getLocalBusinessSnippets()
    {
        $snippets = array();
        if (Mage::getStoreConfig('snippets/system/localbusiness')) {
            $snippets['@context'] = 'http://schema.org';
            $snippets['@type'] = Mage::getStoreConfig('snippets/system/seller_type');
            $snippets['@id'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            if ($name = Mage::getStoreConfig('snippets/system/name')) {
                $snippets['name'] = $name;
            }

            if ($telephone = Mage::getStoreConfig('snippets/system/telephone')) {
                $snippets['telephone'] = $telephone;
            }

            if ($logoUrl = Mage::getStoreConfig('snippets/system/logo_url')) {
                $snippets['image'] = $logoUrl;
            }

            if ($priceRange = Mage::getStoreConfig('snippets/system/price_range')) {
                $snippets['priceRange'] = $priceRange;
            }

            if ($street = Mage::getStoreConfig('snippets/system/street')) {
                $snippets['address']['@type'] = 'PostalAddress';
                $snippets['address']['streetAddress'] = $street;

                if ($locality = Mage::getStoreConfig('snippets/system/locality')) {
                    $snippets['address']['addressLocality'] = $locality;
                }

                if ($region = Mage::getStoreConfig('snippets/system/region')) {
                    $snippets['address']['addressRegion'] = $region;
                }

                if ($postalcode = Mage::getStoreConfig('snippets/system/postalcode')) {
                    $snippets['address']['postalCode'] = $postalcode;
                }

                if ($country = Mage::getStoreConfig('snippets/system/country')) {
                    $snippets['address']['addressCountry'] = $country;
                }

                $latitude = Mage::getStoreConfig('snippets/system/latitude');
                $longitude = Mage::getStoreConfig('snippets/system/longitude');
                if (!empty($latitude) && !empty($longitude)) {
                    $snippets['geo']['@type'] = 'GeoCoordinates';
                    $snippets['geo']['latitude'] = $latitude;
                    $snippets['geo']['longitude'] = $longitude;
                }
            }

            $openinghours = @unserialize(Mage::getStoreConfig('snippets/system/openinghours'));
            if (!empty($openinghours)) {
                foreach ($openinghours as $open) {
                    $openingArray = array();
                    $openingArray['@type'] = 'OpeningHoursSpecification';
                    $openingArray['dayOfWeek'] = $open['day'];
                    $openingArray['opens'] = $open['from'];
                    $openingArray['closes'] = $open['to'];
                    $snippets['openingHoursSpecification'][] = $openingArray;
                    unset($openingArray);
                }
            }

            if (!empty($snippets)) {
                if (Mage::getStoreConfig('snippets/system/ratings')) {
                    if (Mage::getStoreConfig('snippets/system/rating_schema') == 'LocalBusiness') {
                        if ($reviews = $this->getAggregateRating()) {
                            if (Mage::getStoreConfig('snippets/system/index_only')) {
                                $controller = Mage::app()->getFrontController()->getAction()->getFullActionName();
                                if ($controller == 'cms_index_index') {
                                    $snippets['aggregateRating'] = $reviews;
                                }
                            } else {
                                $snippets['aggregateRating'] = $reviews;
                            }
                        }
                    }
                }

                return $snippets;
            }
        }

        return false;
    }

    /**
     * @return array|bool
     */
    public function getOrganizationSnippets()
    {
        $snippets = array();
        if (Mage::getStoreConfig('snippets/system/organization')) {
            $snippets['@context'] = 'http://schema.org';
            $snippets['@type'] = 'Organization';
            $snippets['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

            if ($logoUrl = Mage::getStoreConfig('snippets/system/logo_url')) {
                $snippets['logo'] = $logoUrl;
            }

            $contacts = @unserialize(Mage::getStoreConfig('snippets/system/contacts'));
            if (!empty($contacts)) {
                $contArray = array();
                foreach ($contacts as $contact) {
                    if (!empty($contact['telephone'])) {
                        $contArray['@type'] = 'ContactPoint';
                        $contArray['telephone'] = $contact['telephone'];
                        if (!empty($contact['contacttype'])) {
                            $contArray['contactType'] = $contact['contacttype'];
                        }

                        if (!empty($contact['contactoption'])) {
                            if (strpos($contact['contactoption'], ',') !== false) {
                                $contArray['contactOption'] = explode(',', $contact['contactoption']);
                            } else {
                                $contArray['contactOption'] = $contact['contactoption'];
                            }
                        }

                        if (!empty($contact['area'])) {
                            if (strpos($contact['area'], ',') !== false) {
                                $contArray['areaServed'] = explode(',', $contact['area']);
                            } else {
                                $contArray['areaServed'] = $contact['area'];
                            }
                        }

                        if (!empty($contact['languages'])) {
                            if (strpos($contact['languages'], ',') !== false) {
                                $contArray['availableLanguage'] = explode(',', $contact['languages']);
                            } else {
                                $contArray['availableLanguage'] = $contact['languages'];
                            }
                        }
                    }

                    if (!empty($contArray)) {
                        $snippets['contactPoint'][] = $contArray;
                    }
                }
            }

            $socialUrls = @unserialize(Mage::getStoreConfig('snippets/system/social'));
            if (!empty($socialUrls)) {
                foreach ($socialUrls as $social) {
                    if (!empty($social['url'])) {
                        $snippets['sameAs'][] = $social['url'];
                    }
                }
            }

            if (!empty($snippets)) {
                if (Mage::getStoreConfig('snippets/system/ratings')) {
                    if (Mage::getStoreConfig('snippets/system/rating_schema') == 'Organization') {
                        if ($reviews = $this->getAggregateRating()) {
                            if (Mage::getStoreConfig('snippets/system/index_only')) {
                                $controller = Mage::app()->getFrontController()->getAction()->getFullActionName();
                                if ($controller == 'cms_index_index') {
                                    $snippets['aggregateRating'] = $reviews;
                                }
                            } else {
                                $snippets['aggregateRating'] = $reviews;
                            }
                        }
                    }
                }

                return $snippets;
            }
        }

        return false;
    }

    /**
     * @return array|bool
     */
    public function getWebsiteSnippets()
    {
        $search = array();
        if (Mage::getStoreConfig('snippets/system/sitelinkssearch')) {
            if (Mage::app()->getFrontController()->getAction()->getFullActionName() == 'cms_index_index') {
                $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                $search['@type'] = 'SearchAction';
                $search['target'] = $baseUrl . 'catalogsearch/result/?q={search_term_string}';
                $search['query-input'] = 'required name=search_term_string';
            }
        }

        $sitename = Mage::getStoreConfig('snippets/system/sitename');
        if (!empty($search) || !empty($sitename)) {
            $sitenameName = Mage::getStoreConfig('snippets/system/sitename_name');
            $sitenameAlternate = Mage::getStoreConfig('snippets/system/sitename_alternate');
            $snippets = array();
            $snippets['@context'] = 'http://schema.org';
            $snippets['@type'] = 'WebSite';
            $snippets['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            if (!empty($sitename) && !empty($sitenameName)) {
                $snippets['name'] = $sitenameName;
            }

            if (!empty($sitename) && !empty($sitenameAlternate)) {
                $snippets['alternateName'] = $sitenameAlternate;
            }

            if (!empty($search)) {
                $snippets['potentialAction'] = $search;
            }

            return $snippets;
        }

        return false;
    }

    /**
     * @return array|bool
     */
    public function getWebPageSnippets()
    {
        $snippets = array();
        if (Mage::getStoreConfig('snippets/system/ratings')) {
            if (Mage::getStoreConfig('snippets/system/rating_schema') == 'WebPage') {
                if ($reviews = $this->getAggregateRating()) {
                    if (Mage::getStoreConfig('snippets/system/index_only')) {
                        if (Mage::app()->getFrontController()->getAction()->getFullActionName() == 'cms_index_index') {
                            $snippets['@context'] = 'http://schema.org';
                            $snippets['@type'] = 'WebPage';
                            $snippets['aggregateRating'] = $reviews;
                        }
                    } else {
                        $snippets['@context'] = 'http://schema.org';
                        $snippets['@type'] = 'WebPage';
                        $snippets['aggregateRating'] = $reviews;
                    }
                }
            }
        }

        if (!empty($snippets)) {
            return $snippets;
        }

        return false;
    }

    /**
     * @return array|bool
     */
    public function getJsonBlogSnippets()
    {
        $snippets = array();
        $enable = Mage::getStoreConfig('snippets/blog/enable');
        if (Mage::app()->getFrontController()->getAction()->getFullActionName() == 'blog_post_view') {
            if ($blog = Mage::getSingleton('blog/post')) {
                $post = Mage::getModel('blog/post')->load($blog->getId());
                $title = $post->getTitle();
                $user = $post->getUser();
                $shortContent = $post->getShortContent();
                $postContent = $post->getPostContent();
                $createdTime = $post->getCreatedTime();
                $updateTime = $post->getUpdateTime();
                if (empty($updateTime)) {
                    $updateTime = $createdTime;
                }

                $imageWidth = '';
                $imageHeight = '';
                $imageUrl = '';

                $processor = Mage::getModel('core/email_template_filter');
                $postContent = $processor->filter($postContent);
                preg_match_all('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $postContent, $image);
                if (!empty($image['src'])) {
                    foreach ($image['src'] as $img) {
                        if (!preg_match('(http|//)', $img)) {
                            $imgLoc = Mage::getBaseDir('base') . $img;
                            if (file_exists($imgLoc)) {
                                list($width, $height) = getimagesize($imgLoc);
                                $img = Mage::getBaseUrl() . $img;
                            } else {
                                $width = $height = 0;
                            }
                        } else {
                            list($width, $height) = getimagesize($img);
                        }

                        if ($width > 0 && ($width > $imageWidth)) {
                            $imageUrl = $img;
                            $imageWidth = $width;
                            $imageHeight = $height;
                            if ($imageWidth > 695) {
                                break;
                            }
                        }
                    }
                }

                if (($enable > 1) && (empty($imageUrl))) {
                    return false;
                }

                if ($enable == 3) {
                    if ($imageWidth < 695) {
                        return false;
                    }
                }

                $snippets['@context'] = 'http://schema.org';
                $snippets['@type'] = 'BlogPosting';
                $snippets['mainEntityOfPage']['@type'] = 'WebPage';
                $snippets['mainEntityOfPage']['@id'] = $post->getAddress();
                $snippets['headline'] = trim(strip_tags($title));
                if (!empty($imageUrl)) {
                    $snippets['image']['@type'] = 'ImageObject';
                    $snippets['image']['url'] = $imageUrl;
                    $snippets['image']['height'] = $imageHeight;
                    $snippets['image']['width'] = $imageWidth;
                }

                $snippets['datePublished'] = $createdTime;
                $snippets['dateModified'] = $updateTime;
                $snippets['author']['@type'] = 'Person';
                $snippets['author']['name'] = $user;
                $snippets['publisher']['@type'] = 'Organization';
                $snippets['publisher']['name'] = Mage::getStoreConfig('snippets/system/sitename_name');

                if ($logo = Mage::getStoreConfig('snippets/blog/logo_url')) {
                    list($logoWidth, $logoHeight) = getimagesize($logo);
                    $snippets['publisher']['logo']['@type'] = 'ImageObject';
                    $snippets['publisher']['logo']['url'] = Mage::getStoreConfig('snippets/blog/logo_url');
                    $snippets['publisher']['logo']['width'] = $logoWidth;
                    $snippets['publisher']['logo']['height'] = $logoHeight;
                }

                $snippets['description'] = trim(strip_tags($shortContent));
            }
        }

        if (!empty($snippets)) {
            return $snippets;
        }

        return false;
    }

    /**
     * @return array|bool
     */
    public function getAggregateRating()
    {
        $ratingValue = '';
        $ratingCount = '';
        $ratingBest = '';

        $type = Mage::getStoreConfig('snippets/system/rating_source');
        if ($type == 'shopreview') {
            if (Mage::helper('core')->isModuleEnabled('Magmodules_Shopreview')) {
                $total = Mage::helper('shopreview')->getTotalScore();
                if (isset($total['total'])) {
                    $ratingValue = $total['total'];
                    $ratingCount = Mage::helper('shopreview')->getReviewCount();
                    $ratingBest = '100';
                }
            }
        }

        if ($type == 'feedbackcompany') {
            if (Mage::helper('core')->isModuleEnabled('Magmodules_Feedbackcompany')) {
                $total = Mage::helper('feedbackcompany')->getTotalScore();
                if (isset($total['percentage'])) {
                    $ratingValue = $total['percentage'];
                    $ratingCount = $total['votes'];
                    $ratingBest = '100';
                }
            }
        }

        if ($type == 'webwinkelkeur') {
            if (Mage::helper('core')->isModuleEnabled('Magmodules_Webwinkelconnect')) {
                $total = Mage::helper('webwinkelconnect')->getTotalScore();
                if (isset($total['average'])) {
                    $ratingValue = $total['average'];
                    $ratingCount = $total['votes'];
                    $ratingBest = '100';
                }
            }
        }

        if ($type == 'trustpilot') {
            if (Mage::helper('core')->isModuleEnabled('Magmodules_Trustpilot')) {
                $total = Mage::helper('trustpilot')->getTotalScore();
                if (isset($total['score'])) {
                    $ratingValue = $total['score'];
                    $ratingCount = $total['votes'];
                    $ratingBest = '100';
                }
            }
        }

        if ($type == 'kiyoh') {
            if (Mage::helper('core')->isModuleEnabled('Magmodules_Kiyoh')) {
                $total = Mage::helper('kiyoh')->getTotalScore();
                if (isset($total['score'])) {
                    $ratingValue = $total['score'];
                    $ratingCount = $total['votes'];
                    $ratingBest = '100';
                }
            }
        }

        if (($ratingValue > 0) && ($ratingCount > 0)) {
            $rating = array();
            $rating['@type'] = 'AggregateRating';
            $rating['ratingValue'] = $ratingValue;
            $rating['reviewCount'] = $ratingCount;
            $rating['bestRating'] = $ratingBest;
            return $rating;
        }

        return false;
    }

    /**
     * @param $breadcrumbs
     *
     * @return bool
     */
    public function getJsonBreadcrumbs($breadcrumbs)
    {
        if ($breadcrumbs) {
            $cacheKeyInfo = $breadcrumbs->getCacheKeyInfo();
            if (!empty($cacheKeyInfo['crumbs'])) {
                $crumbs = unserialize(base64_decode($cacheKeyInfo['crumbs']));
                $listitems = array();
                $i = 1;
                if ($crumbs) {
                    $snippets['@context'] = 'http://schema.org';
                    $snippets['@type'] = 'BreadcrumbList';
                    foreach ($crumbs as $crumb) {
                        if ($crumb['link']) {
                            $list['@type'] = 'ListItem';
                            $list['position'] = $i;
                            $list['item']['@id'] = $crumb['link'];
                            if ($i == 1) {
                                $list['item']['name'] = $this->getFirstBreadcrumbTitle($crumb['label']);
                            } else {
                                $list['item']['name'] = $crumb['label'];
                            }

                            $listitems[] = $list;
                            $i++;
                        }
                    }

                    $snippets['itemListElement'] = $listitems;
                    return $snippets;
                }
            }
        }

        return false;
    }

    /**
     * @return array|bool
     */
    public function getProductMetatags()
    {
        if ($product = $this->getProduct()) {
            $meta = array();
            $pinEnabled = Mage::getStoreConfig('snippets/metadata/product_pinterest');
            $twitterUser = Mage::getStoreConfig('snippets/metadata/twitter_name');
            $twitterEnabled = Mage::getStoreConfig('snippets/metadata/product_twitter');
            $productMarkup = Mage::getStoreConfig('snippets/products/type');
            $productEnabled = Mage::getStoreConfig('snippets/products/enabled');
            $offers = '';
            $availability = '';

            if (($twitterUser && $twitterEnabled) || ($pinEnabled)) {
                $offers = $this->getProductOffers($product);
                $availability = $this->getAvailability($product);
            }

            if ($pinEnabled) {
                if ((!$productEnabled) || ($productMarkup == 'json')) {
                    $meta['og:type'] = 'product';
                    $meta['og:title'] = htmlspecialchars($product->getName());
                    if (isset($offers['clean'])) {
                        $meta['product:price:amount'] = $offers['clean'];
                        $meta['product:price:currency'] = $offers['currency'];
                    }

                    if (Mage::getStoreConfig('snippets/system/sitename_name')) {
                        $meta['og:site_name'] = Mage::getStoreConfig('snippets/system/sitename_name');
                    }

                    $meta['og:url'] = $this->getCurrentUrl();
                    $description = str_replace(array("\r\n", "\r", "\n"), '', $this->getProductDescription($product));
                    $meta['og:description'] = htmlspecialchars($description);
                    $meta['og:image'] = $this->getProductImage($product);
                    if (isset($availability['url'])) {
                        if ($availability['url'] == 'http://schema.org/InStock') {
                            $meta['og:availability'] = 'instock';
                        } else {
                            $meta['og:availability'] = 'out of stock';
                        }
                    }
                }
            }

            if ($twitterUser && $twitterEnabled) {
                $prices = $offers;
                if ($twitterUser[0] != '@') {
                    $twitterUser = '@' . $twitterUser;
                }

                $meta['twitter:card'] = 'summary';
                $meta['twitter:site'] = $twitterUser;
                $meta['twitter:title'] = htmlspecialchars($product->getName());
                if ($description = $this->getProductDescription($product)) {
                    $description = str_replace(array("\r\n", "\r", "\n"), '', $description);
                    $meta['twitter:description'] = htmlspecialchars($description);
                } else {
                    $meta['twitter:description'] = htmlspecialchars($product->getName()) . ' - ' . $prices['price'];
                }

                $meta['twitter:image'] = $this->getProductImage($product);
            }

            return $meta;
        }

        return false;
    }

    /**
     * @return array|bool
     */
    public function getCategoryMetatags()
    {
        if ($category = $this->getCategory()) {
            $meta = array();
            $pinEnabled = Mage::getStoreConfig('snippets/metadata/category_pinterest');
            $twitterUser = Mage::getStoreConfig('snippets/metadata/twitter_name');
            $twitterEnabled = Mage::getStoreConfig('snippets/metadata/category_twitter');
            $categoryMarkup = Mage::getStoreConfig('snippets/category/type');
            $categoryEnabled = Mage::getStoreConfig('snippets/category/enabled');

            if (($twitterUser && $twitterEnabled) || ($pinEnabled)) {
                $offers = $this->getCategoryOffers($category);
            } else {
                $offers = '';
            }

            if ($pinEnabled) {
                if ((!$categoryEnabled) || ($categoryMarkup == 'json')) {
                    if (isset($offers['clean_low'])) {
                        $meta['og:type'] = 'product';
                        $meta['og:title'] = htmlspecialchars($category->getName());
                        $meta['product:price:amount'] = $offers['clean_low'];
                        $meta['product:price:currency'] = $offers['currency'];
                        if (Mage::getStoreConfig('snippets/system/sitename_name')) {
                            $meta['og:site_name'] = Mage::getStoreConfig('snippets/system/sitename_name');
                        }

                        $meta['og:url'] = $this->getCurrentUrl();

                        $bad = array("\r\n", "\r", "\n");
                        $description = str_replace($bad, '', $this->getCategoryDescription($category));
                        $meta['og:description'] = htmlspecialchars($description);
                        if ($image = $this->getCategoryImage($category)) {
                            $meta['og:image'] = $this->getCategoryImage($category);
                        }

                        $meta['og:availability'] = 'instock';
                    }
                }
            }

            if ($twitterUser && $twitterEnabled) {
                $prices = $offers;
                if ($twitterUser[0] != '@') {
                    $twitterUser = '@' . $twitterUser;
                }

                $meta['twitter:card'] = 'summary';
                $meta['twitter:site'] = $twitterUser;
                $meta['twitter:title'] = htmlspecialchars($category->getName());
                if ($description = $this->getProductDescription($category)) {
                    $description = str_replace(array("\r\n", "\r", "\n"), '', $description);
                    $meta['twitter:description'] = htmlspecialchars($description);
                } else {
                    if (!empty($prices['price_low'])) {
                        $description = htmlspecialchars($category->getName()) . ' - ' . $prices['price_low'];
                        $meta['twitter:description'] = $description;
                    }
                }

                $meta['twitter:image'] = $this->getCategoryImage($category);
            }

            return $meta;
        }

        return false;
    }

    /**
     * @return array|bool
     */
    public function getCmsMetatags()
    {
        $cmsMetadata = Mage::getStoreConfig('snippets/metadata/cms_metadata');
        $twitter = Mage::getStoreConfig('snippets/metadata/cms_twitter');
        if ($cmsMetadata || $twitter) {
            $meta = array();
            $cmsTitle = Mage::getStoreConfig('snippets/metadata/cms_title');
            $cmsDescription = Mage::getStoreConfig('snippets/metadata/cms_description');
            $cmsLogo = Mage::getStoreConfig('snippets/metadata/cms_logo');
            $logoUrl = Mage::getStoreConfig('snippets/system/logo_url');
            $twitterUsername = Mage::getStoreConfig('snippets/metadata/twitter_name');

            if ($cmsMetadata && ($cmsTitle || $cmsDescription || $cmsLogo)) {
                if ($cmsTitle) {
                    $meta['og:title'] = htmlspecialchars(Mage::getSingleton('cms/page')->getTitle());
                }

                if ($cmsDescription) {
                    $meta['og:description'] = htmlspecialchars(Mage::getSingleton('cms/page')->getDescription());
                }

                if ($cmsLogo && $logoUrl) {
                    $meta['og:image'] = $logoUrl;
                }

                if (Mage::getSingleton('cms/page')->getIdentifier() == 'home') {
                    $meta['og:type'] = 'website';
                } else {
                    $meta['og:type'] = 'article';
                }

                $meta['og:url'] = $this->getCurrentUrl();
            }

            if ($twitter && $twitterUsername) {
                $meta['twitter:card'] = 'summary';
                $meta['twitter:site'] = '@' . $twitterUsername;
                $meta['twitter:title'] = htmlspecialchars(Mage::getSingleton('cms/page')->getTitle());
                $meta['twitter:description'] = htmlspecialchars(Mage::getSingleton('cms/page')->getDescription());
                if ($logoUrl) {
                    $meta['twitter:image'] = $logoUrl;
                }
            }

            return $meta;
        }

        return false;
    }

    /**
     * @param      $product
     * @param null $simpleId
     *
     * @return array|bool
     */
    public function getAvailability($product, $simpleId = null)
    {
        $showStock = Mage::getStoreConfig('snippets/products/stock');
        if ($showStock) {
            $availability = array();
            if (!empty($simpleId)) {
                $availability['url'] = 'http://schema.org/InStock';
                $availability['text'] = Mage::helper('snippets')->__('In stock');
            } else {
                if ($product->isAvailable()) {
                    $availability['url'] = 'http://schema.org/InStock';
                    $availability['text'] = Mage::helper('snippets')->__('In stock');
                } else {
                    $availability['url'] = 'http://schema.org/OutOfStock';
                    $availability['text'] = Mage::helper('snippets')->__('Out of Stock');
                }
            }

            return $availability;
        }

        return false;
    }

    /**
     * @param      $product
     * @param null $simpleId
     *
     * @return array|bool
     */
    public function getCondition($product, $simpleId = null)
    {
        $conditionType = Mage::getStoreConfig('snippets/products/condition');
        if ($conditionType) {
            $condition = array();
            if ($conditionType == 1) {
                $conditionDefault = ucfirst(Mage::getStoreConfig('snippets/products/condition_default'));
                if ($conditionDefault) {
                    $condition['url'] = 'http://schema.org/' . $conditionDefault . 'Condition';
                    $condition['text'] = Mage::helper('snippets')->__($conditionDefault);
                }
            }

            if ($conditionType == 2) {
                $productCondition = ucfirst($this->getProductCondition($product, $simpleId));
                if ($productCondition) {
                    $condition['url'] = 'http://schema.org/' . $productCondition . 'Condition';
                    $condition['text'] = Mage::helper('snippets')->__($productCondition);
                }
            }

            return $condition;
        }

        return false;
    }

    /**
     * @param $product
     */
    public function getProductThumbnail($product)
    {
        return Mage::helper('catalog/image')->init($product, 'small_image')->resize(75);
    }

    /**
     * @param      $product
     * @param null $simpleId
     *
     * @return string
     */
    public function getProductImage($product, $simpleId = null)
    {
        $image = '';
        if (!empty($simpleId)) {
            $_resource = Mage::getSingleton('catalog/product')->getResource();
            $imageRaw = $_resource->getAttributeRawValue($simpleId, 'image', Mage::app()->getStore());
            if (($imageRaw != 'no_selection') && !empty($imageRaw)) {
                $image = Mage::getModel('catalog/product_media_config')->getMediaUrl($imageRaw);
            }
        }

        if (empty($image)) {
            if ($product->getImage() != 'no_selection') {
                $image = Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage());
            }
        }

        return $image;
    }

    /**
     * @param $category
     *
     * @return bool
     */
    public function getCategoryImage($category)
    {
        if ($imageUrl = $category->getImageUrl()) {
            return $imageUrl;
        }

        return false;
    }

    /**
     * @param $category
     *
     * @return bool|string
     */
    public function getCategoryThumbnail($category)
    {
        if ($imageUrl = $category->getThumbnail()) {
            return Mage::getBaseUrl('media') . 'catalog/category/' . $imageUrl;
        }

        return false;
    }

    /**
     * @param      $product
     * @param null $simplePrice
     *
     * @return array
     */
    public function getProductOffers($product, $simplePrice = null)
    {
        $price = '';
        $offers = array();
        if (Mage::getStoreConfig('snippets/products/prices') == 'custom') {
            $attribute = Mage::getStoreConfig('snippets/products/price_attribute');
            $price = $product[$attribute];
        } else {
            if ($product->getTypeId() == 'grouped') {
                if ($price = $this->getPriceGrouped($product)) {
                    $cleanLow = Mage::helper('core')->currency($price, false, false);
                    $offers['price_low'] = Mage::helper('core')->currency($price, true, false);
                    $offers['clean_low'] = number_format($cleanLow, 2, '.', '');
                }
            }

            if ($product->getTypeId() == 'bundle') {
                $price = $this->getPriceBundle($product);
            }

            if (!empty($simplePrice)) {
                $price = Mage::helper('core')->currency($simplePrice, false, false);
            }

            if (!$price && empty($simplePrice)) {
                $price = Mage::helper('core')->currency($product->getFinalPrice(), false, false);
                $price = Mage::helper('tax')->getPrice($product, $price, true);
            }
        }

        if (Mage::getStoreConfig('snippets/products/prices') == 'notax') {
            $tax = Mage::getStoreConfig('snippets/products/taxperc');
            if ($tax > 0) {
                $price = (($price / (100 + $tax)) * 100);
            }
        }

        $offers['price_only'] = number_format($price, 2, '.', '');
        $offers['clean'] = number_format($price, 2, '.', '');
        $offers['price'] = number_format($price, 2, '.', '');
        $offers['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
        $offers['seller'] = Mage::getStoreConfig('snippets/products/seller');

        if (isset($offers['price_low'])) {
            $offers['type'] = 'http://schema.org/AggregateOffer';
        } else {
            $offers['type'] = 'http://schema.org/Offer';
        }

        if (Mage::getStoreConfig('snippets/products/mulitple_currencies')) {
            if ($configCurrencies = Mage::getStoreConfig('snippets/products/currencies')) {
                $currencyModel = Mage::getModel('directory/currency');
                $currencies = $currencyModel->getConfigAllowCurrencies();
                $rates = $currencyModel->getCurrencyRates($offers['currency'], $currencies);
                if (is_array($rates)) {
                    $curArray = explode(',', $configCurrencies);
                    foreach ($curArray as $currency) {
                        if (isset($rates[$currency])) {
                            $price = ($rates[$currency] * $price);
                            $offers['extra_offer'][$currency]['price'] = number_format($price, 2, '.', '');
                            $offers['extra_offer'][$currency]['currency'] = $currency;
                        }
                    }
                }
            }
        }

        return $offers;
    }

    /**
     * @param $product
     *
     * @return bool|string
     */
    public function getPriceGrouped($product)
    {
        $price = '';
        $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
        foreach ($associatedProducts as $_item) {
            $priceAssociated = Mage::helper('tax')->getPrice($_item, $_item->getFinalPrice(), true);
            if (($priceAssociated < $price) || ($price == '')) {
                $price = $priceAssociated;
            }
        }

        if ($price > 0) {
            return $price;
        }

        return false;
    }

    /**
     * @param $product
     *
     * @return int
     */
    public function getPriceBundle($product)
    {


        if (($product->getPriceType() == '1') && ($product->getFinalPrice() > 0)) {
            $price = $product->getFinalPrice();
        } else {
            $priceModel = $product->getPriceModel();
            $block = Mage::getSingleton('core/layout')->createBlock('bundle/catalog_product_view_type_bundle');
            $options = $block->setProduct($product)->getOptions();
            $price = 0;
            $storeId = Mage::app()->getStore()->getStoreId();
            foreach ($options as $option) {
                $selection = $option->getDefaultSelection();
                if ($selection === null) {
                    continue;
                }

                $selectionProductId = $selection->getProductId();
                $_resource = Mage::getSingleton('catalog/product')->getResource();
                $finalPrice = $_resource->getAttributeRawValue($selectionProductId, 'final_price', $storeId);
                $selectionQty = $_resource->getAttributeRawValue($selectionProductId, 'selection_qty', $storeId);
                $price += ($finalPrice * $selectionQty);
            }
        }

        if ($price < 0.01) {
            $priceArray = Mage::getModel('bundle/product_price')->getTotalPrices($product, '', true);
            if (isset($priceArray[0])) {
                $price = $priceArray[0];
                $price = Mage::helper('core')->currency($price, false, false);
            }
        }

        return $price;
    }

    /**
     * @param $category
     *
     * @return array
     */
    public function getCategoryOffers($category)
    {
        $offers = array();
        if (Mage::getStoreConfig('snippets/products/prices') == 'custom') {
            $priceAttribute = Mage::getStoreConfig('snippets/products/price_attribute');
            $catProducts = Mage::getModel('catalog/product')->getCollection()
                ->addCategoryFilter(Mage::registry('current_category'))
                ->addAttributeToSelect($priceAttribute)
                ->addAttributeToFilter($priceAttribute, array('gt' => 0))
                ->load();
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($catProducts);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($catProducts);
            $qty = $catProducts;

            if (!empty($catProducts)) {
                $prices = array();
                foreach ($catProducts as $catproduct) {
                    $prices[] = $catproduct[$priceAttribute];
                }

                if ($prices) {
                    $priceLow = min($prices);
                    $priceHigh = max($prices);
                }
            }
        } else {
            $magentoVersion = Mage::getVersion();
            if (version_compare($magentoVersion, '1.7.2', '>=')) {
                $priceLow = Mage::getSingleton('catalog/layer')->getProductCollection()->getMinPrice();
                $priceHigh = Mage::getSingleton('catalog/layer')->getProductCollection()->getMaxPrice();
                $qty = Mage::getSingleton('catalog/layer')->getProductCollection()->getSize();
                if ($priceLow < 0.01) {
                    $priceLow = '0.0001';
                }
            } else {
                $category = Mage::getModel('catalog/category')->load(Mage::registry('current_category')->getId());
                $productColl = Mage::getModel('catalog/product')->getCollection()->addCategoryFilter($category)
                    ->addAttributeToFilter('visibility', array('eq' => 4))
                    ->addAttributeToFilter('status', array('eq' => 1))
                    ->addAttributeToFilter('price', array('gt' => 0))
                    ->addAttributeToSort('price', 'asc')
                    ->addAttributeToSelect('price')
                    ->setPageSize(1)->load();
                $qty = count($productColl);
                $lowestProductPrice = $productColl->getFirstItem()->getPrice();
                $priceLow = $lowestProductPrice;
            }
        }

        if (Mage::getStoreConfig('snippets/products/prices') == 'notax') {
            $tax = Mage::getStoreConfig('snippets/products/taxperc');
            if ($tax > 0) {
                if ($priceLow) {
                    $priceLow = (($priceLow / (100 + $tax)) * 100);
                }

                if ($priceHigh) {
                    $priceHigh = (($priceHigh / (100 + $tax)) * 100);
                }
            }
        }

        if (isset($priceLow)) {
            $offers['price_low'] = Mage::helper('core')->formatPrice($priceLow, false);
            $offers['clean_low'] = number_format($priceLow, 2, '.', '');
        }

        if ((isset($priceHigh)) && (Mage::getStoreConfig('snippets/category/prices') == 'range')) {
            $offers['price_high'] = Mage::helper('core')->formatPrice($priceHigh, false);
            $offers['clean_high'] = number_format($priceHigh, 2, '.', '');
        }

        $offers['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
        $offers['seller'] = Mage::getStoreConfig('snippets/products/seller');

        if (!isset($offers['price_low'])) {
            $offers['price_low'] = Mage::helper('core')->currency('0.00', true, false);
            $offers['clean_low'] = '0.00';
        }

        $offers['qty'] = $qty;
        return $offers;
    }

    /**
     * @param $product
     *
     * @return bool|string
     */
    public function getProductDescription($product)
    {
        if (Mage::getStoreConfig('snippets/products/description')) {
            $attribute = Mage::getStoreConfig('snippets/products/description_attribute');
            if ($attribute) {
                $description = trim(strip_tags($product[$attribute]));
            } else {
                $description = trim(strip_tags($product->getShortDescription()));
            }

            $descriptionLenght = Mage::getStoreConfig('snippets/products/description_lenght');
            if ($descriptionLenght > 0) {
                $description = Mage::helper('core/string')->truncate($description, $descriptionLenght, '...');
            }

            return $description;
        }

        return false;
    }

    /**
     * @param $category
     *
     * @return bool|string
     */
    public function getCategoryDescription($category)
    {
        if (Mage::getStoreConfig('snippets/category/description')) {
            $description = strip_tags($category->getDescription());
            if ($description) {
                $descriptionLenght = Mage::getStoreConfig('snippets/category/description_lenght');
                if ($descriptionLenght > 0) {
                    $description = Mage::helper('core/string')->truncate($description, $descriptionLenght, '...');
                }

                return $description;
            }
        }

        return false;
    }

    /**
     * @param $product
     *
     * @return array|bool
     */
    public function getProductRatings($product)
    {
        $showReviews = Mage::getStoreConfig('snippets/products/reviews');
        if ($showReviews) {
            if ((Mage::getStoreConfig('snippets/products/reviews_source') == 'yotpo')
                &&
                Mage::helper('core')->isModuleEnabled('Yotpo_Yotpo')
            ) {
                return $this->getYotpoReviews();
            } else {
                $summaryData = Mage::getModel('review/review_summary')
                    ->setStoreId(Mage::app()->getStore()->getStoreId())
                    ->load($product->getId());
                $rating = array();
                $rating['count'] = $summaryData->getReviewsCount();
                $rating['percentage'] = $summaryData->getRatingSummary();
                if (Mage::getStoreConfig('snippets/products/reviews_metric') == '5') {
                    $rating['avg'] = round(($summaryData->getRatingSummary() / 20), 1);
                    $rating['best'] = '5';
                } else {
                    $rating['avg'] = round($summaryData->getRatingSummary());
                    $rating['best'] = '100';
                }

                if (Mage::getStoreConfig('snippets/products/reviews_type') == 'votes') {
                    $rating['type'] = 'ratingCount';
                } else {
                    $rating['type'] = 'reviewCount';
                }

                if ($summaryData->getReviewsCount() > 0) {
                    return $rating;
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getYotpoReviews()
    {
        $rating = array();
        $yotpoSnippets = Mage::helper('yotpo/richSnippets')->getRichSnippet();
        if (isset($yotpoSnippets["average_score"])) {
            if ($yotpoSnippets["average_score"] > 0) {
                $ratingSummary = $yotpoSnippets["average_score"];
                $rating['percentage'] = ($ratingSummary * 20);
                $rating['avg'] = $ratingSummary;
                $rating['count'] = $yotpoSnippets["reviews_count"];
                $rating['best'] = '5';
                $rating['type'] = 'ratingCount';
                if (Mage::getStoreConfig('snippets/products/reviews_metric') != '5') {
                    $rating['avg'] = ($ratingSummary * 20);
                    $rating['best'] = '100';
                }

                return $rating;
            }
        }

        return $rating;
    }

    /**
     * @param $category
     *
     * @return array|bool
     */
    public function getCategoryRatings($category)
    {
        $enabled = Mage::getStoreConfig('snippets/category/reviews');
        if ($enabled) {
            $count = '';
            $ratingSummary = '';
            $cacheKey = 'ratings-snippets-' . $category->getId() . '-' . Mage::app()->getStore()->getId();
            if ($catRatings = @unserialize(Mage::app()->getCacheInstance()->load($cacheKey))) {
                $count = $catRatings['reviews_count'];
                $ratingSummary = ($catRatings['rating_summary'] / $catRatings['qty']);
            } else {
                $productIds = $category->getProductCollection()
                    ->addAttributeToFilter('visibility', array('neq' => 1))
                    ->addAttributeToFilter('status', 1)
                    ->getAllIds();

                if ($productIds) {
                    $resource = Mage::getSingleton('core/resource');
                    $readConnection = $resource->getConnection('core_read');
                    $reviewEntitySummaryTable = $resource->getTableName('review_entity_summary');
                    $exists = (boolean) Mage::getSingleton('core/resource')->getConnection('core_write')
                        ->showTableStatus($reviewEntitySummaryTable);

                    if ($exists) {
                        $query = "SELECT SUM(rating_summary) as 'rating_summary', 
                                  SUM(reviews_count) as 'reviews_count', 
                                  COUNT(entity_pk_value) as 'qty' 
                                  FROM " . $reviewEntitySummaryTable . "
                                  WHERE store_id = " . Mage::app()->getStore()->getId() . "
                                  AND rating_summary > 0 
                                  AND reviews_count > 0 
                                  AND entity_pk_value IN (" . implode(',', array_map("intval", $productIds)) . ")
                                 ";
                        $catRatings = $readConnection->fetchRow($query);
                        if (!empty($catRatings['rating_summary']) && !empty($catRatings['reviews_count'])) {
                            $count = $catRatings['reviews_count'];
                            $ratingSummary = ($catRatings['rating_summary'] / $catRatings['qty']);
                            $cacheData = serialize($catRatings);
                            $storeId = Mage::app()->getStore()->getId();
                            $cacheKey = 'ratings-snippets-' . $category->getId() . '-' . $storeId;
                            Mage::app()->getCacheInstance()->save($cacheData, $cacheKey);
                        }
                    }
                }
            }

            if (empty($count)) {
                return false;
            }

            $rating = array();
            $rating['count'] = $count;
            $rating['percentage'] = ($ratingSummary);
            if (Mage::getStoreConfig('snippets/category/reviews_metric') == '5') {
                $rating['avg'] = round(($ratingSummary / 20), 1);
                $rating['best'] = '5';
            } else {
                $rating['avg'] = round($ratingSummary);
                $rating['best'] = '100';
            }

            if (Mage::getStoreConfig('snippets/category/reviews_type') == 'votes') {
                $rating['type'] = 'ratingCount';
            } else {
                $rating['type'] = 'reviewCount';
            }

            return $rating;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getCurrentUrl()
    {
        $url = Mage::helper('core/url')->getCurrentUrl();
        $url = preg_replace('/\?.*/', '', $url);
        return $url;
    }

    /**
     * @param      $product
     * @param null $simpleId
     *
     * @return array
     */
    public function getProductExtraFields($product, $simpleId = null)
    {
        $fields = array();

        $attributesArr = @unserialize(Mage::getStoreConfig('snippets/products/attributes'));
        $customArr = @unserialize(Mage::getStoreConfig('snippets/products/cus_attributes'));
        $fieldsArr = array_merge($attributesArr, $customArr);

        if (count($fieldsArr)) {
            foreach ($fieldsArr as $field) {
                $value = $this->getProductAttributeValue($product, $field, $simpleId);
                if (!empty($value)) {
                    if ($field['type'] == 'brand') {
                        $data = '<span itemprop="brand" itemscope itemtype="http://schema.org/Brand">';
                        $data .= '<span itemprop="name">' . $value . '</span></span>';
                        $fields[] = array(
                            'value'    => $data,
                            'label'    => 'Brand',
                            'clean'    => $value,
                            'itemprop' => 'brand'
                        );
                    } else {
                        $data = '<span itemprop="' . $field['type'] . '">' . $value . '</span>';
                        $fields[] = array(
                            'value'    => $data,
                            'label'    => ucfirst($field['type']),
                            'clean'    => $value,
                            'itemprop' => $field['type']
                        );
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * @param      $product
     * @param null $simpleId
     *
     * @return bool|mixed|string
     */
    public function getProductCondition($product, $simpleId = null)
    {
        $conditionShow = Mage::getStoreConfig('snippets/products/condition');
        if ($conditionShow) {
            $attribute = Mage::getStoreConfig('snippets/products/condition_attribute');
            if (!empty($simpleId)) {
                $condition = $this->getProductAttributeValueById($simpleId, $attribute);
                if (!empty($condition)) {
                    return $condition;
                }
            }

            if ($condition = $product->getAttributeText($attribute)) {
                if (!is_array($condition)) {
                    return $condition;
                }
            } else {
                if ($condition = $product[$attribute]) {
                    return $condition;
                }
            }
        }

        return false;
    }

    /**
     * @return bool|mixed
     */
    public function getMarkup()
    {
        if (Mage::registry('product')) {
            return Mage::getStoreConfig('snippets/products/type');
        } elseif (Mage::registry('current_category') && !Mage::registry('product')) {
            return Mage::getStoreConfig('snippets/category/type');
        }

        return false;
    }

    /**
     * @return bool|mixed
     */
    public function getContent()
    {
        if (Mage::registry('current_product')) {
            $type = Mage::getStoreConfig('snippets/products/type');
            if ($type == 'visible') {
                if (Mage::getStoreConfig('snippets/products/location') == 'advanced') {
                    return Mage::getStoreConfig('snippets/products/location_custom');
                } else {
                    return Mage::getStoreConfig('snippets/products/location');
                }
            }

            if ($type == 'footer') {
                return Mage::getStoreConfig('snippets/products/location_ft');
            }
        } elseif (Mage::registry('current_category') && !Mage::registry('product')) {
            $type = Mage::getStoreConfig('snippets/category/type');
            if ($type == 'visible') {
                return Mage::getStoreConfig('snippets/category/location');
            }

            if ($type == 'footer') {
                return Mage::getStoreConfig('snippets/category/location_ft');
            }
        }

        return false;
    }

    /**
     * @return bool|mixed
     */
    public function getPosition()
    {
        if (Mage::registry('current_product')) {
            $type = Mage::getStoreConfig('snippets/products/type');
            if ($type == 'visible') {
                return Mage::getStoreConfig('snippets/products/position');
            }

            if ($type == 'footer') {
                return Mage::getStoreConfig('snippets/products/position_ft');
            }
        } elseif (Mage::registry('current_category') && !Mage::registry('product')) {
            $type = Mage::getStoreConfig('snippets/category/type');
            if ($type == 'visible') {
                return Mage::getStoreConfig('snippets/category/position');
            }

            if ($type == 'footer') {
                return Mage::getStoreConfig('snippets/category/position_ft');
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        $enabled = Mage::getStoreConfig('snippets/general/enabled');
        $block = '';
        $enabledEnt = '';
        $type = '';
        if (Mage::registry('current_product')) {
            $enabledEnt = Mage::getStoreConfig('snippets/products/enabled');
            $type = Mage::getStoreConfig('snippets/products/type');
            if ($type == 'visible') {
                $block = Mage::getStoreConfig('snippets/products/location');
            }

            if ($type == 'footer') {
                $block = Mage::getStoreConfig('snippets/products/location_ft');
            }
        } elseif (Mage::registry('current_category')) {
            $enabledEnt = Mage::getStoreConfig('snippets/category/enabled');
            $type = Mage::getStoreConfig('snippets/category/type');
            if ($type == 'visible') {
                $block = Mage::getStoreConfig('snippets/category/location');
            }

            if ($type == 'footer') {
                $block = Mage::getStoreConfig('snippets/category/location_ft');
            }
        }

        if (($block == '') || ($enabled == '') || ($enabledEnt == '') || ($type == 'hidden')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $title
     *
     * @return mixed
     */
    public function getFirstBreadcrumbTitle($title)
    {
        $custom = Mage::getStoreConfig('snippets/system/breadcrumbs_custom');
        $enabled = Mage::getStoreConfig('snippets/system/breadcrumbs');
        $customname = Mage::getStoreConfig('snippets/system/breadcrumbs_customname');
        if ($custom && $enabled && $customname) {
            return $customname;
        }

        return $title;
    }

    /**
     * @return bool|mixed
     */
    public function getFilterHash()
    {
        if ($category = $this->getCategory()) {
            $url = str_replace('?___SID=U', '', $category->getUrl());
            $diff = str_replace(Mage::getBaseUrl(), '', $url);
            return $diff;
        }

        return false;
    }

    /**
     * @param      $product
     * @param      $data
     * @param null $simpleId
     *
     * @return mixed
     */
    public function getProductAttributeValue($product, $data, $simpleId = null)
    {
        $value = '';
        $inputType = $data['input_type'];
        $source = $data['source'];
        $type = $data['type'];

        if (!empty($simpleId)) {
            $value = $this->getProductAttributeValueById($simpleId, $source, $inputType);
        }

        if (empty($value)) {
            if ($inputType == 'select') {
                $value = $product->getAttributeText($source);
            } elseif ($inputType == 'multiselect') {
                if (count($product->getAttributeText($source))) {
                    if (count($product->getAttributeText($source)) > 1) {
                        $value = implode(',', $product->getAttributeText($source));
                    } else {
                        $value = $product->getAttributeText($source);
                    }
                }
            } else {
                if (isset($product[$source])) {
                    $value = $product[$source];
                }
            }
        }

        if (!empty($value)) {
            if ($type == 'gtin8') {
                $value = str_pad($value, 8, "0", STR_PAD_LEFT);
            }

            if ($type == 'gtin12') {
                $value = str_pad($value, 12, "0", STR_PAD_LEFT);
            }

            if ($type == 'gtin13') {
                $value = str_pad($value, 13, "0", STR_PAD_LEFT);
            }

            if ($type == 'gtin14') {
                $value = str_pad($value, 14, "0", STR_PAD_LEFT);
            }
        }

        return $this->escapeHtml($value);
    }

    /**
     * @param        $productId
     * @param        $attribute
     * @param string $inputType
     *
     * @return bool|mixed|string
     */
    public function getProductAttributeValueById($productId, $attribute, $inputType = '')
    {
        if (empty($inputType)) {
            $att = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attribute);
            $inputType = $att->getFrontendInput();
        }

        $_resource = Mage::getSingleton('catalog/product')->getResource();
        $value = $_resource->getAttributeRawValue($productId, $attribute, Mage::app()->getStore());

        if (empty($value)) {
            return false;
        }

        if ($inputType == 'select') {
            $attributeDetails = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attribute);
            $value = $attributeDetails->getSource()->getOptionText($value);

            return $this->escapeHtml($value);
        } elseif ($inputType == 'multiselect') {
            $multivalue = '';
            $values = explode(',', $value);
            foreach ($values as $value) {
                $attributeDetails = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attribute);
                $multivalue .= $attributeDetails->getSource()->getOptionText($value) . ',';
            }

            return rtrim($multivalue, ',');
        } else {
            return $this->escapeHtml($value);
        }
    }

}