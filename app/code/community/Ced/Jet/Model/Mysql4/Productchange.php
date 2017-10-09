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

class Ced_Jet_Model_Mysql4_Productchange extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     *
     * Initializing Resource Model
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     */
    protected function _construct() {
        $this->_init('jet/productchange', 'id');
    }


    public function deleteFromProductChange($productIds)
    {

        if ( count($productIds)<=0) {
            return $this;
        }

        //$vendorGroup = Mage::getModel('csgroup/group')->loadByField('group_code',$vendor->getGroup());

        $dbh = $this->_getWriteAdapter();
        $condition = "{$this->getTable('jet/productchange')}.product_id in (" . $dbh->quote($productIds).")";
        //. " AND {$this->getTable('jet/profileproducts')}.profile_id = " . $dbh->quote($profileId);
        $dbh->delete($this->getTable('jet/productchange'), $condition);
        return $this;
    }

}
