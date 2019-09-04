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

 
class Ced_Jet_Model_Payment_Payjetcom extends Mage_Payment_Model_Method_Abstract
{

    protected $_code = 'payjetcom';
    protected $_canAuthorize = true;
    protected $_canCancelInvoice = false;
    protected $_canCapture = false;
    protected $_canCapturePartial = false;
    protected $_canCreateBillingAgreement = false;
    protected $_canFetchTransactionInfo = false;
    protected $_canManageRecurringProfiles = false;
    protected $_canOrder = false;
    protected $_canRefund = false;
    protected $_canRefundInvoicePartial = false;
    protected $_canReviewPayment = false;
    /* Setting for disable from front-end. */
    /* START */
    protected $_canUseCheckout = false;
    protected $_canUseForMultishipping = false;
    protected $_canUseInternal = false; 
    protected $_canVoid = false;
    protected $_isGateway = false;
    protected $_isInitializeNeeded = false;
    
    /* END */

    /**
     * Check whether payment method can be used
     * @param Mage_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null) 
    {
        return true;
    }
    
    public function getCode()
    {
        return $this->_code;
    }

}
