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

class Ced_Jet_Block_Adminhtml_Mfeeds_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('_mfeeds');
		 $this->setDefaultSort('created_at');
        $this->setDefaultDir('DSC');
         //$this->setUseAjax(true);
        //$this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
		
	}
	
	protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	
	/**
	 * prepare the collection of gift and set for grid
	 */
	
	protected function _prepareCollection()
	{	
		
		$collection=Mage::getSingleton('adminhtml/session')->getData('mfeeds_collection');

        $this->setCollection($collection);
		
		return parent::_prepareCollection();

	}
	
	/**
	 * prepare the column in the grid
	 */
	//id,sku,type,price,name,qty,enabled
	protected function _prepareColumns()
	{
		$this->addColumn('name', array(
				'header'    => Mage::helper('jet')->__('File Name'),
				'align'     =>'right',
				'width'     => '80px',
				'index'     => 'name',
		));
		 $this->addColumn('created_at', array(
            'header'    => Mage::helper('jet')->__('Created At'),
            'type'     =>'datetime',
            'width'     => '80px',
            'index'     => 'created_at',
        ));
		$this->addColumn('content', array(
				'header'    => Mage::helper('jet')->__('Processed Skus'),
				'align'     =>'right',
				'width'     => '180px',
				'index'     => 'content',
		));

		return parent::_prepareColumns();
		
	}
	
	// Used for AJAX loading
	/*public function getGridUrl()
    {
        return $this->getUrl('adminhtml/adminhtml_jetproduct/gridfilter', array('_current'=>true));
    }*/

	
}
