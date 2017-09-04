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
 * @package    AW_Points
 * @version    1.7.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Points_Model_Actions_ReviewApproved extends AW_Points_Model_Actions_Abstract
{
    protected $_action = 'review_approved';
    protected $_comment = 'Reward for reviewing product %s';

    /**
     * @return Mage_Review_Model_Review
     */
    protected function _getReview()
    {
        return $this->getObjectForAction();
    }

    protected function _limitAmountByDay($amount)
    {
        $reviewStoreId = $this->_getReview()->getStoreId();
        $pointLimitForAction = $this->_getConfigHelper()->getPointsLimitForReviewingProduct($reviewStoreId);

        $collection = Mage::getModel('points/transaction')
            ->getCollection()
            ->addFieldToFilter('summary_id', $this->getSummary()->getId())
            ->addFieldToFilter('action', $this->getAction())
            ->limitByDay(Mage::getModel('core/date')->gmtTimestamp());

        /* Current summ getting */
        $summ = 0;
        foreach ($collection as $transaction) {
            $summ += $transaction->getBalanceChange();
        }
        return $this->_calculateNewAmount($summ, $amount, $pointLimitForAction);
    }

    protected function _limitAmountByWordsCount($amount)
    {
        $minimumWordsCount = $this->_getConfigHelper()->getMinumumWordsCountInReview();
        if ($minimumWordsCount) {
            $reviewContent = $this->_getReview()->getData('detail');
            $wordsCount = count(preg_split('/\s+/mu', trim($reviewContent)));
            return $wordsCount >= $minimumWordsCount
                ? $amount
                : 0;
        }
        return $amount;
    }

    protected function _applyLimitations($amount)
    {
        if ($newAmount = $this->_limitAmountByWordsCount($amount)) {
            $newAmount = $this->_limitAmountByDay($newAmount);
        }
        return parent::_applyLimitations($newAmount);
    }

    public function getCommentHtml($area = self::ADMIN)
    {
        $locale = Mage::getStoreConfig('general/locale/code', $this->_transaction->getStoreId());
        return $this->_translateComment($this->_transaction->getComment(), $locale);
    }

    public function getComment()
    {
        if (isset($this->_commentParams['product_name'])) {
            return array($this->_comment, $this->_commentParams['product_name']);
        }
        return $this->_comment;
    }
}
