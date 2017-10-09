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

class Ced_Jet_Model_Source_Selprice
{
    public function toOptionArray()
    {


        $_options = array(
            array(
                'label' => 'Default Magento Price',
                'value' => 'final_price'
            ),
            array(
                'label' =>'Increase By Fixed Price',
                'value' =>'plus_fixed'
            ),
            array(
                'label' => 'Increase By Fixed Percentage',
                'value' =>'plus_per'
            ),
            array(
                'label' => 'Decrease By Fixed Price',
                'value' =>'min_fixed'
            ),
            array(
                'label' => 'Decrease By Fixed Percentage',
                'value' =>'min_per'
            ),
            array(
                'label' => 'set individually for each product',
                'value' =>'differ'
            ),
        );
        return $_options;
    }

}