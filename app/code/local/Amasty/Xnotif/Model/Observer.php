<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */  
class Amasty_Xnotif_Model_Observer extends Mage_ProductAlert_Model_Observer
{
    /**
     * Helper instance
     *
     * @var null|Amasty_Xnotif_Helper_Data
     */
    protected $_helper = null;

    protected function _isEnabledQtyLimit()
    {
        return Mage::getStoreConfig('amxnotif/general/email_limit');
    }

    protected function _processStock(Mage_ProductAlert_Model_Email $email)
    {
        if (!$this->_isEnabledQtyLimit()) {
            $this->_foreachAlert('stock', $email);
        }
    }
    
    protected function _processPrice(Mage_ProductAlert_Model_Email $email)
    {
        $this->_foreachAlert('price', $email);
    }
    
    public function handleBlockAlert($observer) 
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();

        if ($block instanceof Mage_Productalert_Block_Product_View) {
            switch ($block->getNameInLayout()) {
                case 'productalert.stock':
                    $block = $this->_getHelper()->getStockAlertBlock($block);
                    break;
                case 'productalert.price':
                    $block = $this->_getHelper()->getPriceAlertBlock($block);
                    break;
            }
            $observer->setBlock($block);
        }
    }

    public function handleBlockAlertOnCategory($observer)
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();
        if (
            $block instanceof Mage_Catalog_Block_Product_List &&
            Mage::getStoreConfig('amxnotif/stock/on_category')
        ) {
            $html = $observer->getTransport()->getHtml();
            $subscribeBlock = $this->_getHelper()->getStockAlertBlockCategory();
            preg_match_all('/price[a-z\-]*?([0-9]*?)"/', $html, $productsId);
            $ids = array_unique($productsId[1]);

            $collection = Mage::getModel('catalog/product')->getCollection();
            $collection->addFieldToFilter('entity_id', array('in' => $ids));
            $collection->addStoreFilter(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*');

            foreach ($collection as $_product) {
                $html = $this->processProduct($html, $_product, $subscribeBlock);
            }

            $observer->getTransport()->setHtml($html);
        }
    }
    
    public function notify()
    {
        if ($this->_isEnabledQtyLimit()) {
            $email = Mage::getModel('productalert/email');
            /** @var $email Mage_ProductAlert_Model_Email */
            $this->_foreachAlert('stock', $email, true);
        }

        if ( !Mage::getStoreConfig('amxnotif/general/notify_admin') ) {
            return;
        }

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $tpl = Mage::getModel('core/email_template');

        $stockAlertTable = Mage::getSingleton('core/resource')->getTableName('productalert/stock');
        $collection = Mage::getModel('amxnotif/product')->getCollection();
        $collection->addAttributeToSelect('name')
            ->addAttributeToFilter(
                'status',
                array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            );

        $select = $collection->getSelect();

        $select->joinRight(
            array('s' => $stockAlertTable),
            's.product_id = e.entity_id',
            array(
                'total_cnt' => 'count(s.product_id)',
                'cnt'       => 'COUNT( NULLIF(`s`.`status`, 1) )',
                'last_d'    => 'MAX(add_date)',
                'product_id'
            )
        )
            ->where('DATE(add_date) = DATE(NOW())')
            ->group(array('s.product_id'));

        $tableBlock = Mage::app()->getLayout()->createBlock('core/template')
            ->setCollection($collection)
            ->setTemplate('amasty/amxnotif/admin_email.phtml');

        $html = $tableBlock->toHtml();

        $currentDate = Mage::getModel('core/date')->date('Y-m-d');
        $emails =  explode(',', Mage::getStoreConfig('amxnotif/general/email_to'));
        if (count($emails)) {
            $emails = array_map('trim', $emails);
        }
        $tpl->setDesignConfig(array('area'=>'frontend'))
            ->sendTransactional(
                Mage::getStoreConfig('amxnotif/general/template'),
                'general',
                $emails,
                Mage::helper('amxnotif')->__('Administrator'),
                array(
                    'date'  => $currentDate,
                    'html'  => $html,
                    'name'  => Mage::getStoreConfig('trans_email/ident_general/name')
                )
            );
        $translate->setTranslateInline(true);
    }

    protected function _foreachAlert($type, $email, $enableLimit = false)
    {
        $email->setType($type);
        foreach ($this->_getWebsites() as $website) {
            /** @var $website Mage_Core_Model_Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }
            
            if (!Mage::getStoreConfig(self::XML_PATH_STOCK_ALLOW, $website->getDefaultGroup()->getDefaultStore()->getId())) {
                continue;
            }
            
            try {
                $collection = Mage::getModel('productalert/' . $type)
                    ->getCollection()
                    ->addWebsiteFilter($website->getId())
                    ->addFieldToFilter('status', 0)
                    ->setCustomerOrder();
            }
            catch (Exception $e) {
                Mage::log($e->getMessage());
                $this->_errors[] = $e->getMessage();
                return $this;
            }
            $previousCustomer = null;
            $email->setWebsite($website);

            $productJoinAlertData = array();
            foreach ($collection as $alert) {
                $storeId = $alert->getStoreId()? $alert->getStoreId() : $website->getDefaultStore()->getId();
                try {
                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($storeId)
                        ->load($alert->getProductId());
                    /** @var $product Mage_catalog_Model_Product */
                    if (!$product) {
                        continue;
                    }

                    if ($enableLimit) {
                        if (array_key_exists($alert->getProductId(), $productJoinAlertData)) {
                            if ($productJoinAlertData[$alert->getProductId()]['qty']
                                    <= $productJoinAlertData[$alert->getProductId()]['counter']) {
                                continue;
                            } else {
                                $productJoinAlertData[$alert->getProductId()]['counter']++;
                            }
                        } else {
                            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                            $quantity  = $stockItem->getData('qty');

                            $productJoinAlertData[$alert->getProductId()] = array(
                                'qty'     => $quantity,
                                'counter' => 1
                            );
                        }
                    }
                    $isGuest = (0 == $alert->getCustomerId())? 1: 0;

                    if (!$previousCustomer
                        || ($previousCustomer->getId() != $alert->getCustomerId())
                        || ($previousCustomer->getEmail() != $alert->getEmail())
                    ) {
                        if ($isGuest) {
                            $customer = Mage::getModel('customer/customer');
                            $customer->setWebsiteId($website->getId());
                            $customer->loadByEmail($alert->getEmail());
                            
                            if (!$customer->getId()) {
                                $customer->setEmail($alert->getEmail());
                                $customer->setStoreId($storeId);
                                $customer->setFirstname(
                                    Mage::getStoreConfig('amxnotif/general/customer_name', $storeId)
                                );
                                $customer->setGroupId(0);
                                $customer->setId(0);
                            }
                        } else {
                            $customer = Mage::getModel('customer/customer')->load($alert->getCustomerId());
                        }
                        if ($previousCustomer) {
                            $email->send();
                        }

                        if (!$customer) {
                            continue;
                        }
                        $previousCustomer = $customer;
                        $email->clean();
                        $email->setCustomer($customer);
                        if ($storeId == $website->getDefaultStore()->getId() && $customer->getStoreId()) {
                            $storeId = $customer->getStoreId();
                        }
                    } else {
                        $customer = $previousCustomer;
                    }
                      
                    $product->setCustomerGroupId($customer->getGroupId());

                    /*
                     * check alert data by type
                     * */
                    if ('stock' == $type) {
                        $minQuantity = Mage::getStoreConfig('amxnotif/general/min_qty');
                        if($minQuantity < 1) $minQuantity = 1;

                        $isInStock = false;
                        if ($product->isConfigurable() && $product->isInStock()) {
                            $allProducts = $product->getTypeInstance(true)->getUsedProducts(null, $product);

                            foreach ($allProducts as $simpleProduct) {
                                $stockItem   = Mage::getModel('cataloginventory/stock_item')
                                    ->setStoreId($storeId)->loadByProduct($simpleProduct);
                                $quantity = $stockItem->getData('qty');
                                $isInStock = ($simpleProduct->isSalable() || $simpleProduct->isInStock())
                                    && $quantity >= $minQuantity;
                                if ($isInStock) {
                                    break;
                                }
                            }
                        } else {
                            if ($product->getTypeId() === 'bundle') {
                                $isInStock = $product->isSalable() || $product->isSaleable();
                            } else {
                                $stockItem   = Mage::getModel('cataloginventory/stock_item')
                                    ->setStoreId($storeId)
                                    ->loadByProduct($product);
                                $quantity = $stockItem->getData('qty');
                                $isInStock = ($product->isSalable() || $product->isSaleable())
                                    && (int)$quantity >= (int)$minQuantity;
                            }
                        }
                        if ($isInStock) {
                            if ($alert->getParentId() && !$product->isConfigurable()) {
                                $parentProduct = Mage::getModel('catalog/product')
                                    ->setStoreId($storeId)
                                    ->load($alert->getParentId());
                                $product->setData('parent_id', $alert->getParentId());
                                $product->setData('url', $parentProduct->getProductUrl());

                            }

                            $email->addStockProduct($product);
                            $alert->setSendDate(Mage::getModel('core/date')->gmtDate());

                            $alert->setSendCount($alert->getSendCount() + 1);
                            $alert->setStatus(1);
                            $alert->save();
                        }
                    } else {
                        if ($alert->getPrice() > $product->getFinalPrice()) {
                            $productPrice = $product->getFinalPrice();
                            $product->setFinalPrice(Mage::helper('tax')->getPrice($product, $productPrice));
                            $product->setPrice(Mage::helper('tax')->getPrice($product, $product->getPrice()));
                            $email->addPriceProduct($product);

                            $alert->setPrice($productPrice);
                            $alert->setLastSendDate(Mage::getModel('core/date')->gmtDate());

                            $alert->setSendCount($alert->getSendCount() + 1);
                            $alert->setStatus(1);
                            $alert->save();
                        }
                    }

                }
                catch (Exception $e) {
                    Mage::log($e->getMessage());
                    $this->_errors[] = $e->getMessage();
                }
            }
            if ($previousCustomer) {
                try {
                    $email->send();
                }
                catch (Exception $e) {
                    Mage::log($e->getMessage());
                    $this->_errors[] = $e->getMessage();
                }
            }
        }
        return $this;    
    }

    /**
     * Retrieve helper instance
     *
     * @return Amasty_Xnotif_Helper_Data|null
     */
    protected function _getHelper()
    {
        if ($this->_helper === null) {
            $this->_helper = Mage::helper('amxnotif');
        }
        return $this->_helper;
    }

    private function processProduct($html, $product, $subscribeBlock)
    {
        $productId = $product->getId();
        $template = '@(product.*?-price-' . $productId . '">(.*?)div>)@s';
        preg_match_all($template, $html, $res);
        if (!$res[0]) {
            $template = '@(price-including-tax-' . $productId . '">(.*?)div>)@s';
            preg_match_all($template, $html, $res);
            if (!$res[0]) {
                $template = '@(price-excluding-tax-' . $productId . '">(.*?)div>)@s';
                preg_match_all($template, $html, $res);
            }
        }

        if ($res[0]) {
            $subscribeBlock->setData('product', $product);
            $subscribeHtml = $subscribeBlock->toHtml();
            if ($subscribeHtml) {
                $replace = $res[1][0] . $subscribeHtml;
                $html = str_replace($res[0][0], $replace, $html);
            }
        }

        return $html;
    }
}
