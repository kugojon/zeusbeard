<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category   Ced
 * @package    Ced_CsRma
 * @author       CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright  Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Ced_Jet_Block_Adminhtml_System_Config_Field_Variantattribute extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_regionRenderer;
    protected $_methodRenderer;
    public function __construct()
    {

        parent::__construct();
        $this->setTemplate('ced/jet/system/config/form/field/array.phtml');
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {

        $this->addColumn(
            'magento_attribute_code', array(
            'label' => Mage::helper('jet')->__('Magento Attribute'),
            // 'renderer' => $this->_getMagentoAttributeRenderer(),
            'style' => 'width:150px',
            )
        );
        $this->addColumn(
            'jet_attribute_id', array(
            'label' => Mage::helper('jet')->__('Jet Attribute'),
            'renderer' => $this->_getJetAttributeRenderer(),
            'style' => 'width:60px',
            )
        );
        $this->_addAfter = false;
       // $this->_addButtonLabel = Mage::helper('jet')->__('Add Rules');
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {

        $row->setData(
            'option_extra_attr_' . $this->_getJetAttributeRenderer()
                ->calcOptionHash($row->getData('jet_attribute_id')),
            'selected="selected"'
        );


        $row->setData(
            'option_extra_attr_' . $this->_getMagentoAttributeRenderer()
                ->calcOptionHash($row->getData('magento_attribute_code')),
            'selected="selected"'
        );
    }




    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getJetAttributeRenderer()
    {
        if (!$this->_regionRenderer) {
            $this->_regionRenderer = $this->getLayout()->createBlock(
                'jet/adminhtml_system_config_field_jetattribute', '',
                array('is_render_to_js_template' => true)
            );
            $this->_regionRenderer->setClass('input-text required-entry');
            $this->_regionRenderer->setExtraParams('style="width:120px"');
        }

        return $this->_regionRenderer;
    }


    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getMagentoAttributeRenderer()
    {
        if (!$this->_methodRenderer) {
            $this->_methodRenderer = $this->getLayout()->createBlock(
                'jet/adminhtml_system_config_field_magentoattribute', '',
                array('is_render_to_js_template' => true)
            );
            $this->_methodRenderer->setClass('customer_group_select');
            $this->_methodRenderer->setExtraParams('style="width:120px"');
        }

        return $this->_methodRenderer;
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)
                ->toHtml();
        }

        return '<input type="text" readonly="readonly" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
    }


}