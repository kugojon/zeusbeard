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

class Magmodules_Snippets_Block_Product_Metatags extends Mage_Core_Block_Template
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        if ($this->getSnippetsEnabled()) {
            $twitter = Mage::getStoreConfig('snippets/products/twitter');
            $pinterest = Mage::getStoreConfig('snippets/products/pinterest');
            if ($twitter || $pinterest) {
                if ($_snippets = $this->getProductMetatags()) {
                    $storeId = Mage::app()->getStore()->getStoreId();
                    if (is_object(Mage::registry('current_product'))) {
                        $pId = Mage::registry('current_product')->getId();
                        $this->addData(
                            array(
                                'cache_lifetime' => 7200,
                                'cache_tags'     => array(
                                    Mage_Catalog_Model_Category::CACHE_TAG,
                                    Magmodules_Snippets_Model_Snippets::CACHE_TAG
                                ),
                                'cache_key'      => $storeId . 'snippets-meta-p-' . $pId,
                            )
                        );
                    }

                    $this->setSnippets($_snippets);
                    $this->setTemplate('magmodules/snippets/catalog/product/metatags.phtml');
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getSnippetsEnabled()
    {
        return Mage::getStoreConfig('snippets/general/enabled');
    }

    /**
     * @return mixed
     */
    public function getProductMetatags()
    {
        return $this->helper('snippets')->getProductMetatags();
    }

}