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

class Ced_Jet_Model_System_Config_Source_Autoreject
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'outofstock',
                'label' => 'Product Out Of Stock',
            ),
            array(
                'value' => 'productdisabled',
                'label' => 'Product Disabled',
            ),
            array(
                'value' => 'notexist',
                'label' => 'Product Does not Exist',
            ),
        );
    }
}
