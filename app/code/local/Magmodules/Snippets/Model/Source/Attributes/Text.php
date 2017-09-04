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

class Magmodules_Snippets_Model_Source_Attributes_Text
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getEntityTypeId();
        $attributes = Mage::getModel('eav/entity_attribute')->getCollection()
            ->addFilter('entity_type_id', $entityTypeId)
            ->setOrder('attribute_code', 'ASC');
        $backendTypes = array('text', 'varchar');

        foreach ($attributes as $attribute) {
            if (in_array($attribute->getBackendType(), $backendTypes)) {
                if ($attribute->getFrontendLabel()) {
                    $options[] = array(
                        'value' => $attribute->getAttributeCode(),
                        'label' => str_replace("'", '', $attribute->getFrontendLabel())
                    );
                }
            }
        }

        return $options;
    }

}