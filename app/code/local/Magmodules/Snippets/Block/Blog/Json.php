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

class Magmodules_Snippets_Block_Blog_Json extends Mage_Core_Block_Template
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        if ($this->getSnippetsEnabled()) {
            $enable = Mage::getStoreConfig('snippets/blog/enable');
            $blogEnable = Mage::getStoreConfig('blog/blog/enabled');
            if ($enable && $blogEnable) {
                if ($_snippets = $this->getJsonBlogSnippets()) {
                    $this->setSnippets($_snippets);
                    $this->setTemplate('magmodules/snippets/blog/json.phtml');
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getJsonBlogSnippets()
    {
        return $this->helper('snippets')->getJsonBlogSnippets();
    }

    /**
     * @return mixed
     */
    public function getSnippetsEnabled()
    {
        return Mage::getStoreConfig('snippets/general/enabled');
    }

}