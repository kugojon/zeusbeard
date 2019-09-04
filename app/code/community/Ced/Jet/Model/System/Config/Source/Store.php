<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_Jet
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

class Ced_Jet_Model_System_Config_Source_Store
{
    public function toOptionArray()
    {
        
        $complete_opt = array();
        $webcall = Mage::app()->getWebsites();
        
          foreach ($webcall as $website) {
            foreach ($website->getGroups() as $group) {
             $stores = $group->getStores();
            
             foreach ($stores as $store) {
                 $arr = array();
                $arr['value'] = $store->getId();
                $arr['label'] = $store->getName();
                $complete_opt[]= $arr;
             }
            }
          }
         
        return $complete_opt;
        
    }
    
}
