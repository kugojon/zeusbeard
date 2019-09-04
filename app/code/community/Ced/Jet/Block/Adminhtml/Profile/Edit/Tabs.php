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
  * @category    Ced
  * @package     Ced_Jet
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_Jet_Block_Adminhtml_Profile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * setting title information
     * @return void
     * 
     */
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendor_group_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('jet')->__('Profile Information'));
  }
  
  /**
   * Preparing Html befor rendering
   * @see Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml()
   */
    protected function _beforeToHtml() 
    {
        $this->addTab(
            'profile_info', array(
            'label'     => Mage::helper('jet')->__('Profile Info'),
            'content'   => $this->getLayout()->createBlock('jet/adminhtml_profile_edit_tab_info')->toHtml(),
            )
        );
        
        /*$this->addTab('profile_configurations', array(
			'label'     => Mage::helper('jet')->__('Profile Configurations'),
			'content'   => $this->getLayout()->createBlock('jet/adminhtml_profile_edit_tab_configurations')->toHtml(),
		));*/



        $this->addTab(
            'profile_categorymapping', array(
            'label' => Mage::helper('jet')->__('Jet Category Mapping '),
            'content' =>  $this->getLayout()->createBlock(
                'jet/adminhtml_profile_edit_tab_categorymapping'
            )
                ->append($this->getLayout()->createBlock('jet/adminhtml_profile_edit_tab_attributes', 'required_attributes'))
            //->append($this->getLayout()->createBlock('jet/adminhtml_profile_edit_tab_configattributes', 'config_required_attributes'))
            ->toHtml()
            )
        );










        $chooser = $this->getLayout()
            ->createBlock('jet/adminhtml_profile_edit_tab_products')
            ->setName(Mage::helper('core')->uniqHash('products_grid_'))
            ->setUseMassaction(true);
        /* @var $serializer Mage_Adminhtml_Block_Widget_Grid_Serializer */
        $serializer = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer');
        $serializer->initSerializerBlock($chooser, '_getProducts', 'in_profile_product', 'in_profile_product');


        $this->addTab(
            'profile_products', array(
            'label'     => Mage::helper('jet')->__('Profile Products'),
            'content'   =>  $chooser->toHtml().$serializer->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
  
  /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        return 'jet/adminhtml_profile_edit_tab_information';
    }
}
