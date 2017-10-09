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

 
class Ced_Jet_Model_System_Config_Source_Caution extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions($withEmpty = false)
    {
       $options = array();
 		$options[] = array(
				'label' =>'no warning applicable',
				'value' => 'no warning applicable',
		);
		$options[] = array(
				'label' =>'choking hazard small parts',
				'value' => 'choking hazard small parts',
		);
		$options[] = array(
				'label' =>'choking hazard is a small ball',
				'value' => 'choking hazard is a small ball',
		);
		$options[] = array(
				'label' =>'choking hazard is a marble',
				'value' => 'choking hazard is a marble',
		);
		$options[] = array(
				'label' =>'choking hazard contains a small ball',
				'value' => 'choking hazard contains a small ball',
		);
		$options[] = array(
				'label' =>'choking hazard contains a marble',
				'value' => 'choking hazard contains a marble',
		);
		$options[] = array(
				'label' =>'choking hazard balloon',
				'value' => 'choking hazard balloon',
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
?>