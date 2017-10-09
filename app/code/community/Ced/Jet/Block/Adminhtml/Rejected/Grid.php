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

class Ced_Jet_Block_Adminhtml_Rejected_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('_rejected');
		$this->setDefaultSort('id');
	   	 $this->setUseAjax(true);
		$this->setSaveParametersInSession(true);

		
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
		
		$collection = Mage::getModel('jet/fileinfo')->getCollection()
            ->addFieldToSelect('*');
	    
		$this->setCollection($collection);
		return parent::_prepareCollection();

	}
	
	/**
	 * prepare the column in the grid
	 */
	//id,sku,type,price,name,qty,enabled
	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
				'header'    => Mage::helper('jet')->__('ID'),
				'align'     =>'right',
				'width'     => '80px',
				'index'     => 'id',
		));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('jet')->__('Created At'),
            'type'     =>'datetime',
            'index'     => 'created_at',
        ));
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('jet')->__('Updated At'),
            'type'     =>'datetime',
            'index'     => 'updated_at',
        ));
		$this->addColumn('jet_file_id', array(
				'header'    => Mage::helper('jet')->__('Jet File Id'),
				'align'     =>'left',
				'index'     => 'jet_file_id',
		));
		
        $this->addColumn('file_type',
            array(
                'header'=> Mage::helper('jet')->__('File Type'),
                'align'     =>'left',
				'index' => 'file_type',
        ));


		$this->addColumn('status', array(
				'header'    => Mage::helper('jet')->__('Status'),
				'width' 	=> '200px',
				'align'     => 'left',
				'index'     => 'status',
		));
		
		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('jet')->__('Action'),
				'width'     => '150',
				'type'      => 'action',
				'getter'    => 'getId',
				'renderer'  => 'Ced_Jet_Block_Adminhtml_Rejected_Renderer_Resubmit',
		));
		
		return parent::_prepareColumns();
		
	}
	
	// Used for AJAX loading
	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('error_ids');
		 
		$this->getMassactionBlock()->addItem('delete', array(
		'label'=> Mage::helper('jet')->__('Delete'),
		'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
		'confirm' => Mage::helper('jet')->__('Are you sure?')
		));
		 
		return $this;
	}
}
