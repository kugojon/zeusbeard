<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusReferFriend
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusReferFriend Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusReferFriend
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusReferFriend_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_AffiliateplusReferFriend_Model_Observer
     */
    public function controllerActionPredispatch($observer) {
        //Changed By Adam 28/07/2014
        if (!Mage::helper('affiliateplus')->isAffiliateModuleEnabled())
            return;
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }

    //hainh 28-07-2014
    public function modelConfigDataSaveBefore($observer) {
        if ($observer->getObject()->getSection() == 'affiliateplus') {
            $object = $observer->getObject();
            $group = $object->getGroups();
            $scope = $object->getScope();
            $scopeId = $object->getScopeId();
            $path = 'affiliateplus/refer/url_param_array';
            $newParam = $group['general']['fields']['url_param']['value'];
            if (!$newParam || ($newParam == ''))
                $newParam = 'acc';
            $paramList = Mage::getModel('core/config_data')->getCollection()
                            ->addFieldToFilter('scope', $scope)
                            ->addFieldToFilter('scope_id', $scopeId)
                            ->addFieldToFilter('path', $path)->getFirstItem();
            if (!$paramList)
                $paramList = Mage::getModel('core/config_data');
            else {
                $paramArray = explode(',', $paramList->getValue());
                for ($i = 0; $i < count($paramArray); $i++) {
                    if ($paramArray[$i] == $newParam)
                        unset($paramArray[$i]);
                }
                $paramArray[] = $newParam;
                $newParam = implode(',', $paramArray);
            }
            $paramList->setScope($scope);
            $paramList->setScopeId($scopeId);
            $paramList->setPath($path);
            $paramList->setValue($newParam);
            try {
                $paramList->save();
            } catch (Exception $e) {
                
            }
        }
    }

    //end editing
}
