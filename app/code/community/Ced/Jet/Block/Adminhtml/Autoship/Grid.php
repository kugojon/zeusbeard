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


class Ced_Jet_Block_Adminhtml_Autoship_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        //$this->_removeButton('add');
        $this->setId('_autoship');
        $this->setDefaultSort('id');
        /*$this->setUseAjax(true);*/
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
        $collection = Mage::getModel('jet/autoship')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare the column in the grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id', array(
            'header'    => Mage::helper('jet')->__('ID'),
            'align'     =>'right',
            'width'     => '80px',
            'index'     => 'id',
            )
        );
        $this->addColumn(
            'order_id', array(
            'header'=> Mage::helper('jet')->__('Magento Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'order_id',
            'renderer'  => 'Ced_Jet_Block_Adminhtml_Autoship_Renderer_Orderinfo'
            )
        );

       /* $this->addColumn('jet_reference_id', array(
            'header'    => Mage::helper('jet')->__('Magento order ID'),
            'align'     =>'left',
            'type'      => 'action',
            'actions'   => array(
                array(
                    'caption' => 14758962,
                    'url'     => array('base'=>'sales_order/view'),
                    'field'   => 'order_id',
                    'data-column' => 'action',
                )
            ),
            'index'     => 'order_id',
        ));*/

        $this->addColumn(
            'jet_reference_id)', array(
            'header'    => Mage::helper('jet')->__('Jet Reference Order ID'),
            'align'     =>'left',
            'index'     => 'jet_reference_id',
            )
        );
        $this->addColumn(
            'error_log',
            array(
                'header'=> Mage::helper('jet')->__('Reason to failed'),
                'align'     =>'left',
                'index' => 'error',
                'sort'  => false ,
                'filter' => false ,
            )
        );
        $this->addColumn(
            'jet_shipment_status',
            array(
                'header'=> Mage::helper('jet')->__('Jet Shipment Status'),
                'align'     =>'left',
                'index' => 'jet_shipment_status',
                'sort'  => false ,
                'filter' => false ,
            )
        );


        return parent::_prepareColumns();

    }

    /**
     * Delete failed Jet Order's Log
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem(
            'delete', array(
             'label'=> Mage::helper('jet')->__('Delete'),
             'url'  => $this->getUrl('adminhtml/adminhtml_jetorder/deleteautoshiplog'),
            )
        );




        return $this;
    }



}
