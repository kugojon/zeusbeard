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
class Ced_Jet_Block_Adminhtml_Profile_Edit_Tab_Requiredattributes
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $magentoAttributes;
    public $elementName;
    public function __construct()
    {
        $this->elementName = 'required_attributes';
        $this->addColumn(
            'animal', array(
            'label' => Mage::helper('adminhtml')->__('Animal'),
            'size'  => 28,
            )
        );
        $this->addColumn(
            'colour', array(
            'label' => Mage::helper('adminhtml')->__('Colour'),
            'size'  => 28
            )
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add new coloured animal');

        parent::__construct();
        $this->setTemplate('ced/jet/profile/requiredattributes.phtml');
    }

    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column     = $this->_columns[$columnName];
        $inputName  = $this->elementName . '[#{_id}][' . $columnName . ']';

        $rendered = '<select name="'.$inputName.'">';
        if ($columnName == 'animal') {
            $rendered .= '<option value="cat">Cat</option>';
            $rendered .= '<option value="dog">Dog</option>';
            $rendered .= '<option value="monkey">Monkey</option>';
            $rendered .= '<option value="rabbit">Rabbit</option>';
        } else {
            $rendered .= '<option value="red">Red</option>';
            $rendered .= '<option value="blue">Blue</option>';
            $rendered .= '<option value="yellow">Yellow</option>';
            $rendered .= '<option value="green">Green</option>';
        }

        $rendered .= '</select>';

        return $rendered;
    }
}