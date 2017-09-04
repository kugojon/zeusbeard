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

class Magmodules_Snippets_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function setSnippetsData(Varien_Event_Observer $observer)
    {
        if (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalog') {
            $helper = Mage::helper('snippets');
            $position = $helper->getPosition();
            $enabled = $helper->getEnabled();
            $markup = $helper->getMarkup();
            if ($enabled) {
                $block = $observer->getBlock();
                $thisClass = get_class($block);
                $content = $helper->getContent();
                $normalOutput = $observer->getTransport()->getHtml();
                $argBefore = null;
                $argAfter = null;
                if ($content == $thisClass) {
                    if ($markup == 'footer') {
                        if (Mage::registry('product')) {
                            $snipblock = $block->getLayout()->createBlock('snippets/product_footer')->toHtml();
                        } else {
                            $snipblock = $block->getLayout()->createBlock('snippets/category_footer')->toHtml();
                        }
                    }

                    if ($markup == 'visible') {
                        if (Mage::registry('product')) {
                            $snipblock = $block->getLayout()->createBlock('snippets/product_schema')->toHtml();
                        } else {
                            $snipblock = $block->getLayout()->createBlock('snippets/category_schema')->toHtml();
                        }
                    }

                    if ($position == 'after') {
                        $argAfter = $snipblock;
                    } else {
                        $argBefore = $snipblock;
                    }
                }

                $observer->getTransport()->setHtml($argBefore . $normalOutput . $argAfter);
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function addFullBreadcrumb(Varien_Event_Observer $observer)
    {
        if (!Mage::registry('current_category')) {
            $detailed = Mage::getStoreConfig('snippets/system/breadcrumbs_detailed');
            $breadcrumbs = Mage::getStoreConfig('snippets/system/breadcrumbs');
            $enabled = Mage::getStoreConfig('snippets/general/enabled');
            if ($detailed && $enabled && $breadcrumbs) {
                $product = $observer->getProduct();
                if ($categoryIds = $product->getCategoryIds()) {
                    $rootCategory = Mage::getModel('catalog/category')
                        ->load(Mage::app()->getStore()->getRootCategoryId());
                    $allCategories = Mage::getResourceModel('catalog/category_collection')
                        ->addIdFilter($product->getCategoryIds())
                        ->addAttributeToFilter('is_active', 1)
                        ->addFieldToFilter('path', array('like' => $rootCategory->getPath() . '/%'))
                        ->addAttributeToSelect('level', 'id')
                        ->getItems();

                    $allCats = array();
                    foreach ($allCategories as $cat) {
                        $allCats[$cat->getLevel()] = $cat->getId();
                    }

                    krsort($allCats);
                    $categoryId = reset($allCats);
                    $category = Mage::getModel('catalog/category')->load($categoryId);
                    if ($category->getId()) {
                        $product->setCategory($category);
                        Mage::register('current_category', $category);
                    }
                }
            }
        }

        return $this;
    }

}