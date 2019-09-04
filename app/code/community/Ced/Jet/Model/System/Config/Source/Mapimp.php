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

 
class Ced_Jet_Model_System_Config_Source_Mapimp extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions($withEmpty = false)
    {
       $options = array();
       $options=array(
                 array('label' =>'','value' => ''),
                 array('label' =>'Jet member savings never applied to product','value' => '103'),
                 array('label' =>'Jet member savings on product only visible to logged in Jet members','value' =>'102'),
                 array('label' =>'no restrictions on product based pricing','value' =>'101'),
        );
        
        return $options;
    }
 
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
 
    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);
        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }

        return false;
    }
}
