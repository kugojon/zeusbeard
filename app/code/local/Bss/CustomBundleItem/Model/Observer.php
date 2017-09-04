<?php
class Bss_CustomBundleItem_Model_Observer
{
  public function saveBundleOption(Varien_Event_Observer $observer)
  {
    $data = Mage::app()->getRequest()->getParam('bundle_options');
    $product = $observer->getEvent()->getProduct();
    $store_id = (int)$product->getData('store_id');

    $hiddenArray = Mage::getModel('custombundleitem/items')->getCollection()
    ->addFieldToFilter('product_id',$product->getId());
    // ->addFieldToFilter('store_id',$store_id);
    foreach ($hiddenArray as $val) {
      $val->delete();
    }


    $typeInstance = $product->getTypeInstance(true);
	if($product->getTypeId() == 'bundle'){
		$selectionCollection = $typeInstance->getSelectionsCollection(
		  $typeInstance->getOptionsIds($product), $product
		  );

		$optionCollection = $typeInstance->getOptionsCollection($product);

		$_options = $optionCollection->appendSelections($selectionCollection, false,
		  Mage::helper('catalog/product')->getSkipSaleableCheck()
		  );

		$option = array();
		foreach ($_options as $key => $value) {
		  $option[$value->getData('default_title')] = $value->getData('option_id');
		}

		if(count($data) > 0) {
		  foreach ($data as $item) {
			$model = Mage::getModel('custombundleitem/items')
			->setStoreId(o)
			->setProductId($product->getId())
			->setOptionId($option[$item['title']])
			->setIsHidden($item['ishidden'])
			->save();
		  }
		}
	}
  }
}