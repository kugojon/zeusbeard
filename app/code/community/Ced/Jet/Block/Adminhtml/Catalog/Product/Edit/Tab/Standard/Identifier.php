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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml group price item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ced_Jet_Block_Adminhtml_Catalog_Product_Edit_Tab_Standard_Identifier
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{

    protected $_product;
    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->_product = Mage::registry('current_product');
        $this->setTemplate('ced/jet/standard/identifier.phtml');
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
                'label' => Mage::helper('catalog')->__('Add Identifier'),
                'onclick' => 'return standardIdentifier.addItem()',
                'class' => 'add'
                )
            );
        $button->setName('add_standard_identifier_item_button');

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
    public function getStandardIdentifiers()
    {
        $identifiers = array('UPC' => 'UPC', 'EAN' => 'EAN', 'ASIN' => 'ASIN',
            'ISBN-13' => 'ISBN-13', 'ISBN-10' => 'ISBN-10', 'GTIN-14' => 'GTIN-14');
        return $identifiers;
    }






    public function getStandardIdentifiersMapping()
    {
        $data = array();
        if($this->_product && $this->_product->getId()){
            $data = $this->_product->getData('standard_identifier');
        }else{
            $identifiers = $this->getStandardIdentifiers();
            foreach ($identifiers as $key=> $identifier){
                $data[] = array('identifier' =>$key, 'value' => '');
                break;
            }
        }

        return $data;
    }


}
