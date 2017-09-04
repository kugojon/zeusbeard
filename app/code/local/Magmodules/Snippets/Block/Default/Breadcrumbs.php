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

class Magmodules_Snippets_Block_Default_Breadcrumbs extends Mage_Catalog_Block_Breadcrumbs
{

    /**
     * @return bool|Mage_Catalog_Block_Breadcrumbs
     */
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $enabled = $this->getSnippetsEnabled();
            $breadcrumbs = $this->getBreadcrumbsEnabled();
            if ($enabled && $breadcrumbs) {
                $homeTitle = Mage::helper('snippets')->getFirstBreadcrumbTitle(Mage::helper('catalog')->__('Home'));
                $breadcrumbsBlock->addCrumb(
                    'home', array(
                        'label' => $homeTitle,
                        'title' => Mage::helper('catalog')->__('Go to Home Page'),
                        'link'  => Mage::getBaseUrl()
                    )
                );
                $title = array();
                $path = Mage::helper('catalog')->getBreadcrumbPath();
                foreach ($path as $name => $breadcrumb) {
                    $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                    $title[] = $breadcrumb['label'];
                }

                if ($headBlock = $this->getLayout()->getBlock('head')) {
                    $headBlock->setTitle(join($this->getTitleSeparator(), array_reverse($title)));
                }
            } else {
                return parent::_prepareLayout();
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getSnippetsEnabled()
    {
        return Mage::getStoreConfig('snippets/general/enabled');
    }

    /**
     * @return bool
     */
    public function getBreadcrumbsEnabled()
    {
        $enabled = Mage::getStoreConfig('snippets/system/breadcrumbs');
        $markup = Mage::getStoreConfig('snippets/system/breadcrumbs_markup');
        if ($enabled && ($markup == 'json')) {
            return true;
        }

        return false;
    }

}