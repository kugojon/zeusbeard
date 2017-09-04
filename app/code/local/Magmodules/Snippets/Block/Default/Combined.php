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

class Magmodules_Snippets_Block_Default_Combined extends Mage_Core_Block_Template
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        if ($this->getSnippetsEnabled()) {
            $snippets = $this->helper('snippets')->getWebsiteSnippets();
            $organization = $this->helper('snippets')->getOrganizationSnippets();
            $localBusiness = $this->helper('snippets')->getLocalBusinessSnippets();
            $webpage = $this->helper('snippets')->getWebPageSnippets();
            if ($snippets || $organization || $localBusiness || $webpage) {
                $storeId = Mage::app()->getStore()->getStoreId();
                if (Mage::app()->getFrontController()->getAction()->getFullActionName() == 'cms_index_index') {
                    $cacheKey = $storeId . '-snippets-combined-home';
                } else {
                    $cacheKey = $storeId . '-snippets-combined';
                }

                $this->addData(
                    array(
                        'cache_lifetime' => 7200,
                        'cache_tags'     => array(
                            Mage_Cms_Model_Block::CACHE_TAG,
                            Magmodules_Snippets_Model_Snippets::CACHE_TAG
                        ),
                        'cache_key'      => $cacheKey,
                    )
                );
                $this->setWebsiteSnippets($snippets);
                $this->setOrganizationSnippets($organization);
                $this->setLocalBusinessSnippets($localBusiness);
                $this->setWebPageSnippets($webpage);
                $this->setTemplate('magmodules/snippets/page/combined.phtml');
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

}