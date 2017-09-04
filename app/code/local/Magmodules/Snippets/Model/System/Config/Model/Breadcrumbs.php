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

class Magmodules_Snippets_Model_System_Config_Model_Breadcrumbs extends Mage_Core_Model_Config_Data
{

    protected function _beforeSave()
    {
        $data = $this->getData();
        if (isset($data['groups']['system']['fields']['breadcrumbs_markup']['value'])) {
            $markup = $data['groups']['system']['fields']['breadcrumbs_markup']['value'];
            $breadcrumbs = $data['groups']['system']['fields']['breadcrumbs']['value'];
            if ($markup == 'json') {
                Mage::getModel('core/config')->saveConfig('snippets/system/breadcrumbs_overwrite', 0);
            } else {
                if ($breadcrumbs) {
                    Mage::getModel('core/config')->saveConfig('snippets/system/breadcrumbs_overwrite', 1);
                }
            }
        }

        parent::_beforeSave();
    }

    protected function _afterSave()
    {
        Mage::app()->cleanCache(Magmodules_Snippets_Model_Snippets::CACHE_TAG);
    }

}