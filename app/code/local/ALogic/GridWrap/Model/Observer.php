<?php

class ALogic_GridWrap_Model_Observer {
    
    protected function _insertOrderColumns(Mage_Adminhtml_Block_Widget_Grid $grid, $after = 'status')
    {
        $grid->addColumnAfter('gift_wrapped', array(
            'header'=> Mage::helper('algridwrap')->__('Gift Wrapped'),
            'index' => 'gift_wrapped',
            'type' => 'options',
            'options' => array(
                0 => Mage::helper('algridwrap')->__('No'),
                1 => Mage::helper('algridwrap')->__('Yes'),
            ),
            'align' => 'center'
        ), $after);
        $grid->sortColumnsByOrder();
    }
    
    public function beforeHtmlHook(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ( ! $block)
            return;
        
        switch ($block->getType())
        {
            case 'adminhtml/sales_order_grid':
                $this->_insertOrderColumns($block);
                break;
            default:
                //echo $block->getType();
        }
    }
    
    public function applyOrderFlag($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        $data = Mage::getSingleton('checkout/session')->getData('aw_giftwrap', null);
        if (is_null($data)) {
            return $this;
        }
        $giftWrapType = Mage::getModel('aw_giftwrap/type')->load($data['wrap_type_id']);
        if (is_null($giftWrapType->getId())) {
            return $this;
        }
        
        $order->setGiftWrapped(1);
        return $this;
    }
    
}
