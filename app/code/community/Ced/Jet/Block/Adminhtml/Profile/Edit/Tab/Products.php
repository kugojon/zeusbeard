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
class Ced_Jet_Block_Adminhtml_Profile_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * setting parametrs
	 * @return void
	 * 
	 */
	public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('id');
        $this->setDefaultDir('asc');
        $this->setId('groupProductPpcode');
        $this->setDefaultFilter(array('in_profile_products'=>1));
        $this->setUseAjax(true);
    }

    /**
     * filtering column
     * @param $column
     * @see Mage_Adminhtml_Block_Widget_Grid::_addColumnFilterToCollection()
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_profile_products') {
            $inProfileIds = $this->_getProducts();
            if (empty($inProfileIds)) {
                $inProfileIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$inProfileIds));
            }
            else {
                if($inProfileIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$inProfileIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * 
     * preparing collection 
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareCollection()
     */
    protected function _prepareCollection()
    {
        $profileCode = $this->getRequest()->getParam('id');
        Mage::register('PCODE', $profileCode);
        $collection = Mage::getModel('catalog/product')
							->getCollection()
							->addAttributeToSelect('*')
                            ->addAttributeToFilter('type_id', array('in' => array('configurable','simple')))
                            ->addAttributeToFilter('visibility',  array('neq' => 1)) ;

        $store = $this->_getStore();

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
        }

          $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * 
     * preparing columns
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareColumns()
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_profile_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_profile_products',
            'values'    => $this->_getProducts(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));

        $this->addColumn('entity_id', array(
			'header'    => Mage::helper('catalog')->__('Id'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'entity_id',
			'filter_index' => 'entity_id',
			'type'	  => 'int',

        ));
		
	    $this->addColumn('name', array(
            'header'        => Mage::helper('catalog')->__('Product Name'),
            'align'         => 'left',
            'type'          => 'text',
            'index'         => 'name',
			'filter_index' => 'name',
        ));

        $this->addColumn('type_id',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ));



        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '60px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
            ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'index' => 'sku',
            ));


        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
            ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '50px',
                    'type'  => 'number',
                    'index' => 'qty',
                ));
        }
        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
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



        return parent::_prepareColumns();
    }
     public function filterCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $_category = Mage::getModel('catalog/category')->load($value);
        $collection->addCategoryFilter($_category);
        
        return $collection;
    }

    /**
     * getting grid url
     * @return string
     *
     * */
    public function getGridUrl()
    {
        $profileCode = $this->getRequest()->getParam('pcode');
        return $this->getUrl('*/*/editprofilegrid', array('pcode' => $profileCode));
    }

    /**
     * 
     * 
     * @param string $json
     * @return mixed|multitype:|string
     */
    public function _getProducts($json=false)
    {
        if ( $this->getRequest()->getParam('in_profile_product') != "" ) {
            return explode(",", $this->getRequest()->getParam('in_profile_product'));
            //return $this->getRequest()->getParam('in_profile_product');
        }
        $profileCode = ( strlen($this->getRequest()->getParam('pcode')) > 0 ) ? $this->getRequest()->getParam('pcode') : Mage::registry('PCODE');
        //$vendors  = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('group',array('eq'=>$groupCode));

        $profile =  Mage::registry('current_profile');
        $profileId = false;
        if($profile && $profile->getId())
            $profileId = $profile->getId();

        $products  = Mage::getModel('jet/profileproducts')->getProfileProducts($profileId);

		if (sizeof($products) > 0) {
            if ( $json ) {
                $jsonProducts = Array();
                foreach($products as $productId) $jsonProducts[$productId] = 0;
                return Mage::helper('core')->jsonEncode((object)$jsonProducts);
            } else {
                return array_values($products);
            }
        } else {
            if ( $json ) {
                return '{}';
            } else {
                return array();
            }
        }
    }
}