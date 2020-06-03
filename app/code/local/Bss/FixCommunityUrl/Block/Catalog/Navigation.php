<?php
class Bss_FixCommunityUrl_Block_Catalog_Navigation extends Mage_Catalog_Block_Navigation
{
    public function getCategoryUrl($category)
    {
        $url_redirect = Mage::getModel('catalog/category')->load($category->getId())->getData('url_redirect');
        if($url_redirect) {
            return $url_redirect;
        }

        if ($category instanceof Mage_Catalog_Model_Category) {
            $url = $category->getUrl();
        } else {
            $url = $this->_getCategoryInstance()
                ->setData($category->getData())
                ->getUrl();
        }

        return $url;
    }
}
			