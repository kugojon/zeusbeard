<?php
require_once('Mage/Bundle/Block/Adminhtml/Catalog/Product/Edit/Tab/Bundle/Option.php');
class Bss_CustomBundleItem_Block_Option extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option
{
	public function __construct()
	{
		$this->setTemplate('bss/custombundleitem/option.phtml');
		$this->setCanReadPrice(true);
		$this->setCanEditPrice(true);
	}

	public function getOptions()
	{
		if (!$this->_options) {
			$this->getProduct()->getTypeInstance(true)->setStoreFilter($this->getProduct()->getStoreId(),
				$this->getProduct());

			$optionCollection = $this->getProduct()->getTypeInstance(true)->getOptionsCollection($this->getProduct());

			$selectionCollection = $this->getProduct()->getTypeInstance(true)->getSelectionsCollection(
				$this->getProduct()->getTypeInstance(true)->getOptionsIds($this->getProduct()),
				$this->getProduct()
				);

			$this->_options = $optionCollection->appendSelections($selectionCollection);

			$data = array();
			$hiddenArray = Mage::getModel('custombundleitem/items')->getCollection()
			->addFieldToFilter('product_id',$this->getProduct()->getId());
			// ->addFieldToFilter('store_id',$this->getProduct()->getStoreId());
			if(count($hiddenArray) > 0) {
				$ishidden = 1;
				foreach ($hiddenArray as $val) {
					$data[$val->getOptionId()] = $val->getIsHidden(); 
				}
			}else {
				$ishidden = 0;
			}
			foreach ($this->_options as $option) {
                    //gets each option's id
				$option_id = $option->getData('option_id');
                   //loop through module collection
				if($ishidden == 1) {
					$option->addData(array('ishidden'=> $data[$option_id]));
				}else {
					$option->addData(array('ishidden'=> 0));
				}
			}

			if ($this->getCanReadPrice() === false) {
				foreach ($this->_options as $option) {
					if ($option->getSelections()) {
						foreach ($option->getSelections() as $selection) {
							$selection->setCanReadPrice($this->getCanReadPrice());
							$selection->setCanEditPrice($this->getCanEditPrice());
						}
					}
				}
			}
		}
		return $this->_options;
	}

	public function getHiddenSelectHtml()
	{
		$array_reverse = array_reverse(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray());
		$select = $this->getLayout()->createBlock('adminhtml/html_select')
		->setData(array(
			'id' => $this->getFieldId().'_{{index}}_ishidden',
			'class' => 'select'
			))
		->setName($this->getFieldName().'[{{index}}][ishidden]')
		->setOptions($array_reverse);

		return $select->getHtml();
	}
}