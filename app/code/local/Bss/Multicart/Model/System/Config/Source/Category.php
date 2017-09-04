<?php
/**
* BSS Commerce Co.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://bsscommerce.com/Bss-Commerce-License.txt
*
* =================================================================
*                 MAGENTO EDITION USAGE NOTICE
* =================================================================
* This package designed for Magento COMMUNITY edition
* BSS Commerce does not guarantee correct work of this extension
* on any other Magento edition except Magento COMMUNITY edition.
* BSS Commerce does not provide extension support in case of
* incorrect edition usage.
* =================================================================
*
* @category   BSS
* @package    Bss_AddMultipleProducts
* @author     Extension Team
* @copyright  Copyright (c) 2014-2016 BSS Commerce Co. ( http://bsscommerce.com )
* @license    http://bsscommerce.com/Bss-Commerce-License.txt
*/

class Bss_Multicart_Model_System_Config_Source_Category 
{

    public function toOptionArray()
    {
 
        $oCategoryCollection = Mage::getResourceModel('catalog/category_collection');
 
        $oCategoryCollection->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active')
            ->addAttributeToSelect('parent_id')
            ->setStoreId($iStoreId)
            ->addFieldToFilter('parent_id', array('gt' => 0))
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('level', array('gteq' => 1))
            ->addAttributeToSort('path', 'asc');
        $aOptions = array();
        $aOptions[] = array(
                'label' => Mage::helper('adminhtml')->__('All Category (*)'),
                'value' => 'all'
            );
        foreach ($oCategoryCollection as $oCategory) {
            $sLabel = " {$oCategory->getName()} (ID: {$oCategory->getId()})";
            $iPadWidth = ($oCategory->getLevel() - 1) * 3 + strlen($sLabel);
            $sLabel = str_pad($sLabel, $iPadWidth, '---', STR_PAD_LEFT);
 
            $aOptions[] = array(
                'label' => $sLabel,
                'value' => $oCategory->getId()
            );
        }
 
        // return $aOptions;
        return array(array(
                'label' => '-- Please Select a Category --',
                'value' => $aOptions,
            ),
        );
    }

}