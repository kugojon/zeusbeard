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
class Ced_Jet_Adminhtml_JetattrlistController extends Mage_Adminhtml_Controller_Action
{
    public $_count = 0;
    protected function _isAllowed()
    {
        return true;
    }
    public function jetattributeAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function jetcronAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function gridAction()
    {
        $this->loadLayout();
              $this->getResponse()->setBody(
                  $this->getLayout()->createBlock('jet/adminhtml_jetcron_grid')->toHtml()
              );
     }

    /*
    * All below function Not necessary becuase Only Category Mapping allowed
    */
    /*
    public function masscreateAction(){
                $this->_count=0;
                $ids=array();
                $ids=$this->getRequest()->getParam('ids');
                if(count($ids)<=0){
                        Mage::getSingleton('adminhtml/session')->addError('No category ids found.');
                        $this->_redirect('adminhtml/adminhtml_jetattrlist/categorylist');
                        return;
                }
                $error = false;
                $store_def = "";
                $parentId ='';
                $store_def = Mage::getStoreConfig('jet_options/ced_jet/jet_storeid');
                if(empty($store_def) || $store_def==null || $store_def==''){
                        $error = true;
                        Mage::getSingleton('adminhtml/session')->addError('Please select the store from Jet Configration settings for which Jet category will be created.');
                        $this->_redirect('adminhtml/adminhtml_jetattrlist/categorylist');
                        return;
                }

                $parentId = Mage::app()->getStore($store_def)->getRootCategoryId();
                $Jetcate_name= 'Jet.com Category';
                $cdata ='';
                $cdata = $this->parentCate_exist($parentId,$Jetcate_name);
                if($cdata!=false){
                            $parentId = $cdata;
                }else{
                            //-1 means this category is no jet node so it is not needed to be saved in jet_categordy attribute table
                            $parentId = $this->Categoryhelper($Jetcate_name,$parentId, -1);
                }
                $err_msg="";
                $i=0;
                foreach($ids as $id){
                        $model="";
                        $data="";
                        $flag=false;
                        $model=Mage::getModel('jet/catlist')->load($id);
                        $data=$model->getData();
                        $flag=$this->checkCategoryExists($data['csv_cat_id']);
                        if($flag){
                                //$model->setData('created_category','1');
                                //$model->save();
                                $err_msg=$err_msg." Category '".$data['name']."'".' already created or mapped to magento category.<br/>';
                                $i++;
                                continue;
                        }
                        $first_leval_id=-1;
                        $second_level_id = -1;
                        $data['path'] = mb_convert_encoding($data['path'], 'UTF-8', 'UTF-8');
                        $explode_dum = explode("|",$data['path']);
                        $totalCount= count($explode_dum);

                        if($totalCount==3){

                            $CCNAME1= trim($explode_dum[0]);
                            $CCNAME2= trim($explode_dum[1]);
                            $CCNAME3= trim($explode_dum[2]);

                            $Rdata = $this->parentCate_exist($parentId,$CCNAME1);
                            if($Rdata==false){
                                $p_flag=false;
                                $p_flag=$this->createParentCategory($CCNAME1,$parentId);
                                if($p_flag==false){
                                    Mage::getSingleton('adminhtml/session')->addError('Some error occured in Creating Parent Category '.$CCNAME1.'.');
                                    break;
                                }else{
                                    $Rdata = $this->parentCate_exist($parentId,$CCNAME1);
                                    $this->savedata($data['csv_cat_id'],$Rdata);
                                    $first_leval_id = $Rdata;
                                    //$this->createItsArributes($data['csv_cat_id']);
                                    $this->addAttributeToTable($data['csv_cat_id']);
                                    $this->_count++;
                                }

                            }else{
                                $this->savedata($data['csv_cat_id'],$Rdata);
                                $first_leval_id = $Rdata;
                                $this->_count++;
                                $this->addAttributeToTable($data['csv_cat_id']);
                                //$this->createItsArributes($data['csv_cat_id']);
                            }

                            if(!empty($first_leval_id) && $first_leval_id!=null){

                                $Rdata2 = $this->parentCate_exist($first_leval_id,$CCNAME2);
                                if($Rdata2!=false){
                                    $this->savedata($data['csv_parent_id'],$Rdata2);
                                    $this->_count++;
                                    $second_level_id = $Rdata2;
                                    $this->addAttributeToTable($data['csv_parent_id']);
                                    //$this->createItsArributes($data['csv_parent_id']);
                                }else{
                                    $second_level_id = $this->Categoryhelper($CCNAME2,$first_leval_id, $data['csv_parent_id']);
                                    //$this->createItsArributes($data['csv_parent_id']);
                                    $this->addAttributeToTable($data['csv_parent_id']);
                                    $this->_count++;

                                }

                                if(!empty($second_level_id) && $second_level_id!=null){
                                    $Rdata3 = $this->parentCate_exist($second_level_id,$CCNAME3);
                                    if($Rdata3!=false){
                                        $this->savedata($data['csv_cat_id'],$Rdata3);
                                        $this->_count++;
                                        $this->addAttributeToTable($data['csv_cat_id']);
                                        //$this->createItsArributes($data['csv_cat_id']);
                                    }else{
                                         $this->Categoryhelper($CCNAME3,$second_level_id, $data['csv_cat_id']);
                                        $this->_count++;
                                        $this->addAttributeToTable($data['csv_cat_id']);
                                         //$this->createItsArributes($data['csv_cat_id']);
                                    }
                                }
                            }

                        }else if($totalCount==2){
                            $CCNAME1= trim($explode_dum[0]);
                            $CCNAME2= trim($explode_dum[1]);
                            $Rdata = $this->parentCate_exist($parentId,$CCNAME1);
                            if($Rdata!=false){
                                $this->savedata($data['csv_cat_id'],$Rdata);
                                $first_leval_id = $Rdata;
                                $this->_count++;
                                $this->addAttributeToTable($data['csv_cat_id']);
                                //$this->createItsArributes($data['csv_cat_id']);
                            }else{
                                $first_leval_id = $this->Categoryhelper($CCNAME1,$parentId, $data['csv_parent_id']);
                                //$this->createItsArributes($data['csv_cat_id']);
                                $this->addAttributeToTable($data['csv_parent_id']);
                                $this->_count++;
                            }

                            if($first_leval_id!=null && !empty($first_leval_id)){
                                    $R1data = $this->parentCate_exist($first_leval_id,$CCNAME2);
                                    if($R1data!=false){
                                        $this->savedata($data['csv_cat_id'],$R1data);
                                        //$this->createItsArributes($data['csv_cat_id']);
                                        $this->addAttributeToTable($data['csv_cat_id']);
                                        $this->_count++;
                                    }else{
                                        $this->Categoryhelper($CCNAME2,$first_leval_id, $data['csv_cat_id']);
                                        //$this->createItsArributes($data['csv_cat_id']);
                                        $this->addAttributeToTable($data['csv_cat_id']);
                                        $this->_count++;
                                    }
                                }

                        }else{
                            if($totalCount==1){
                                $CCNAME1= trim($explode_dum[0]);
                                $Rdata = $this->parentCate_exist($parentId,$CCNAME1);
                                if($Rdata!=false){
                                    $this->savedata($data['csv_cat_id'],$Rdata);
                                    $this->addAttributeToTable($data['csv_cat_id']);
                                    //$this->createItsArributes($data['csv_cat_id']);
                                    $this->_count++;
                                }else{
                                    $this->Categoryhelper($CCNAME1,$parentId, $data['csv_cat_id']);
                                    //$this->createItsArributes($data['csv_cat_id']);
                                    $this->addAttributeToTable($data['csv_cat_id']);
                                    $this->_count++;
                                }
                            }
                        }

                        $first_leval_id=-1;
                        $second_level_id = -1;
                }
                // to create attribute of categories created/


                if($i>0){
                        Mage::getSingleton('adminhtml/session')->addError($err_msg);
                }
                if($this->_count>0){
                    Mage::getSingleton('adminhtml/session')->addSuccess('Your '.$this->_count.' category has been created successfully');
                }
                $this->_redirect('adminhtml/adminhtml_jetattrlist/categorylist');
                return;

    }
    */
    /*
    * All below function Not necessary becuase Only Category Mapping allowed
    */
    /*
    public function parentCate_exist($currentCategoryId = null, $cat_name){
        if($currentCategoryId!=null){
            $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('parent_id', $currentCategoryId)
            ->addAttributeToFilter('name', $cat_name);
            if ($collection->getSize() == 1) {
                $id="";
                $id=$collection->getFirstItem()->getId();
                $this->searchCategory($id);
                return $collection->getFirstItem()->getId();
            }
            else{
                return false;
            }

        }else{
            return false;
        }


    }
    */
    /*
    * All below function Not necessary becuase Only Category Mapping allowed
    */
    /*
    public function Categoryhelper($name = null , $parent = null,$jet_node = null){
        if(isset($name) && $name!=null){
            $category = new Mage_Catalog_Model_Category();
            $category->setName($name);
            $category->setIsActive(1);
            $category->setIncludeInMenu(0);
            $category->setIsAnchor(0);

            $category->setJetCategoryId($jet_node);
            $category->setIsJetCategory(1);

            $parentCategory = Mage::getModel('catalog/category')->load($parent);
            $category->setPath($parentCategory->getPath());
            $category->save();

            $Retrun_Id = $category->getId();
            if($jet_node!=-1){ // if category name is 'Jet Category' then no need to save in database table
                $this->savedata($jet_node,$Retrun_Id);
            }
            return $Retrun_Id;
        }else{
            return null;
        }
    }
    */
    /*
    * All below function Not necessary becuase Only Category Mapping allowed
    */
    /*
    public function savedata($jet_cate_id,$mage_id){
        if($this->fetchdata($mage_id)==false){

            $vald = number_format($jet_cate_id,0,'','');
            $model = Mage::getModel('jet/jetcategory');
            $model ->setJetCateId($vald);
            $model ->setMagentoCatId($mage_id);
            $model ->setIsCsvCategory(1);
            $model->save();
            $coll='';
            $id="";

            $coll=Mage::getModel('jet/catlist')->getCollection()->addFieldToFilter('csv_cat_id',$vald);

            foreach($coll as $collection){
                    $id=$collection->getId();
                    break;
            }

            if($id!=""){
                $mod="";
                $mod=Mage::getModel('jet/catlist')->load($id);
                $mod->setData('created_category','1');
                $mod->save();
            }
            unset($vald);
            unset($mage_id);
        }
    }
    */
    /*
    * All below function Not necessary becuase Only Category Mapping allowed
    */
    /*

    public function fetchdata($mage_id=""){
        if($mage_id==""){
            return false;
        }
        $model = Mage::getModel('jet/jetcategory') ->getCollection()->addFieldToFilter('magento_cat_id',(int)$mage_id);
        return	$model->getData('jet_cate_id');
    }
    public function checkCategoryExists($jet_cat_id=""){
        $jet_cat_id=trim($jet_cat_id);
        if($jet_cat_id==""){
            return false;
        }
        $coll ="";
        $coll = Mage::getModel('jet/jetcategory') ->getCollection()->addFieldToFilter('jet_cate_id',$jet_cat_id);
        foreach ($coll as $model) {
                if($model->getData('magento_cat_id')!="" && $model->getData('magento_cat_id')!=0){
                    $this->addAttributeToTable($jet_cat_id);
                    return true;
                }
        }
        return false;
    }
    public function createParentCategory($cat_name="",$parentId=""){
            $cat_name=trim($cat_name);
            if($cat_name==""){
                    return false;
            }
            if($parentId==""){
                    return false;
            }
            $coll="";
            $coll=Mage::getModel('jet/catlist')->getCollection()->addFieldToFilter('name',$cat_name)->addFieldToFilter('path',$cat_name);
            foreach($coll as $model){
                    $id="";
                    $flag=false;
                    $id=$model->getId();
                    $flag=$this->createParent($id,$parentId);
                    $mod="";

                    if($flag==false){
                        return false;
                    }else{
                        $mod=Mage::getModel('jet/catlist')->load($id);
                        $mod->setData('created_category','1');
                        $mod->save();
                        return true;
                    }
                    break;
            }
            return false;
    }
    public function createParent($id="",$parentId=""){
            if($id==""){
                    return false;
            }
            if($parentId==""){
                    return false;
            }
            $model="";
            $data="";
            $flag=false;
            $model=Mage::getModel('jet/catlist')->load($id);
            $data=$model->getData();
            $flag=$this->checkCategoryExists($data['csv_cat_id']);
            if($flag){
                    $model->setData('created_category','1');
                    $model->save();
                    return true;

            }
            $first_leval_id=-1;
            $second_level_id = -1;
            $data['path'] = mb_convert_encoding($data['path'], 'UTF-8', 'UTF-8');
            $explode_dum = explode("|",$data['path']);
            $totalCount= count($explode_dum);
            if($totalCount==3){
                            $CCNAME1= trim($explode_dum[0]);
                            $CCNAME2= trim($explode_dum[1]);
                            $CCNAME3= trim($explode_dum[2]);

                            $Rdata = $this->parentCate_exist($parentId,$CCNAME1);

                            if($Rdata==false){
                                $par_flag=false;
                                $par_flag=$this->createParentCategory($CCNAME1,$parentId);
                                if($par_flag==false){
                                        return false;
                                }else{
                                    $Rdata = $this->parentCate_exist($parentId,$CCNAME1);
                                    $this->savedata($data['csv_cat_id'],$Rdata);
                                    $first_leval_id = $Rdata;
                                    $this->_count++;
                                    $this->addAttributeToTable($data['csv_cat_id']);
                                    //$this->createItsArributes($data['csv_cat_id']);
                                }
                            }else{
                                $this->savedata($data['csv_cat_id'],$Rdata);
                                $first_leval_id = $Rdata;
                                $this->_count++;
                                $this->addAttributeToTable($data['csv_cat_id']);
                                //$this->createItsArributes($data['csv_cat_id']);
                            }

                            if(!empty($first_leval_id) && $first_leval_id!=null){

                                $Rdata2 = $this->parentCate_exist($first_leval_id,$CCNAME2);
                                if($Rdata2!=false){
                                    $this->savedata($data['csv_parent_id'],$Rdata2);
                                    $second_level_id = $Rdata2;
                                    $this->_count++;
                                    $this->addAttributeToTable($data['csv_parent_id']);
                                    //$this->createItsArributes($data['csv_parent_id']);
                                }else{
                                    $second_level_id = $this->Categoryhelper($CCNAME2,$first_leval_id, $data['csv_parent_id']);
                                    $this->_count++;
                                    $this->addAttributeToTable($data['csv_parent_id']);
                                    //$this->createItsArributes($data['csv_parent_id']);
                                }

                                if(!empty($second_level_id) && $second_level_id!=null){
                                    $Rdata3 = $this->parentCate_exist($second_level_id,$CCNAME3);
                                    if($Rdata3!=false){
                                        $this->savedata($data['csv_cat_id'],$Rdata3);
                                        $this->addAttributeToTable($data['csv_cat_id']);
                                        $this->_count++;
                                        //$this->createItsArributes($data['csv_cat_id']);
                                    }else{
                                         $this->Categoryhelper($CCNAME3,$second_level_id, $data['csv_cat_id']);
                                        $this->_count++;
                                        $this->addAttributeToTable($data['csv_cat_id']);
                                        // $this->createItsArributes($data['csv_cat_id']);
                                    }
                                }
                            }

            }else if($totalCount==2){
                            $CCNAME1= trim($explode_dum[0]);
                            $CCNAME2= trim($explode_dum[1]);
                            $Rdata = $this->parentCate_exist($parentId,$CCNAME1);
                            if($Rdata!=false){
                                $this->savedata($data['csv_cat_id'],$Rdata);
                                $first_leval_id = $Rdata;
                                $this->_count++;
                                $this->addAttributeToTable($data['csv_cat_id']);
                                //$this->createItsArributes($data['csv_cat_id']);
                            }else{
                                $first_leval_id = $this->Categoryhelper($CCNAME1,$parentId, $data['csv_parent_id']);
                                $this->_count++;
                                $this->addAttributeToTable($data['csv_parent_id']);
                                //$this->createItsArributes($data['csv_cat_id']);
                            }

                            if($first_leval_id!=null && !empty($first_leval_id)){
                                    $R1data = $this->parentCate_exist($first_leval_id,$CCNAME2);
                                    if($R1data!=false){
                                        $this->savedata($data['csv_cat_id'],$R1data);
                                        $this->_count++;
                                        $this->addAttributeToTable($data['csv_cat_id']);
                                        //$this->createItsArributes($data['csv_cat_id']);
                                    }else{
                                        $this->Categoryhelper($CCNAME2,$first_leval_id, $data['csv_cat_id']);
                                        $this->_count++;
                                        $this->addAttributeToTable($data['csv_cat_id']);
                                        //$this->createItsArributes($data['csv_cat_id']);
                                    }
                                }

            }else{
                            if($totalCount==1){
                                $CCNAME1= trim($explode_dum[0]);

                                $Rdata = $this->parentCate_exist($parentId,$CCNAME1);
                                if($Rdata!=false){
                                    $this->savedata($data['csv_cat_id'],$Rdata);
                                    $this->_count++;
                                    $this->addAttributeToTable($data['csv_cat_id']);
                                    //$this->createItsArributes($data['csv_cat_id']);
                                }else{
                                    $return_id=$this->Categoryhelper($CCNAME1,$parentId, $data['csv_cat_id']);
                                    $this->_count++;
                                    $this->addAttributeToTable($data['csv_cat_id']);
                                    //$this->createItsArributes($data['csv_cat_id']);
                                }
                            }
            }
            return true;

    }
    public function addAttributeToTable($jet_id=""){

            $jet_id=trim($jet_id);
            $attribute_ids="";
            $id="";
            if($jet_id !=""){
                    $coll="";
                    $coll=Mage::getModel('jet/catlist')->getCollection()->addFieldToFilter('csv_cat_id',$jet_id);
                    foreach($coll as $coll1){
                            $attribute_ids=$coll1->getData('attribute_ids');
                            break;
                    }
                    $collection="";
                    $collection=Mage::getModel('jet/jetcategory')->getCollection()->addFieldToFilter('jet_cate_id',$jet_id);
                    foreach($collection as $com){
                        $id=$com->getId();
                        break;
                    }
                    $id=trim($id);
                    $attribute_ids1=implode(',',$attribute_ids);
                    if($id !="" && $attribute_ids1 !=""){
                            $model="";
                            $model=Mage::getModel('jet/jetcategory')->load($id);
                            $model->setData('jet_attributes',$attribute_ids1);
                            $model->save();
                    }
            }
    }
    public function searchCategory($id){
        $id=trim($id);
        $jet_id="";
        if($id !=""){
                $collection="";
                $collection=Mage::getModel('jet/jetcategory')->getCollection()->addFieldToFilter('magento_cat_id',$id);
                foreach($collection as $com){
                            $jet_id=$com->getData('jet_cate_id');
                            break;
                }
                $this->addAttributeToTable($jet_id);
        }

    }
    */
}
