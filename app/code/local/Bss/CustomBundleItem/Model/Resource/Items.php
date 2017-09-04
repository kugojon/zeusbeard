<?php
class Bss_CustomBundleItem_Model_Resource_Items extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
     
        $this->_init('custombundleitem/items', 'id');
    }
}