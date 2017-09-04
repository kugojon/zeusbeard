<?php
/**
 * Magmodules.eu - http://www.magmodules.eu.
 *
 * NOTICE OF LICENSE
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.magmodules.eu/MM-LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magmodules.eu so we can send you a copy immediately.
 *
 * @category      Magmodules
 * @package       Magmodules_Richsnippets
 * @author        Magmodules <info@magmodules.eu>
 * @copyright     Copyright (c) 2017 (http://www.magmodules.eu)
 * @license       https://www.magmodules.eu/terms.html  Single Service License
 */

class Magmodules_Snippets_Block_Adminhtml_Config_Form_Field_Openinghours
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $_renders = array();

    /**
     * Magmodules_Snippets_Block_Adminhtml_Config_Form_Field_Openinghours constructor.
     */
    public function __construct()
    {
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();

        $rendererDays = $layout->createBlock(
            'snippets/adminhtml_config_form_renderer_select', '',
            array('is_render_to_js_template' => true)
        );

        $rendererDays->setOptions(Mage::getModel('snippets/source_Localbusiness_Days')->toOptionArray());

        $this->addColumn(
            'day', array(
                'label'    => Mage::helper('snippets')->__('Day'),
                'style'    => 'width:140px',
                'renderer' => $rendererDays
            )
        );

        $this->addColumn(
            'from', array(
                'label' => Mage::helper('snippets')->__('Opening Time'),
                'style' => 'width:70px',
            )
        );

        $this->addColumn(
            'to', array(
                'label' => Mage::helper('snippets')->__('Closing Time'),
                'style' => 'width:70px',
            )
        );

        $this->_renders['day'] = $rendererDays;

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('snippets')->__('Add new');
        parent::__construct();
    }

    /**
     * @param Varien_Object $row
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        foreach ($this->_renders as $key => $render) {
            $row->setData(
                'option_extra_attr_' . $render->calcOptionHash($row->getData($key)),
                'selected="selected"'
            );
        }
    }

}