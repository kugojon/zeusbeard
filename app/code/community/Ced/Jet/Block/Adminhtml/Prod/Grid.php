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
 

class Ced_Jet_Block_Adminhtml_Prod_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
        parent::__construct();
		$this->setId('_prod');
		$this->setDefaultSort('id');
	    $this->setUseAjax(true);
		$this->setSaveParametersInSession(true);
	}
	
	protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	
	protected function _prepareCollection()
	{
        $profileId = $this->getRequest()->getParam('profile_id');

		/*$result=Mage::getModel('jet/jetcategory') ->getCollection()->addFieldToSelect('magento_cat_id');
		$resultdata=array();
		foreach($result as $val){
			$resultdata[]=$val['magento_cat_id'];
		}*/
		
		$collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
			->addAttributeToSelect('jet_product_status')
			->addAttributeToSelect('price');
						
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')){ 
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
		} 
       	
		$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
		$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');


        if($profileId){
            $productIds = Mage::getModel('jet/profileproducts')->getProfileProducts($profileId);
            $collection->addFieldToFilter('entity_id', $productIds);
        }

	/*	$collectionData = $collection;
	
		$collectionProd = Mage::getModel('catalog/product')->getCollection();
		$collectionProd->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id =entity_id', null, 'left');
		$collectionProd->addAttributeToSelect('*')
        ->addAttributeToFilter('category_id', array('in' => $resultdata));

		$ids = $collectionProd->getAllIds();
		
		$ids = array_unique($ids);*/
		
		$collection
					->addAttributeToFilter('type_id', array('in' => array('simple','configurable')))
					->addAttributeToFilter('visibility', 4);
					//->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
					//->addAttributeToFilter('type_id', array('in' => array('simple','configurable')));
		$this->setCollection($collection);
		return parent::_prepareCollection();

	}
	
	/**
	 * prepare the column in the grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('entity_id', array(
				'header'    => Mage::helper('catalog')->__('ID'),
				'align'     =>'right',
				'width'     => '80px',
				'index'     => 'entity_id',
		));

		$this->addColumn('sku', array(
				'header'    => Mage::helper('catalog')->__('Sku'),
				'align'     =>'left',
				'index'     => 'sku',
		));
        $this->addColumn('category1', array(
                    'header'    => Mage::helper('jet')->__('Category'),
                    'index'     => 'category1',
                    'sortable'  => false,
                    'width' => '50px',
                    'type'  => 'options',
                    'options'   => Mage::getSingleton('jet/system_config_source_category')->toOptionArray(),
                    'renderer'  => 'Ced_Jet_Block_Adminhtml_Prod_Renderer_Category',
                    'filter_condition_callback' => array($this, 'filterCallback'),
            ),'name');
		
		$store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
        ));


		$this->addColumn('name', array(
				'header'    => Mage::helper('catalog')->__('Name'),
				'width' 	=> '200px',
				'align'     => 'left',
				'index'     => 'name',
		));
		
		if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
            ));
        }
		
		$this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));

       $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
		
		$this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
		$jet_state = Mage::getModel('eav/config')->getAttribute('catalog_product', 'jet_product_status');
		$options = $jet_state->getSource()->getAllOptions(false);
		$jetcomstatus = array();
		foreach ($options as $option){
			$jetcomstatus[$option['value']] = $option['label'];
		}
		
		$this->addColumn('jet_product_status',
            array(
                'header'=> Mage::helper('catalog')->__('Jet Product Status'),
                'width' => '60px',
                'index' => 'jet_product_status',
                'type'  => 'options',
                'options' => $jetcomstatus,
        ));

        $jetValidation = Mage::getModel('jet/source_productvalidation')->getOptionArray();
        $this->addColumn('jet_product_validation',
            array(
                'header'    =>  Mage::helper('jet')->__('Validation'),
                'width'     => '100px',
                'type'      => 'options',
                'getter'    => 'getId',
                'sortable'  => false,
                'index'     => 'jet_product_validation',
                'options' => $jetValidation,

                'is_system' => true,
                'renderer' => 'Ced_Jet_Block_Adminhtml_Prod_Renderer_ValidationStatus',
            ));




        $this->addColumn('action',
  			array(
  					'header'    =>  Mage::helper('jet')->__('Action'),
  					'width'     => '100px',
  					'type'      => 'action',
  					'getter'    => 'getId',
  					/*'actions'   => array(
  							array(
  									'caption'   => Mage::helper('jet')->__(' Jet Details'),
  									'url'       => array('base'=> 'adminhtml/adminhtml_jetrequest/productDetails'),
  									'field'     => 'id'
  							)
					), */
  					'filter'    => false,
  					'sortable'  => false,
  					'index'     => 'action',
  					'is_system' => true,
                    'renderer' => 'Ced_Jet_Block_Adminhtml_Prod_Renderer_Showerror',
  			));
		$this->addColumn('save', array(
            'header'    => Mage::helper('jet')->__('Upload'),
            'align'     =>'right',
            'width'     => '50px',
            'renderer'  =>'Ced_Jet_Block_Adminhtml_Prod_Renderer_Upload',
            'index'     => 'save',
            'filter'    => false,
            'sortable'  => false,
        ));
		
		return parent::_prepareColumns();
		
	}
 public function filterCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $_category = Mage::getModel('catalog/category')->load($value);
        $collection->addCategoryFilter($_category);
        
        return $collection;
    }

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('product');

		$profileId = $this->getRequest()->getParam('profile_id');


        $this->getMassactionBlock()->addItem('validate_products', array(
            'label'=> Mage::helper('jet')->__('Validate Products'),
            'url'  => $this->getUrl('adminhtml/adminhtml_jetajax/validateProducts', array('profile_id' => $profileId)),
        ));


        $this->getMassactionBlock()->addItem('ajaximport', array(
                'label'=> Mage::helper('jet')->__('Validate & Upload Products to Jet'),
                'url'  => $this->getUrl('adminhtml/adminhtml_jetajax/massimport', array('profile_id' => $profileId)),
        ));



        $this->getMassactionBlock()->addItem('ajaxarchive', array(
            'label'=> Mage::helper('jet')->__('Archive Products On Jet'),
            'url'  => $this->getUrl('adminhtml/adminhtml_jetajax/massarchived', array('profile_id' => $profileId)),
        ));


        $this->getMassactionBlock()->addItem('ajaxunarchive', array(
            'label'=> Mage::helper('jet')->__('Unarchive Products On Jet'),
            'url'  => $this->getUrl('adminhtml/adminhtml_jetajax/massunarchived', array('profile_id' => $profileId)),
        ));

        $this->getMassactionBlock()->addItem('sync_inventory_price', array(
            'label'=> Mage::helper('jet')->__('Sync Inventory & Price'),
            'url'  => $this->getUrl('adminhtml/adminhtml_jetajax/sync', array('profile_id' => $profileId)),
        ));

        $this->getMassactionBlock()->addItem('sync_product_status', array(
            'label'=> Mage::helper('jet')->__('Sync Product Status'),
            'url'  => $this->getUrl('adminhtml/adminhtml_jetajax/syncProductStatus', array('profile_id' => $profileId)),
        ));
		
		return $this;
	}		
	
	public function getGridUrl()
	{
	    $profileId = $this->getRequest()->getParam('profile_id');
		return $this->getUrl('adminhtml/adminhtml_jetrequest/uploadproductgrid', array('_current'=>true, 'profile_id' => $profileId));
	}
}
