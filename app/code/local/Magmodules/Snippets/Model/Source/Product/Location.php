<?php
/**
 * Magmodules.eu - http://www.magmodules.eu.
 *
 * NOTICE OF LICENSE
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.magmodules.eu/MM-LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magmodules.eu so we can send you a copy immediately.
 *
 * @category      Magmodules
 * @package       Magmodules_Richsnippets
 * @author        Magmodules <info@magmodules.eu>
 * @copyright     Copyright (c) 2017 (http://www.magmodules.eu)
 * @license       https://www.magmodules.eu/terms.html  Single Service License
 */

class Magmodules_Snippets_Model_Source_Product_Location
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $location = array();
        $location[] = array(
            'value' => '',
            'label' => Mage::helper('snippets')->__('-- Manual')
        );
        $location[] = array(
            'value' => 'Mage_Catalog_Block_Product_View_Description',
            'label' => Mage::helper('snippets')->__('Product Description Block')
        );
        $location[] = array(
            'value' => 'Mage_Catalog_Block_Product_View_Attributes',
            'label' => Mage::helper('snippets')->__('Additional Information Block')
        );
        $location[] = array(
            'value' => 'Mage_Review_Block_Product_View_List',
            'label' => Mage::helper('snippets')->__('Product Review Block (Modern Theme)')
        );
        $location[] = array(
            'value' => 'advanced',
            'label' => Mage::helper('snippets')->__('Advanced: Custom Layout Update Handle')
        );
        return $location;
    }

}