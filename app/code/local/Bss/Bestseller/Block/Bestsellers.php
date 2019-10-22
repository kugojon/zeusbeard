<?php

class Bss_Bestseller_Block_Bestsellers extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * @param int $limit
     * @return mixed
     */
    public function getBestsellerProducts($limit = 10)
    {
        if ($limit == "") $limit = 10;
        $storeId = (int)Mage::app()->getStore()->getId();

        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addStoreFilter()
            ->addPriceData()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->setPageSize($limit);

        $collection->getSelect()
            ->joinLeft(
                array('aggregation' => $collection->getResource()->getTable('sales/bestsellers_aggregated_monthly')),
                "e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId}",
                array('SUM(aggregation.qty_ordered) AS sold_quantity')
            )
            ->group('e.entity_id')
            ->order('sold_quantity DESC');

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $collection;
    }
}