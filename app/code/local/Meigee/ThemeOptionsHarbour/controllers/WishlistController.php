<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_WishlistController extends Mage_Core_Controller_Front_Action
{
    public function countAction()
    {
		echo MAGE::helper('wishlist')->getItemCount();
    }
}