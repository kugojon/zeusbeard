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

class Ced_Jet_Model_Source_Cronchunk
{
    public function toOptionArray()
    {


        $_options = array(
            array(
                'label' => '100',
                'value' => '100'
            ),
            array(
                'label' =>'500',
                'value' =>'500'
            ),
            array(
                'label' => '1000',
                'value' =>'1000'
            ),
            array(
                'label' => '1500',
                'value' =>'1500'
            ),
            array(
                'label' => '2000',
                'value' =>'2000'
            ),
            array(
                'label' => '5000',
                'value' =>'5000'
            ),
        );
        return $_options;
    }

}