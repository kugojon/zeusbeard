<?php

/**
 * @author Alan Barber <alan@cadence-labs.com>
 */
class Cadence_Fbpixel_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isVisitorPixelEnabled()
    {
        return Mage::getStoreConfig("cadence_fbpixel/visitor/enabled");
    }

    public function isConversionPixelEnabled()
    {
        return Mage::getStoreConfig("cadence_fbpixel/conversion/enabled");
    }

    public function isAddToCartPixelEnabled()
    {
        return Mage::getStoreConfig("cadence_fbpixel/add_to_cart/enabled");
    }

    public function isAddToWishlistPixelEnabled()
    {
        return Mage::getStoreConfig('cadence_fbpixel/add_to_wishlist/enabled');
    }

    public function isInitiateCheckoutPixelEnabled()
    {
        return Mage::getStoreConfig('cadence_fbpixel/inititiate_checkout/enabled');
    }

    public function isViewProductPixelEnabled()
    {
        return Mage::getStoreConfig('cadence_fbpixel/view_product/enabled');
    }

    public function isSearchPixelEnabled()
    {
        return Mage::getStoreConfig('cadence_fbpixel/search/enabled');
    }

    public function getVisitorPixelId()
    {
        return Mage::getStoreConfig("cadence_fbpixel/visitor/pixel_id");
    }

    public function getConversionPixelId()
    {
        return Mage::getStoreConfig("cadence_fbpixel/conversion/pixel_id");
    }
    public function escapeSingleQuotes($str)
    {
        return str_replace("'", "\'", $str);
    }
    public function getOrderData()
    {
        $order_id = Mage::getSingleton('checkout/session')->getLastOrderId();
        $order = Mage::getModel('sales/order')->load($order_id);
        $orderId = $order->getIncrementId();
        
        if ($orderId) {
            $items = [];
    
            foreach ($order->getAllVisibleItems() as $item) {
                $product = Mage::getModel('catalog/product')->load($item['product_id']);
                $cats = $product->getCategoryIds();
                    foreach ($cats as $category_id) {
                        $_cat = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($category_id);
                            $cate_name =  $_cat->getName();             
                        
                        $items[] = [
                            'name' => $item->getName(), 'sku' => $item->getSku(),'cate_name'=>$cate_name
                        ];

                    }
            }
    
            $data = [];
    
            if (count($items) === 1) {
                $data['content_name'] = $this->escapeSingleQuotes($items[0]['name']);
                $data['content_category'] = $this->escapeSingleQuotes($items[0]['cate_name']);
            }
    
            $ids = '';
            foreach ($items as $i) {
                $ids .= "'" . $this->escapeSingleQuotes($i['sku']) . "', ";
            }
    
            $data['content_ids']  = trim($ids, ", ");
            $data['content_type'] = 'product';
            $data['value']        = number_format(
                $order->getGrandTotal(),
                2,
                '.',
                ''
            );
            $data['currency']     = $order->getOrderCurrencyCode();
            
            return $data;
        } else {
            return null;
        }
    }
    /**
     * @param $event
     * @param $data
     * @return string
     */
    public function getPixelHtml($event, $data = false)
    {
        $id = $this->getVisitorPixelId();
        $json = '';
        $query = '';
        if ($data) {
            $json = ', ' . json_encode($data);
        }
        $html = <<<HTML
    <!-- Begin Facebook {$event} Pixel -->
    <script type="text/javascript">
        fbq('track', '{$event}'{$json});
    </script>
    <!-- End Facebook {$event} Pixel -->
HTML;
        return $html;
    }
}