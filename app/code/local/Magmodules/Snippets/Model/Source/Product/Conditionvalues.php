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

class Magmodules_Snippets_Model_Source_Product_Conditionvalues
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $type = array();
        $type[] = array('value' => 'new', 'label' => Mage::helper('snippets')->__('New'));
        $type[] = array('value' => 'refurbished', 'label' => Mage::helper('snippets')->__('Refurbished'));
        $type[] = array('value' => 'used', 'label' => Mage::helper('snippets')->__('Used'));
        $type[] = array('value' => 'damaged', 'label' => Mage::helper('snippets')->__('Damaged'));
        return $type;
    }

}