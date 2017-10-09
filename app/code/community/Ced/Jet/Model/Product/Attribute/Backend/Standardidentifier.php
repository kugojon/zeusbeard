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


/**
 * Catalog product group price backend attribute model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ced_Jet_Model_Product_Attribute_Backend_Standardidentifier
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Tierprice
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('catalog/product_attribute_backend_groupprice');
    }

    /**
     * Error message when duplicates
     *
     * @return string
     */
    protected function _getDuplicateErrorMessage()
    {
        return Mage::helper('jet')->__('Duplicate Standard Identifier.');
    }



    /**
     * Website currency codes and rates
     *
     * @var array
     */
    protected $_rates;




    /**
     * Get additional unique fields
     *
     * @param array $objectArray
     * @return array
     */
    protected function _getAdditionalUniqueFields($objectArray)
    {
        return array();
    }

    /**
     * Whether group price value fixed or percent of original price
     *
     * @param Mage_Catalog_Model_Product_Type_Price $priceObject
     * @return bool
     */
    protected function _isPriceFixed($priceObject)
    {
        return $priceObject->isGroupPriceFixed();
    }

    /**
     * Validate group price data
     *
     * @param Mage_Catalog_Model_Product $object
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function validate($object)
    {
        $attribute = $this->getAttribute();
        $identifierRows = $object->getData($attribute->getName());
        if (empty($identifierRows)) {
            return true;
        }

        // validate per website
        $duplicates = array();
        foreach ($identifierRows as $identifierRow) {
            if (!empty($identifierRow['delete'])) {
                continue;
            }


            $compare = join('-', array_merge(
                array($identifierRow['identifier']),
                $this->_getAdditionalUniqueFields($identifierRow)
            ));
            if (isset($duplicates[$compare])) {
                Mage::throwException($this->_getDuplicateErrorMessage());
            }
            $duplicates[$compare] = true;
        }
        return true;
    }

    /**
     * Prepare group prices data for website
     *
     * @param array $priceData
     * @param string $productTypeId
     * @param int $websiteId
     * @return array
     */
    public function preparePriceData(array $priceData, $productTypeId, $websiteId)
    {
        $rates  = $this->_getWebsiteCurrencyRates();
        $data   = array();
        $price  = Mage::getSingleton('catalog/product_type')->priceFactory($productTypeId);
        foreach ($priceData as $v) {
            $key = join('-', array_merge(array($v['cust_group']), $this->_getAdditionalUniqueFields($v)));
            if ($v['website_id'] == $websiteId) {
                $data[$key] = $v;
                $data[$key]['website_price'] = $v['price'];
            } else if ($v['website_id'] == 0 && !isset($data[$key])) {
                $data[$key] = $v;
                $data[$key]['website_id'] = $websiteId;
                if ($this->_isPriceFixed($price)) {
                    $data[$key]['price'] = $v['price'] * $rates[$websiteId]['rate'];
                    $data[$key]['website_price'] = $v['price'] * $rates[$websiteId]['rate'];
                }
            }
        }

        return $data;
    }

    /**
     * Assign group prices to product data
     *
     * @param Mage_Catalog_Model_Product $object
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract
     */
    public function afterLoad($object)
    {
        $standardIdentifier = $object->getData( $this->getAttribute()->getName());
        $data = array();
        if($standardIdentifier!= NULL && $standardIdentifier!="")
            $data = json_decode($standardIdentifier, true);

        $object->setData($this->getAttribute()->getName(), $data);
        $object->setOrigData($this->getAttribute()->getName(), $data);

        $valueChangedKey = $this->getAttribute()->getName() . '_changed';
        $object->setOrigData($valueChangedKey, 0);
        $object->setData($valueChangedKey, 0);

        return $this;
    }

    /**
     * Before save method
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $identfierRows = $object->getData($this->getAttribute()->getName());
        $data = array();
        foreach($identfierRows as $row){
            if(isset($row['delete']) && $row['delete']==1)
                continue;
            $data[] = $row;
        }
        $object->setData($attrCode, json_encode($data));
        return $this;
    }
}
