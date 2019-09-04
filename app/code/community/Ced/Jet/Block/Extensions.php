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
class Ced_Jet_Block_Extensions extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $header = $html = $footer = '';
        $header = $this->_getHeaderHtml($element);
        $modules = Mage::helper('jet/core')->getCedCommerceExtensions();
        $field = $element->addField(
            'extensions_heading', 'note', array(
            'name'  => 'extensions_heading',
            'label' => '<a href="javascript:;"><b>Installed Version</b></a>',
            'text' => '<a href="javascript:;"><b>Available Version</b></a>',
            )
        )->setRenderer($this->_getFieldRenderer());
        $html.= $field->toHtml();
        foreach ($modules as $moduleName=>$releaseVersion) {            
            $html.= $this->_getFieldHtml($element, $moduleName, $releaseVersion);
        }

        if (strlen($html) == 0) {
            $html = '<p>'.$this->__('No records found.').'</p>';
        }

        $footer .= $this->_getFooterHtml($element);
        return $header. $html . $footer;
    }
    
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }

        return $this->_fieldRenderer;
    }
    
    protected function _getFieldHtml($fieldset, $moduleName,$currentVersion = '0.0.1')
    {
        $allExtensions  = unserialize(Mage::app()->loadCache('all_extensions_by_cedcommerce'));
        $name            = $moduleName;
        $releaseVersion = 'Up to date';
        $warning = '';
        if ($allExtensions && isset($allExtensions[$moduleName])) {
            $url     = $allExtensions[$moduleName]['url'];
            $warning = isset($allExtensions[$moduleName]['warning'])?$allExtensions[$moduleName]['warning']:'';
            
            if(strlen($warning) == 0) {
                $releaseVersion = $allExtensions[$moduleName]['release_version'];
                $releaseVersion = '<a href="'.$url.'" target="_blank" title="'.strip_tags($allExtensions[$moduleName]['description']).'">'.$name.'-'.$releaseVersion.'</a>';
            } else {
                $releaseVersion = '<div class="notification-global"><strong class="label">'.$warning.'</strong></div>';
            }
        }
        
        $field = $fieldset->addField(
            strtolower($moduleName), 'note', array(
            'name'  => 'jet',
            'label' => '<span style="text-align: center;">'.$name.'-'.$currentVersion.'</span>',
            'text' => '<span style="text-align: center;">'.$releaseVersion.'</span>',
            )
        )->setRenderer($this->_getFieldRenderer());
        return $field->toHtml();
    }
}