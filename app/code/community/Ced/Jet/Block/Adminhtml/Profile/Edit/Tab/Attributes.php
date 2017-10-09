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
class Ced_Jet_Block_Adminhtml_Profile_Edit_Tab_Attributes
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{

    protected $_jetAttribute = array() ;
    protected  $_profile;
    /**
     * Initialize block
     */
    public function __construct()
    {



        $this->_profile = Mage::registry('current_profile');
        $this->setTemplate('ced/jet/profile/attributes.phtml');
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
            ->setData(array(
                'label' => Mage::helper('catalog')->__('Add Attribute'),
                'onclick' => 'return tierPriceControl.addItem()',
                'class' => 'add'
            ));
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
    public function getJetAttributes()
    {
        $nodeId = "";

        if($this->_profile && $this->_profile->getId()>0){
            $nodeId = $this->_profile->getData('node_id');
        } else{
            $nodeId = $this->getNodeId();
        }

        $requiredAttribute = array();

        //$nodeId = 2000066;
        $attributes = Mage::helper('jet')->getNodeAttributes($nodeId);


        if(isset($attributes['attributes']))
            $this->_jetAttribute['jet_attribute'] = $attributes['attributes'];

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
            ->addFieldToFilter('is_user_defined', 1)
            ->getItems();
        $mattributecode = '--please select--';

        $simpleMagentoAttributeCodeArray[''] = $mattributecode;

        foreach ($attributes as $attribute){
            $simpleMagentoAttributeCodeArray[$attribute->getAttributecode()] = $attribute->getAttributecode();
        }

        $systemAttribute = array('exclude_from_fee_adjust', 'jet_product_status', 'manufacturer',
            'map_implementation', 'product_tax_code', 'prop_65', 'ships_alone', 'newegg_condition', 'newegg_shipping', 'walmart_productid_type',
            'walmart_product_status');

        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFieldToFilter('attribute_code', array('nin' => $systemAttribute))
            ->addFieldToFilter('is_configurable', 1)
            ->addFieldToFilter('is_global', 1)

            ->addFieldToFilter('frontend_input', ['in' => ['select', 'boolean']]);
        $variantMagentoattributeCodeArray = array();
        foreach ($attributes as $attribute) {
            $variantMagentoattributeCodeArray[$attribute->getAttributecode()] = $attribute->getAttributecode();
        }



        $magentoattributeCodeArray = [];

        $magentoattributeCodeArray[] = array(
            'label' => Mage::helper('widget')->__('Simple Attributes'),
            'value' => $simpleMagentoAttributeCodeArray
        );


        $magentoattributeCodeArray[] = array(
            'label' => Mage::helper('widget')->__('Variant Attributes'),
            'value' => $variantMagentoattributeCodeArray
        );
        return $magentoattributeCodeArray;
    }


    public function getJetAttributeValuesMapping(){
        $data = array();
        if($this->_profile && $this->_profile->getId()>0){
            $data = json_decode($this->_profile->getProfileAttributeMapping(), true);
              $data = $data;
        }else{
            if(!$this->_jetAttribute)
                $this->_jetAttribute = $this->getJetAttributes();

                $data = $this->_jetAttribute;
        }
        return $data;
    }


}
