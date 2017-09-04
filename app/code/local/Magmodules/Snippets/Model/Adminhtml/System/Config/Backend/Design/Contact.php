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

class Magmodules_Snippets_Model_Adminhtml_System_Config_Backend_Design_Contact
    extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{

    /**
     *
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
            if (!empty($value)) {
                $keys = array();

                for ($i = 0; $i < count($value); $i++) {
                    $keys[] = 'availability_' . uniqid();
                }

                foreach ($value as $key => $field) {
                    $value[$key]['telephone'] = $field['telephone'];
                    $value[$key]['contacttype'] = $field['contacttype'];
                    $value[$key]['contactoption'] = $field['contactoption'];
                    $value[$key]['area'] = $field['area'];
                    $value[$key]['languages'] = $field['languages'];
                }

                $value = array_combine($keys, array_values($value));
            }
        }

        $this->setValue($value);
        parent::_beforeSave();
    }

}
