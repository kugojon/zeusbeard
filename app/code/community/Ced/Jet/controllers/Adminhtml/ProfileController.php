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
require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'System' . DS . 'ConfigController.php';

class Ced_Jet_Adminhtml_ProfileController extends Ced_Jet_Controller_Adminhtml_MainController
{
    /**
     * Profile products
     * @var array
     */
    public $products = array();

    protected function _isAllowed()
    {
        return true;
    }


    /**
     *
     * @param string $idFieldName
     * @return mixed
     */
    protected function _initProfile($idFieldName = 'pcode')
    {
        $this->_title($this->__('Jet'))->_title($this->__('Profile'));

        $profileCode = $this->getRequest()->getParam($idFieldName);
        $profile = Mage::getModel('jet/profile');

        $id = $this->getRequest()->getParam('id');
        if ($profileCode) {
            $profile->loadByField('profile_code', $profileCode);
        } else if ($id > 0) {
            $profile = $profile->load($id);
        }

        $this->getRequest()->setParam('is_jet', 1);
        Mage::register('current_profile', $profile);
        return Mage::registry('current_profile');
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('jet/jetprofile')
            ->_addBreadcrumb(
                Mage::helper('jet')->__('Jet'),
                Mage::helper('jet')->__('Profile')
            );
        return $this;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * new action
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     *
     * Edit action
     * @return void
     */
    public function editAction()
    {

        $profile = $this->_initProfile();
        $this->_initAction();

        if ($profile->getId()) {
            $breadCrumb = $this->__('Edit Jet Profile');
            $breadCrumbTitle = $this->__('Edit Jet Profile');
        } else {
            $breadCrumb = $this->__('Add New Jet Profile');
            $breadCrumbTitle = $this->__('Add New Jet Profile');
        }

        $this->_title($profile->getId() ? $profile->getProfileName() : $this->__('New Jet Profile'));

        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        Mage::app()->getLayout()->getBlock('head')->removeItem('js', 'mage/adminhtml/grid.js');
        Mage::app()->getLayout()->getBlock('head')->addJs('ced/grid.js');
        $this->_addContent($this->getLayout()->createBlock('jet/adminhtml_profile_edit'))
            ->_addLeft($this->getLayout()->createBlock('jet/adminhtml_profile_edit_tabs'));
        $this->_addJs($this->getLayout()->createBlock('adminhtml/template')->setTemplate('ced/jet/profile_grid_js.phtml'));
        $this->renderLayout();
    }

    /**
     * Save action
     * @return void
     *
     */
    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        $tab = $this->getRequest()->getParam('tab', false);
        $website = $this->getRequest()->getParam('website', false);

        $pcode = $this->getRequest()->getParam('pcode', false);
        $profileData = $this->getRequest()->getPost();
        $profileProducts = $this->getRequest()->getParam('in_profile_products', null);
        if(!is_array($this->getRequest()->getParam('in_profile_products', null))) {
            $profileProducts = explode(',' , $this->getRequest()->getParam('in_profile_products', null));
        }
        
        
       /* $profileData = $this->getRequest()->getPost();
        //print_r($profileData);die;
        //$resource   = explode(',', $this->getRequest()->getPost('resource', false));
          print_r($resource);die; 
        $profileProducts = $this->getRequest()->getParam('in_profile_product', null);
        parse_str($profileProducts, $profileProducts);
        $profileProducts = array_keys($profileProducts);*/


        /*        $oldProfileProducts = $this->getRequest()->getParam('in_profile_product_old');
                parse_str($oldProfileProducts, $oldProfileProducts);
                $oldProfileProducts = array_keys($oldProfileProducts);*/


        //$isAll = $this->getRequest()->getParam('all');
        //if ($isAll)
        //   $resource = array("all");
        //print_r($resource);die;
        $profile = $this->_initProfile('pcode');
        $oldProfileProducts = Mage::getModel('jet/profileproducts')->getProfileProducts($profile->getId());

        if (!$profile->getId() && $pcode) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('This profile no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $attributes = array();
            if (isset($profileData['node_id'])) {
                $attributes = Mage::helper('jet')->getNodeAttributes($profileData['node_id']);
            }

            $attributeMapped = array();
            if (isset($attributes['attributes']))
                $attributeMapped['jet_attribute'] = $attributes['attributes'];
            if (isset($profileData['mapped_attribute']))
                $attributeMapped['mapped_attribute'] = $profileData['mapped_attribute'];


            $profile->addData($profileData);
            $profile->setProfileAttributeMapping(json_encode($attributeMapped));

            Mage::dispatchEvent('jet_profile_prepare_save', array('object' => $profile, 'request' => $this->getRequest()));
            $profile->save();


            /*$configuration = Mage::getControllerInstance('Mage_Adminhtml_System_ConfigController', $this->getRequest(), $this->getResponse());
            $configuration->dispatch('save');*/

            $deleteProds = array_diff($oldProfileProducts, $profileProducts);
            $addProds = array_diff($profileProducts, $oldProfileProducts);

            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('entity_id', 'type_id')
                ->addAttributeToFilter(
                    'entity_id',
                    array('in' => $addProds)
                );

            foreach ($products as $product) {
                $this->_addProductToProfile($product, $profile->getId());
            }

            $deleteProds = array_merge($deleteProds, $this->products);
            $deleteProdsCount = count($deleteProds);
            if ($deleteProdsCount > 0) {
                $this->_deleteProductsFromProfile($deleteProds);
            }

            if (count($this->products) > 0) {
                $this->_addProductsToProfile($this->products, $profile->getId());
            }

            /*foreach($deleteProds as $oUid) {
                $this->_deleteProductFromProfile($oUid);
            }

            foreach ($addProds as $nRuid) {
                //add to current profile
                $this->_addProductToProfile($nRuid, $profile->getId());
            }*/

            $pcode = $profile->getProfileCode();
            //Mage::getSingleton('adminhtml/session')->getMessages(true);
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The profile has been successfully saved.'));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this profile.' . $e->getMessage() . ' File:' . $e->getFile() . ' Line: ' . $e->getLine()));
        }

        if ($redirectBack == 'edit') {
            $this->_redirect(
                '*/*/edit',
                array(
                    'back' => 'edit',
                    'tab' => $tab,
                    'id' =>  $profile->getId(),
                    'active_tab' => null,
                    'pcode' => $pcode,
                    'section' => 'jet_configuration',
                    'website' => $website,
                )
            );
        } else if ($redirectBack == 'upload') {
            $this->_redirect(
                'adminhtml/adminhtml_jetrequest/uploadproduct', array(
                'profile_id' => $profile->getId(),
                )
            );
        } else {
            $this->_redirect('*/*/');
        }

        return;
    }

    /**
     * Action for ajax request from assigned vendors grid
     *
     */
    public function editprofilegridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('jet/adminhtml_profile_edit_tab_products')->toHtml()
        );
    }

    /**
     * Remove vendor from group
     *
     * @param int $vendorId
     * @param string $groupCode
     * @return bool
     */
    protected function _deleteProductFromProfile($productId)
    {
        try {
            Mage::getModel("jet/profileproducts")
                ->deleteFromProfile($productId);
        } catch (Exception $e) {
            //throw $e;
            return false;
        }

        return true;
    }

    /**
     * Remove products from profile
     * @param array $productIds
     * @throws Exception $e
     * @return bool
     */
    protected function _deleteProductsFromProfile($productIds)
    {
        try {
            $return = Mage::getModel("jet/profileproducts")
                ->deleteProductsFromProfile($productIds);
            return $return;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Add products into profile
     * @param $productIds
     * @param $profileId
     * @return bool
     * @throws Exception $e
     */
    protected function _addProductsToProfile($productIds, $profileId)
    {
        try {
            $return = Mage::getModel("jet/profileproducts")
                ->addProductsToProfile($productIds, $profileId);
            return $return;
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Assign vendor to group
     *
     * @param int $vendorId
     * @param string $groupCode
     * @return bool
     */
    protected function _addProductToProfile($product, $profileId)
    {
        $profileproduct = Mage::getModel("jet/profileproducts");

        $productId = $product->getId();

        //remove children from old profile
        //add children to current profile
        if ($product->getTypeId() == 'configurable') {
            //removed from already assigned profile
            //$this->_deleteProductFromProfile($productId);

            $this->products[] = $productId;

            $childIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($productId);
            if (isset($childIds[0]))
                foreach ($childIds[0] as $id) {
                    $childProduct = Mage::getModel('catalog/product')->load($id);
                    $this->_addProductToProfile($childProduct, $profileId);
                }
        }

        //skip product if parent already exist in other profile
        if ($product->getTypeId() == 'simple') {
            $checkForChild = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
            if (!empty($checkForChild) && count($checkForChild) > 0) {
                $profileToProducts = $profileproduct->loadByField('product_id', $checkForChild[0])->getData();
                if (!empty($profileToProducts) && $profileToProducts['profile_id'] != $profileId) {
                    /* array('errors' => 'The Parent Product of the SKU is already assigned to Pofile . Please unassign it to continue.');*/
                    Mage::getSingleton('adminhtml/session')
                        ->addError('The Parent Product (ID - ' . $checkForChild[0] . ' ) of the SKU - ' . $product->getSku() . ' is already assigned to Pofile ID - ' . $profileToProducts['profile_id'] . '. Please unassign it to continue.');
                    return false;
                }
            }
        }


        if ($profileproduct->profileProductExists($productId, $profileId) === true) {
            return false;
        } else {
            //removed from already assigned profile
            //$this->_deleteProductFromProfile($productId);

            $this->products[] = $productId;

            // assign product to profile
            // $profileproduct->setProductId($productId);
            // $profileproduct->setProfileId($profileId);
            // $profileproduct->save();
            return true;
        }
    }

    public function updateCategoryAttributesAction()
    {

        $profileId = $this->getRequest()->getParam('profile_id');
        $p_id = $this->getRequest()->getParam('p_id');
        $c_id = $this->getRequest()->getParam('c_id');

        $collection = Mage::getModel('jet/profile')->getCollection()
            ->addFieldToFilter('id', $profileId)
            ->addFieldToFilter('profile_category_level_1', $p_id)
            ->addFieldToFilter('profile_category_level_2', $c_id);
        if (count($collection) > 0) {
            $profile = $collection->getFirstItem();
            Mage::register('current_profile', $profile);
        }

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('jet/adminhtml_profile_edit_tab_attributes')
                ->setPId($p_id)->setCId($c_id)->toHtml()
        );
    }


    public function nodeIdAttributesAction()
    {

        $profileId = $this->getRequest()->getParam('profile_id');
        $node_id = $this->getRequest()->getParam('node_id');


        $collection = Mage::getModel('jet/profile')
            ->getCollection()
            ->addFieldToFilter('id', $profileId)
            ->addFieldToFilter('node_id', $node_id);

        if (count($collection) > 0) {
            $profile = $collection->getFirstItem();
            Mage::register('current_profile', $profile);
        }

        $this->getResponse()->setBody(
            $this->getLayout()
                    ->createBlock('jet/adminhtml_profile_edit_tab_attributes')
                    ->setNodeId($node_id)->toHtml()
        );
    }

    /**
     * Vendor's product mass delete action
     */
    public function massDeleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            $ids = array();
            try {
                foreach ($this->getRequest()->getParam('id') as $id) {
                    $profile = Mage::getModel('jet/profile')->load($id);
                    if ($profile && $profile->getId()) {
                        $profile->delete();
                        $ids[] = $id;
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__("Jet Profiles deleted successfully"));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->getResponse()->setRedirect($this->_getRefererUrl());
    }

    /**
     * Delete vendor action
     */
    public function deleteAction()
    {
        $profile = false;
        if ($pcode = $this->getRequest()->getParam('pcode')) {
            $profile = Mage::getModel('jet/profile')->loadByField('profile_code', $pcode);
        }

        if ($profile->getId()) {
            try {
                $profile->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('jet')->__('Profile has been deleted.'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/');
    }


    /**
     * Update profile(s) status action
     *
     */
    public function massStatusAction()
    {
        $inline = $this->getRequest()->getParam('inline', 0);
        $profileIds = $this->getRequest()->getParam('id');
        $status = $this->getRequest()->getParam('status', '');
        if ($inline) {
            $profileIds = array($profileIds);
        }

        if (!is_array($profileIds)) {
            $this->_getSession()->addError($this->__('Please select profile(s)'));
        } else {
            try {
                foreach ($profileIds as $pId) {
                    $model = Mage::getModel('jet/profile')->load($pId);
                    if ($model && $model->getId() > 0) {
                        $model->setProfileStatus($status)->save();
                    }
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been updated.', count($profileIds))
                );
            } catch (Mage_Core_Model_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $this->__($e->getMessage() . ' An error occurred while updating the profile(s) status.'));
            }
        }

        $this->_redirect('*/*/index', array('_secure' => true));

    }

    /**
     * Grid Action
     * @return void
     *
     */
    public function profilegridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('jet/adminhtml_profile_grid')->toHtml());

    }

    /**
     * Validate product
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $profileData = $this->getRequest()->getPost();

        if (isset($profileData['profile_code'])) {
            $pcode = $profileData['profile_code'];
            $profile = Mage::getModel('jet/profile')->loadByField('profile_code', $pcode);
            if ($profile && $profile->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('jet')->__('Profile code with the same code already exists')
                );
                $this->_initLayoutMessages('adminhtml/session');
                $response->setError(true);
                $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
            }
        }

        $this->getResponse()->setBody($response->toJson());
    }
}