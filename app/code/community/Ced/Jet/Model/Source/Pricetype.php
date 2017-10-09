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

class Ced_Jet_Model_Source_Pricetype
{
	public function toOptionArray()
    {
			
        
            $_options = array(
                array(
                    'label' => 'Default Magento Price',
                    'value' => 'final_price'
                ),
             	array(
                    'label' =>'Add % Percent on Magento Price',
                    'value' =>'plus_percent'
                ),
				array(
                    'label' => 'Off % Percent on Magento Price',
                    'value' =>'off_percent'
                ),
				array(
                    'label' => 'Custom Fixed Price',
                    'value' =>'fixed'
                ),
			);
        return $_options;
    }
	
}