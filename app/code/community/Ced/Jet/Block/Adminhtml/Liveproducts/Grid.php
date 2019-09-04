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

 

class Ced_Jet_Block_Adminhtml_Liveproducts_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('_prodlive');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        //$this->setFilterVisibility(false);
    }
    
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
    protected function _prepareCollection()
    {    


        $collection=Mage::getSingleton('adminhtml/session')->getData('live_product_collection');

        $this->setCollection($collection);
        
        return parent::_prepareCollection();

    }
    
    /**
     * prepare the column in the grid
     */
    protected function _prepareColumns()
    {
        
        $this->addColumn(
            'sku', array(
                'header'    => Mage::helper('catalog')->__('Sku'),
                'width'     => '250px',
                'align'     =>'left',
                'index'     => 'sku',
            'filter_index'=>'sku',
            'filter_condition_callback' => array($this, '_statusFilter'),
            )
        );
        
        $this->addColumn(
            'name', array(
                'header'    => Mage::helper('catalog')->__('Name'),
                'width'     => '250px',
                'align'     => 'left',
                'index'     => 'name',
            )
        );
    $this->addColumn(
        'companalysis',
        array(
            'header'    =>  Mage::helper('jet')->__('Competitors Analysis'),
            'width'     => '65px',
            'type'      => 'companalysis',
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
            'index'     => 'companalysis',
            'is_system' => true,
                    'renderer' => 'Ced_Jet_Block_Adminhtml_Prod_Renderer_Analysis',
        )
    );
        $this->addColumn(
            'price',
            array(
            'header'    =>  Mage::helper('jet')->__('Price'),
            'width'     => '45px',
            'type'      => 'price',
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
            'index'     => 'price',
            'is_system' => true,
                    'renderer' => 'Ced_Jet_Block_Adminhtml_Prod_Renderer_Price',
            )
        );
    $this->addColumn(
        'qty',
        array(
            'header'    =>  Mage::helper('jet')->__('Qty'),
            'width'     => '45px',
            'type'      => 'qty',
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
            'index'     => 'qty',
            'is_system' => true,
                    'renderer' => 'Ced_Jet_Block_Adminhtml_Prod_Renderer_Qty',
        )
    );
        $this->addColumn(
            'archive',
            array(
                      'header'    =>  Mage::helper('jet')->__('Archive'),
                      'width'     => '45px',
                      'type'      => 'archieve',
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
                      'index'     => 'archieve',
                      'is_system' => true,
                    'renderer' => 'Ced_Jet_Block_Adminhtml_Prod_Renderer_Archieve',
              )
        );
        $this->addColumn(
            'unarchive',
            array(
                      'header'    =>  Mage::helper('jet')->__('Unarchive'),
                      'width'     => '45px',
                      'type'      => 'unarchieve',
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
                      'index'     => 'unarchieve',
                      'is_system' => true,
                    'renderer' => 'Ced_Jet_Block_Adminhtml_Prod_Renderer_Unarchieve',
              )
        );
        return parent::_prepareColumns();
        
    }
protected function _statusFilter($collection, $column) 
{
  $collection=Mage::getSingleton('adminhtml/session')->getData('live_product_collection');
$arr = $collection->toArray();
$col = array();
    $filterroleid = $column->getFilter()->getValue();
  if (!$value = $column->getFilter()->getValue()) { 
     $collection= new Varien_Data_Collection(); 
    foreach ($arr as $key => $value) {
            if(is_array($value))
            {
              foreach ($value as $kz => $vz) {
                        $thing_1="";
                        $thing_1 = new Varien_Object();
                        $thing_1->setSku($vz['sku']);
                        $thing_1->setName($vz['name']);
                        $collection->addItem($thing_1);
              }
            }
    }

   $this->setCollection($collection);
   return $this;
  }
  
   foreach ($arr as $key => $value) { 
if(is_array($value))
{
  foreach ($value as $kz => $vz) {
       if (strpos($vz['sku'], $filterroleid) !== false) 
       {
           $col[] = $vz;
       }
  }
}
   }

   if(count($col)>1)
   {
       $collection= new Varien_Data_Collection(); 
   foreach ($col as $key => $value) {
    $thing_1="";
            $thing_1 = new Varien_Object();
            $thing_1->setSku($value['sku']);
            $thing_1->setName($value['name']);
            $collection->addItem($thing_1);
   }

    $this->setCollection($collection);
   }
   else
   {
    $collection= new Varien_Data_Collection(); 
    foreach ($arr as $key => $value) {
            if(is_array($value))
            {
              foreach ($value as $kz => $vz) {
                        $thing_1="";
                        $thing_1 = new Varien_Object();
                        $thing_1->setSku($vz['sku']);
                        $thing_1->setName($vz['name']);
                        $collection->addItem($thing_1);
              }
            }
    }

   $this->setCollection($collection);
   }
  
   return $this;
    
}
    
    
    public function getGridUrl()
    {
        /*return $this->getUrl('adminhtml/adminhtml_jetproduct/liveproducts', array('_current'=>true));*/
    }

}
