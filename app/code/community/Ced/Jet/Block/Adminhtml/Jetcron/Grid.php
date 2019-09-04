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


class Ced_Jet_Block_Adminhtml_Jetcron_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

     public function __construct()
     {
        parent::__construct();
        $this->setId('jetcronGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
         $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
       
     }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cron/schedule')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn(
            'schedule_id', array(
            'header'    => Mage::helper('jet')->__('ID'),
            'align'     =>'right',
            'width'     => '10px',
            'index'     => 'schedule_id',
            )
        );

        $this->addColumn(
            'job_code', array(
            'header'    => Mage::helper('jet')->__('Job Code'),
            'align'     =>'left',
            'index'     => 'job_code',
            'width'     => '50px',
            )
        );

        $this->addColumn(
            'status', array(
            'header'=> Mage::helper('jet')->__('Status'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'status'
            )
        );
        $this->addColumn(
            'messages', array(
            'header'=> Mage::helper('jet')->__('Messages'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'messages'
            )
        );
        $this->addColumn(
            'created_at', array(
            'header'=> Mage::helper('jet')->__('Created At'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'created_at'
            )
        );
        $this->addColumn(
            'scheduled_at', array(
            'header'=> Mage::helper('jet')->__('Scheduled At'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'scheduled_at'
            )
        );
        $this->addColumn(
            'executed_at', array(
            'header'=> Mage::helper('jet')->__('Executed At'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'executed_at'
            )
        );
        $this->addColumn(
            'finished_at', array(
            'header'=> Mage::helper('jet')->__('Finished At'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'finished_at'
            )
        );
        return parent::_prepareColumns();
    }
     public function getGridUrl()
     {
        return $this->getUrl('adminhtml/adminhtml_jetattrlist/grid', array('_current'=>true));
     }
}
