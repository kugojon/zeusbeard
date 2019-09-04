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

class Ced_Jet_Block_Adminhtml_Profile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('profileGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('jet/profile')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id', array(
            'header'    => Mage::helper('jet')->__('ID'),
            'align'     =>'right',
            'width'     => '10px',
            'index'     => 'id',
            )
        );

        $this->addColumn(
            'profile_name', array(
            'header'    => Mage::helper('jet')->__('Profile Name'),
            'align'     =>'left',
            'index'     => 'profile_name',
            'width'     => '200px',
            'type'  => 'text',
            )
        );


        $statuses = array('1' => 'Active', '0' => 'Inactive');

        $this->addColumn(
            'profile_status', array(
            'header'    => Mage::helper('jet')->__('Status'),
            'align'     =>'left',
            'index'     => 'profile_status',
            'width'     => '200px',
            'type'  => 'options',
            'options' => $statuses,
            )
        );



        $this->addColumn(
            'total_item', array(
            'header'    => Mage::helper('jet')->__('Total Item'),
            'align'     =>'left',
            'index'     => 'total_item',
            'width'     => '50px',
            'filter'            => false,

            'renderer'  => 'jet/adminhtml_profile_grid_column_renderer_totalitem',
            )
        );

        /*$this->addColumn(
            'active_item', array(
            'header'    => Mage::helper('jet')->__('Active Item'),
            'align'     =>'left',
            'index'     => 'active_item',
            'width'     => '50px',
            'filter'            => false,

            'renderer'  => 'jet/adminhtml_profile_grid_column_renderer_activeitem',

            )
        );

        $this->addColumn(
            'inactive_item', array(
            'header'    => Mage::helper('jet')->__('Inactive Item'),
            'align'     =>'left',
            'index'     => 'inactive_item',
            'width'     => '50px',
            'filter'            => false,

            'renderer'  => 'jet/adminhtml_profile_grid_column_renderer_inactiveitem',

            )
        );*/

        $this->addColumn(
            'action',
            array(
                'header'    =>  Mage::helper('jet')->__('Action'),
                'width'     => '50',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('jet')->__('Edit Profile'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('jet')->__('Upload Products'),
                        'url'       => array('base'=> 'adminhtml/adminhtml_jetrequest/uploadproduct'),
                        'field'     => 'profile_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'action',
                'is_system' => true,
            )
        );
        return parent::_prepareColumns();
    }



    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/adminhtml_profile/profilegrid', array('_current'=>true));
    }

    /**
     * getting row url
     * @return string
     *
     * */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('pcode' => $row->getProfileCode()));
    }


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete', array(
            'label'    => Mage::helper('jet')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('jet')->__('Are you sure?')
            )
        );

        $statuses = array('1' => 'Active', '0' => 'Inactive');


        $this->getMassactionBlock()->addItem(
            'status', array(
            'label'=> Mage::helper('jet')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus/', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('jet')->__('Status'),
                    'default'=>'-1',
                    'values' =>$statuses,
                )
            )
            )
        );
        return $this;
    }


}