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

class Ced_Jet_Model_Jetshipping extends Mage_Shipping_Model_Shipping
{
    public function collectCarrierRates($carrierCode, $request) 
    {
        if (!$this -> _checkCarrierAvailability($carrierCode, $request)) {
            return $this;
        }

        return
        parent::collectCarrierRates($carrierCode, $request);
    }
    protected function _checkCarrierAvailability($carrierCode, $request = null) 
    {
        if ($carrierCode == 'shipjetcom') {
            if(Mage::getDesign()->getArea() == Mage_Core_Model_App_Area::AREA_ADMINHTML){
                return true;
            }else{
                return false;
            }
        }

        return true;
    }
}
