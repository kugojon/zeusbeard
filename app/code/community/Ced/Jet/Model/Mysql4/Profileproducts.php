<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_Jet
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

class Ced_Jet_Model_Mysql4_Profileproducts extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_productsTable;


    /**
     *
     * Initializing Resource Model
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     */
    protected function _construct() 
    {
        $this->_init('jet/profileproducts', 'id');

        $this->_productsTable = $this->getTable('catalog/product');
        $this->_profileProductsTable = $this->getTable('jet/profileproducts');

    }

    /**
     *
     * @param Mage_Core_Model_Abstract $group
     * @see Mage_Core_Model_Resource_Db_Abstract::_beforeSave()
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $profile)
    {
        if ($profile->getId() == '') {
            if ($profile->getIdFieldName()) {
                $profile->unsetData($profile->getIdFieldName());
            } else {
                $profile->unsetData('id');
            }
        }


        $profile->setProfileName($profile->getName());
        return $this;
    }
    /**
     *
     * @param Mage_Core_Model_Abstract $group
     * @see Mage_Core_Model_Resource_Db_Abstract::_afterSave()
     */
    protected function _afterSave(Mage_Core_Model_Abstract $profile)
    {
        //$this->_updateGroupVendorsAcl($group);
        Mage::app()->getCache()->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            array(Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS)
        );
        return $this;
    }



    /**
     *
     *
     * @param Mage_Core_Model_Abstract $group
     * @return multitype:
     */
    public function getProfileProducts($profileId)
    {
        $read     = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable(), array('product_id'))->where("(profile_id = '{$profileId}' ) AND product_id > 0");
        return $read->fetchCol($select);
    }


    public function deleteFromProfile($productId)
    {

        if ($productId <= 0) {
            return $this;
        }

        //$vendorGroup = Mage::getModel('csgroup/group')->loadByField('group_code',$vendor->getGroup());

        $dbh = $this->_getWriteAdapter();
        $condition = "{$this->getTable('jet/profileproducts')}.product_id = " . $dbh->quote($productId);
            //. " AND {$this->getTable('jet/profileproducts')}.profile_id = " . $dbh->quote($profileId);
        $dbh->delete($this->getTable('jet/profileproducts'), $condition);
        return $this;
    }


    public function deleteProductsFromProfile($productIds)
    {

        if (empty($productIds) or !is_array($productIds) or count($productIds) == 0) {
            return $this;
        }

        $productIds = array_unique($productIds);
        $dbh = $this->_getWriteAdapter();
        $condition =
            "{$this->getTable('jet/profileproducts')}.product_id IN (" .implode(',', $productIds).")";
        $dbh->delete($this->getTable('jet/profileproducts'), $condition);
        return $this;
    }

    /**
     * Add products to a profile
     * @param $productIds
     * @param $profileId
     * @return $this
     */
    public function addProductsToProfile($productIds, $profileId)
    {

        if (empty($productIds) or !is_array($productIds) or empty($profileId) or count($productIds) == 0) {
            return $this;
        }

        $productIds = array_unique($productIds);
        $adapter = $this->_getWriteAdapter();
        $data = array();
        foreach ($productIds as $productId) {
            $data[] = array(
                'profile_id' => (int)$profileId,
                'product_id' => (int)$productId
            );
        }

        $adapter->insertMultiple($this->_profileProductsTable, $data);
        return $this;
    }

    public function profileProductExists($productId, $profileId)
    {
        if ($productId > 0) {
            $profileTable = $this->getTable('jet/profileproducts');

            $productProfile = Mage::getModel('jet/profileproducts')->loadByField('profile_id', $profileId);
            if($productProfile && $productProfile->getId())
            {
                $dbh    = $this->_getReadAdapter();
                $select = $dbh->select()->from($profileTable)
                    ->where("product_id = {$productId} AND profile_id = {$profileId}");
                return $dbh->fetchCol($select);
            }
            else
            {
                return array();
            }
        } else {
            return array();
        }
    }


}
