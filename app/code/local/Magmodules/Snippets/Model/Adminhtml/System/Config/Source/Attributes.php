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

class Magmodules_Snippets_Model_Adminhtml_System_Config_Source_Attributes
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $att = array();
        $att[] = array('value' => 'brand', 'label' => 'Brand');
        $att[] = array('value' => 'color', 'label' => 'Color');
        $att[] = array('value' => 'sku', 'label' => 'SKU');
        $att[] = array('value' => 'model', 'label' => 'Model');
        $att[] = array('value' => 'gtin8', 'label' => 'GTIN-8');
        $att[] = array('value' => 'gtin12', 'label' => 'GTIN-12');
        $att[] = array('value' => 'gtin13', 'label' => 'GTIN-13');
        $att[] = array('value' => 'gtin14', 'label' => 'GTIN-14');
        $att[] = array('value' => 'mpn', 'label' => 'MPN');
        $att[] = array('value' => 'isbn', 'label' => 'ISBN');
        return $att;
    }

} 