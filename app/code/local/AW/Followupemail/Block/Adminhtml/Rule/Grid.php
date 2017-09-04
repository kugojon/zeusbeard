<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Followupemail
 * @version    3.6.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Followupemail_Block_Adminhtml_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ruleGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('asc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('followupemail/rule')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();

        foreach($this->getCollection()->getItems() as $item){
            $item->setData('store_ids', @explode(',', $item->getData('store_ids')));
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                 'header' => $this->__('id'),
                 'align'  => 'right',
                 'width'  => '50px',
                 'index'  => 'id',
            )
        );

        $this->addColumn(
            'title',
            array(
                 'header' => $this->__('Title'),
                 'align'  => 'left',
                 'index'  => 'title',
            )
        );

        $this->addColumn(
            'event_type',
            array(
                 'header'  => $this->__('Event type'),
                 'align'   => 'left',
                 'index'   => 'event_type',
                 'type'    => 'options',
                 'options' => Mage::getModel('followupemail/source_rule_types')->toShortOptionArray()
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'store_ids',
                array(
                    'header'                    => $this->__('Store'),
                    'index'                     => 'store_ids',
                    'type'                      => 'store',
                    'store_all'                 => true,
                    'store_view'                => true,
                    'sortable'                  => false,
                    'renderer'                  => 'Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store',
                    'filter_condition_callback' => array($this, '_filterStoreCondition'),
                )
            );
        }

        $this->addColumn(
            'product_type_ids',
            array(
                'header'                    => $this->__('Product type'),
                'align'                     => 'left',
                'width'                     => '150px',
                'index'                     => 'product_type_ids',
                'options'                   => Mage::getModel('followupemail/source_product_types')
                        ->toShortOptionArray(),
                'filter_condition_callback' => array($this, '_filterProductTypeCondition'),
                'value_separator'           => ',',
                'line_separator'            => '<br>',
                'renderer'                  => 'AW_Followupemail_Block_Adminhtml_Rule_Grid_Column_Multiselect',
            )
        );

        $this->addColumn(
            'status',
            array(
                 'header'  => $this->__('Status'),
                 'align'   => 'left',
                 'width'   => '80px',
                 'index'   => 'is_active',
                 'type'    => 'options',
                 'options' => Mage::getModel('followupemail/source_rule_status')->toOptionArray()
            )
        );

        $this->addColumn(
            'sale_amount',
            array(
                 'header' => $this->__('Sale amount'),
                 'align'  => 'left',
                 'width'  => '80px',
                 'index'  => 'sale_amount',
            )
        );

        $this->addColumn(
            'action',
            array(
                 'header'    => $this->__('Action'),
                 'width'     => '100',
                 'type'      => 'action',
                 'getter'    => 'getId',
                 'actions'   => array(
                     array(
                         'caption' => $this->__('Edit'),
                         'url'     => array('base' => '*/*/edit'),
                         'field'   => 'id'
                     )
                 ),
                 'filter'    => false,
                 'sortable'  => false,
                 'index'     => 'stores',
                 'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('followupemail');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                 'label'   => $this->__('Delete'),
                 'url'     => $this->getUrl('*/*/massDelete'),
                 'confirm' => $this->__('Are you sure?')
            )
        );

        $this->getMassactionBlock()->addItem(
            'status',
            array(
                 'label'      => $this->__('Change status'),
                 'url'        => $this->getUrl('*/*/massStatus', array('_current' => true)),
                 'additional' => array(
                     'visibility' => array(
                         'name'   => 'status',
                         'type'   => 'select',
                         'class'  => 'required-entry',
                         'label'  => $this->__('Status'),
                         'values' => Mage::getModel('followupemail/source_rule_status')->toOptionArray()
                     )
                 )
            )
        );
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->getSelect()->where('find_in_set(?, store_ids)', $value);
    }

    protected function _filterProductTypeCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $cond = '';
        foreach (explode(' ', $value) as $v) {
            $cond .= ' OR product_type_ids LIKE \'%' . $v . '%\'';
        }
        $cond = substr($cond, 4);
        $collection->getSelect()->where($cond);
    }
}