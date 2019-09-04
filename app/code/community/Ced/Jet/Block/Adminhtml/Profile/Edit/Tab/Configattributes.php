<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml tier price item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ced_Jet_Block_Adminhtml_Profile_Edit_Tab_Configattributes
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{

    protected $_jetAttribute;
    protected  $_profile;
    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->_profile = Mage::registry('current_profile');
        $this->setTemplate('ced/jet/profile/configattributes.phtml');
    }


    /**
     * Prepare global layout
     * Add "Add tier" button to layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                'label' => Mage::helper('catalog')->__('Add Attribute'),
                'onclick' => 'return configAttributeControl.addItem()',
                'class' => 'add'
                )
            );
        $button->setName('add_tier_price_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }
    /**
     * Render HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Retrieve 'add group price item' button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }



    /**
     * Retrieve jet attributes
     *
     * @param int|null $groupId  return name by customer group id
     * @return array|string
     */
    public function getJetConfigAttributes()
    {
      
        $attributeCollections = Mage::getModel('jet/jetconfattribute')->getCollection();
        $configAttribute = array();
        foreach ($attributeCollections as $item) {
            $this->_jetAttribute[$item->getJetAttributeName()] = $item->getJetAttributeName();
            $temp = array();
            $temp['jet_attribute_name'] = $item->getJetAttributeName();
            $temp['magento_attribute_code'] = $item->getMagentoAttributeCode();
            $temp['jet_attribute_type'] = $item->getJetAttributeType();
            $configAttribute[$item->getJetAttributeName()] = $temp;
        }

        $this->_jetAttribute = $configAttribute;
        return $this->_jetAttribute;

    }


    /**
     * Retrieve magento attributes
     *
     * @param int|null $groupId  return name by customer group id
     * @return array|string
     */
    public function getMagentoAttributes()
    {
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFieldToFilter('is_configurable', 1)
            ->addFieldToFilter('frontend_input', array('in' => array('select', 'multiselect')));
        $magentoattributeCodeArray = array();
        foreach ($attributes as $attribute) {
            $magentoattributeCodeArray[$attribute->getAttributecode()] = $attribute->getAttributecode();
        }


        /*$attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFieldToFilter('frontend_input', ['in' => ['select', 'multiselect']])
            ->getItems();
        $mattributecode =
            $model->getMagentoAttributeCode()!=' ' ? $model->getMagentoAttributeCode() : '--please select--';
        if ($mattributecode == '--please select--') {
            $magentoattributeCodeArray[''] = $mattributecode;
        } else {
            $magentoattributeCodeArray[$mattributecode] = $mattributecode;
        }*/

        foreach ($attributes as $attribute) {
            $magentoattributeCodeArray[$attribute->getAttributecode()] = $attribute->getAttributecode();
        }





        return $magentoattributeCodeArray;
    }


    public function getJetAttributeValuesMapping()
    {
        $data = array();
        if($this->_profile && $this->_profile->getId()>0){
            $data = json_decode($this->_profile->getProfileAttributeMapping(), true);
            $data = $data['variant_attributes'];
        }else{
            if(!$this->_jetAttribute)
                $this->_jetAttribute = $this->getJetConfigAttributes();


            foreach($this->_jetAttribute as $key => $value){
                if(isset($value['magento_attribute_code']) && $value['magento_attribute_code']!=""){
                    $data[] = $value;
                }
            }
        }

        return $data;
    }


}
