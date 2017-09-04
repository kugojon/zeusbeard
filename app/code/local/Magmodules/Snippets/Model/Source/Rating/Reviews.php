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

class Magmodules_Snippets_Model_Source_Rating_Reviews
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $reviewtype = array();
        $reviewtype[] = array(
            'value' => '',
            'label' => Mage::helper('snippets')->__('-- none')
        );
        $reviewtype[] = array(
            'value' => 'shopreview',
            'label' => Mage::helper('snippets')->__('Magmodules: Shopreview')
        );
        $reviewtype[] = array(
            'value' => 'feedbackcompany',
            'label' => Mage::helper('snippets')->__('Magmodules: The Feedback Company')
        );
        $reviewtype[] = array(
            'value' => 'webwinkelkeur',
            'label' => Mage::helper('snippets')->__('Magmodules: Webwinkelkeur')
        );
        $reviewtype[] = array(
            'value' => 'trustpilot',
            'label' => Mage::helper('snippets')->__('Magmodules: Trustpilot')
        );
        $reviewtype[] = array(
            'value' => 'kiyoh',
            'label' => Mage::helper('snippets')->__('Magmodules: KiyOh')
        );
        return $reviewtype;
    }

}