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

class Magmodules_Snippets_Block_Cms_Metatags extends Mage_Core_Block_Template
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        if ($this->getSnippetsEnabled()) {
            $metadata = Mage::getStoreConfig('snippets/metadata/cms_metadata');
            $twitter = Mage::getStoreConfig('snippets/metadata/cms_twitter');
            if ($metadata || $twitter) {
                $storeId = Mage::app()->getStore()->getStoreId();
                $cmsIdentifier = Mage::getSingleton('cms/page')->getIdentifier();
                $this->addData(
                    array(
                        'cache_lifetime' => 7200,
                        'cache_tags'     => array(
                            Mage_Cms_Model_Page::CACHE_TAG,
                            Magmodules_Snippets_Model_Snippets::CACHE_TAG
                        ),
                        'cache_key'      => $storeId . '-snippets-meta-cms-' . $cmsIdentifier,
                    )
                );
                $this->setTemplate('magmodules/snippets/cms/metatags.phtml');
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
    public function getCmsMetatags()
    {
        return $this->helper('snippets')->getCmsMetatags();
    }

}