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

class Magmodules_Snippets_Model_Source_Organization_Contacttype
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $type = array();
        $type[] = array('value' => 'customer service', 'label' => 'customer service');
        $type[] = array('value' => 'technical support', 'label' => 'technical support');
        $type[] = array('value' => 'billing support', 'label' => 'billing support');
        $type[] = array('value' => 'bill payment', 'label' => 'bill payment');
        $type[] = array('value' => 'sales', 'label' => 'sales');
        $type[] = array('value' => 'reservations', 'label' => 'reservations');
        $type[] = array('value' => 'credit card support', 'label' => 'credit card support');
        $type[] = array('value' => 'emergency', 'label' => 'emergency');
        $type[] = array('value' => 'baggage tracking', 'label' => 'baggage tracking');
        $type[] = array('value' => 'roadside assistance', 'label' => 'roadside assistance');
        $type[] = array('value' => 'package tracking', 'label' => 'package tracking');
        return $type;
    }

}