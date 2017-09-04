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

class Magmodules_Snippets_Model_Source_Localbusiness_Days
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $days = array();
        $days[] = array('value' => 'Monday', 'label' => Mage::helper('adminhtml')->__('Monday'));
        $days[] = array('value' => 'Tuesday', 'label' => Mage::helper('adminhtml')->__('Tuesday'));
        $days[] = array('value' => 'Wednesday', 'label' => Mage::helper('adminhtml')->__('Wednesday'));
        $days[] = array('value' => 'Thursday', 'label' => Mage::helper('adminhtml')->__('Thursday'));
        $days[] = array('value' => 'Friday', 'label' => Mage::helper('adminhtml')->__('Friday'));
        $days[] = array('value' => 'Saturday', 'label' => Mage::helper('adminhtml')->__('Saturday'));
        $days[] = array('value' => 'Sunday', 'label' => Mage::helper('adminhtml')->__('Sunday'));
        return $days;
    }

}

