<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
class Amasty_Xnotif_Block_Adminhtml_Stock extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_stock';
        $this->_blockGroup = 'amxnotif';
        $this->_headerText = Mage::helper('amxnotif')->__('Stock Alerts');
        
        $scheduleCollection = Mage::getModel("cron/schedule")->getCollection()
                ->addFieldToFilter('job_code', array('eq' => 'catalog_product_alert'));
        
        $scheduleCollection->getSelect()->order("schedule_id desc");
        if ($scheduleCollection->getSize() < 1) {
            $this->_headerText .= '<div style="font-size: 13px;">'
                . Mage::helper('amxnotif')->__(
                    'No cron job "catalog_product_alert" found.'
                    .' Please check your cron configuration: <a href="%s">Read more</a>',
                    'https://amasty.com/knowledge-base/i-can-t-send-notifications.html'
                )
                . '</div>';
        }
        
        $this->_removeButton('add'); 
    }
}