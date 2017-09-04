<?php
class Bss_CustomBundleItem_Model_Resource_Items_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->_init(
			'custombundleitem/items',
			'custombundleitem/items'
			);
	}
}