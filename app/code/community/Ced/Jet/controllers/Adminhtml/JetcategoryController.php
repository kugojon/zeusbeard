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
class Ced_Jet_Adminhtml_JetCategoryController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return true;
    }

    public function categorylistAction()
    {
        /*$response_simple = Mage::helper('jet')->CGetRequest(rawurlencode('/files/e534ecf5ecbb4e7aba824023fadaa2a1'));
        print_r($response_simple);die();
        Mage::getModel('jet/observer')->updateInvcron();die('check feed file now');*/
        $sku = Mage::app()->getRequest()->getParam('sku');
        if(isset($sku))
        {
            $response_simple = Mage::helper('jet')->CGetRequest('/merchant-skus/' . rawurlencode($sku));

            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody($response_simple);

        }
        else{
            $this->loadLayout();
            $this->renderLayout();
        }


    }

    protected function syncCategory($url, $categoryId = null)
    {
        $data = Mage::helper('jet');
        if (!is_null($categoryId) and is_numeric($categoryId)) {
            $url =  '/taxonomy/nodes/'.$categoryId;
        }

        $response = $data->CGetRequest($url);
       
        $jetCategory = json_decode($response, true);
        if (isset($jetCategory['jet_node_id'])) {
            $id = $jetCategory['jet_node_id'];
            $parentId = isset($jetCategory['parent_id']) ? $jetCategory['parent_id'] : "0";
            $name = $jetCategory['jet_node_name'];
            $path = isset($jetCategory['jet_node_path']) ? $jetCategory['jet_node_path'] : $name;
            $taxCode = isset($jetCategory['suggested_tax_code']) ?
                $jetCategory['suggested_tax_code'] : 'General Taxable Product';
            $status = isset($jetCategory['active'])? ($jetCategory['active']==false?"0":$jetCategory['active']): "0";
            $level = isset($jetCategory['jet_level']) ? $jetCategory['jet_level'] : "0";

            $categoryModel = Mage::getModel('jet/catlist')
                ->load($id, 'csv_cat_id');
            $categoryModel->setData('csv_cat_id', $id);
            $categoryModel->setData('csv_parent_id', $parentId);
            $categoryModel->setData('name', $name);
            $categoryModel->setData('path', $path);
            $categoryModel->setData('jet_tax_code', $taxCode);
            $categoryModel->setData('status', (string)$status);
            $categoryModel->setData('level', $level);
            $categoryModel->save();
            return true;
            //Mage::log(date("H:i:s")." ".$id, null, 'jet_category_sync.log',true);
        }

        return false;
    }

    public function synccategoryAction()
    {
        $categoryId = $this->getRequest()->getParam('csv_cat_id');
        $this->syncCategory(null, $categoryId);
        $this->_redirect('*/adminhtml_jetcategory/categorylist');
    }

    public function syncattributesAction()
    {
        $data = Mage::helper('jet');
        $categories = Mage::getModel('jet/catlist')->getCollection();
        foreach ($categories as $category) {
            $url ='/taxonomy/nodes/' .$category->getCsvCatId(). "/attributes";
            $attributes = $data->CGetRequest($url);
            if (preg_match('/attributes/', $attributes)) {
                $categoryModel = Mage::getModel('jet/catlist')
                    ->load($category->getCsvCatId(), 'csv_cat_id');
                if ($categoryModel) {
                    $categoryModel->setData('attributes', $attributes);
                    $categoryModel->save();
                }
            }
        }

        $this->_redirect('*/adminhtml_jetcategory/categorylist');
    }

    public function syncattributeAction()
    {
        $categoryId = $this->getRequest()->getParam('csv_cat_id');
        if (is_numeric($categoryId) and $categoryId !== 0) {
            $data = Mage::helper('jet');
            $url ='/taxonomy/nodes/' .$categoryId. "/attributes";

            $attributes = $data->CGetRequest($url);

            if (preg_match('/attributes/', $attributes)) {
                $categoryModel = Mage::getModel('jet/catlist')
                ->load($categoryId, 'csv_cat_id');
                if ($categoryModel) {
                    $categoryModel->setData('attributes', $attributes);
                    $categoryModel->save();
                }
            }
        }

        $this->_redirect('*/adminhtml_jetcategory/categorylist');
    }

    public function syncAction()
    {
        Mage::log(date("H:i:s")." Entry", null, 'jet_category_sync.log', true);
        $data = Mage::helper('jet');
        $offsets = array('0', '999', '1999', '2999', '3999', '4999', '5999', '6999', '7999', '8999', '9999');
        foreach ($offsets as $offset) {
            $jetCategoryUrls = $this->getCategoryUrls($offset);
            if (isset($jetCategoryUrls['node_urls'])) {
                foreach ($jetCategoryUrls['node_urls'] as $jetCategoryUrl) {
                   $this->syncCategory($jetCategoryUrl);
                }
            }
        }

        Mage::log(date("H:i:s")." Exit", null, 'jet_category_sync.log', true);
        $this->_redirect('*/adminhtml_jetcategory/categorylist');
    }

    protected function getCategoryUrls($offset = 0)
    {
        try {
            $response = array();
            $data = Mage::helper('jet');
            $version = "1.02";
            $url = sprintf('/taxonomy/links/%s?offset=%s', $version, $offset);
            $response = $data->CGetRequest($url);
            $response = json_decode($response, true);
            return $response;
        } catch (\Exception $e) {
            return $response;
        }
    }

    //@todo fix
    public function editAction()
    {

        if ($this->getRequest()->getParam('id')) {
            $id = $this->getRequest()->getParam('id');
            $model = "";
            $attribute_ids = "";
            $model = Mage::getModel('jet/catlist')->load($id);
            $attribute_ids = $model->getData('attribute_ids');
            if ($attribute_ids == "") {
                Mage::getSingleton('adminhtml/session')->addError('No Attributes present for selected Category.');
                $this->_redirect('*/*/categorylist');
                return;
            }

            $attribute = array();
            $attribute = explode(',', $attribute_ids);
            $csv = new Varien_File_Csv();
            $file = Mage::getBaseDir("var") . DS . "jetcsv" . DS . "Jet_Taxonomy_attribute_value.csv";
            $file1 = Mage::getBaseDir("var") . DS . "jetcsv" . DS . "Jet_Taxonomy_attribute.csv";
            if (!file_exists($file1)) {
                Mage::getSingleton('adminhtml/session')->addError('Jet Extension Csv missing please check "Jet_Taxonomy_attribute.csv" exist at "var/jetcsv" location.');
                $this->_redirect('*/*/categorylist');
                return;
            }

            if (!file_exists($file)) {
                Mage::getSingleton('adminhtml/session')->addError('Jet Extension Csv missing please check "Jet_Taxonomy_attribute_value.csv" exist at "var/jetcsv" location.');
                $this->_redirect('*/*/categorylist');
                return;
            }

            $taxonomy1 = $csv->getData($file1);
            unset($taxonomy1[0]);
            $taxonomy = $csv->getData($file);
            unset($taxonomy[0]);
            $details = array();
            foreach ($taxonomy1 as $txt1) {
                $field = "";
                $field = trim($txt1[0]);
                if (in_array($field, $attribute)) {
                    $details[$field]['name'] = trim($txt1[2]);
                    $details[$field]['description'] = trim($txt1[1]);
                    $details[$field]['free_text'] = trim($txt1[3]);
                    $details[$field]['attr_value'] = '';
                    $details[$field]['units'] = '';
                    foreach ($taxonomy as $txt) {
                        $field1 = "";
                        $field1 = trim($txt[0]);
                        if ($field == $field1) {
                            if ($details[$field]['attr_value'] == "") {
                                if (trim($txt[1]) != "") {
                                    $details[$field]['attr_value'] = trim($txt[1]);
                                }
                            } else {
                                if (trim($txt[1]) != "") {
                                    $details[$field]['attr_value'] = $details[$field]['attr_value'] . ',' . trim($txt[1]);
                                }
                            }

                            if ($details[$field]['units'] == "") {
                                if (trim($txt[2]) != "") {
                                    if ($txt[2] != 'NULL') {
                                        $details[$field]['units'] = trim($txt[2]);
                                    }
                                }
                            } else {
                                if (trim($txt[2]) != "") {
                                    $details[$field]['units'] = $details[$field]['units'] . ',' . trim($txt[2]);
                                }
                            }
                        }
                    }
                }
            }

            $collection = "";
            $collection = new Varien_Data_Collection();

            foreach ($details as $key => $value) {
                $attr_coll = "";
                $magento_attr_id = '';
                $status = "Not Created";
                $attr_coll = Mage::getModel('jet/jetattribute')->getCollection()->addFieldToFilter('jet_attr_id', $key);
                if (count($attr_coll) > 0) {
                    foreach ($attr_coll as $at) {
                        $magento_attr_id = $at->getData('magento_attr_id');
                        break;
                    }
                }

                if ($magento_attr_id != "") {
                    $status = "Created";
                }

                $thing_1 = "";
                $thing_1 = new Varien_Object();
                $thing_1->setId($key);
                $thing_1->setMagentoid($magento_attr_id);
                $thing_1->setStatus($status);
                $thing_1->setCategory($id);
                $thing_1->setName($value['name']);
                $thing_1->setDescription($value['description']);
                $thing_1->setFreetext($value['free_text']);
                $thing_1->setAttrvalue($value['attr_value']);
                $thing_1->setUnits($value['units']);
                $collection->addItem($thing_1);
            }

            Mage::getSingleton('adminhtml/session')->setData('attr_collection', $collection);
            $this->loadLayout();
            $this->renderLayout();
        }

    }

}  
