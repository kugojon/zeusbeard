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

class Ced_Jet_Helper_Catlist extends Mage_Core_Helper_Abstract
{
    public function catlist()
    {
        
            $csv = new Varien_File_Csv();
            $file = Mage::getBaseDir("var").DS."jetcsv".DS."Jet_Taxonomy.csv";     
            
            if(!file_exists($file)){
                Mage::getSingleton('adminhtml/session')->addError('Jet Extension Csv missing please check "Jet_Taxonomy.csv" exist at "var/jetcsv" location.');
                return;
            }

            $file1= Mage::getBaseDir("var").DS."jetcsv".DS."Jet_Taxonomy_mapping.csv";     
            
            if(!file_exists($file1)){
                Mage::getSingleton('adminhtml/session')->addError('Jet Extension Csv missing please check "Jet_Taxonomy_mapping.csv" exist at "var/jetcsv" location.');
                return;
            }
            
            
            $taxonomy = $csv->getData($file);
            unset($taxonomy[0]);
            // Check id exist in csv or not
            try{
                    $table ="";
                    $resource = "";
                    $writeConnection ="";
                    $resource = Mage::getSingleton('core/resource');
                    $table = $resource->getTableName('jet/catlist');
                    $writeConnection = $resource->getConnection('core_write');
                    
                foreach($taxonomy as $txt){
                    //if($txt[3]!='FALSE')
                    //{
                        $csv_cat_id=  number_format($txt[0], 0, '', ''); //trim($txt[0]);

                        $attribute_ids=$this->getattributesids($csv_cat_id);

                            if($attribute_ids!="")
                            {
                                $attributes_names=$this->getattributenames($attribute_ids);
                            }
                            else 
                            {
                                $attributes_names='';
                            }
                        
                        $csv_parent_id= number_format($txt[2], 0, '', ''); // trim($txt[2]);
                            
                        $query = 'INSERT INTO '.$table.' (csv_cat_id, name,csv_parent_id,level,jet_tax_code,attribute_ids,jetattr_names) VALUES ("'.$csv_cat_id.'", "'.$txt[1].'","'.$csv_parent_id.'","'.$txt[3].'","'.$txt[4].'","'.$attribute_ids.'","'.$attributes_names.'")';
                        $writeConnection->query($query);
                        unset($attribute_ids);
                    //}
                }

                Mage::getSingleton('adminhtml/session')->addSuccess("Successfully saved in table.");
                return;
            }catch(Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return;
            }
            
    }
    public function getattributesids($category_id="")
    {
            $attribute_ids=array();
            
            $csv= new Varien_File_Csv();
            $file= Mage::getBaseDir("var").DS."jetcsv".DS."Jet_Taxonomy_mapping.csv";     
            $taxonomy = $csv->getData($file);
            unset($taxonomy[0]);
            try{
                $comma_seperated_ids ='';
                foreach($taxonomy as $txt){
                    if($category_id==number_format($txt[0], 0, '', '')){
                        $comma_seperated_ids = $comma_seperated_ids.','.number_format($txt[1], 0, '', '');
                    }
                }

                $attrstring  = ltrim($comma_seperated_ids, ',');
                    return $attrstring;
            }catch(Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return '';
            }
    }
    public function getattributenames($attribute_id_cs)
    {

        $attribute_ids=array();
        $attribute_id_cs=explode(',', $attribute_id_cs);
        
        $csv= new Varien_File_Csv();
        $file= Mage::getBaseDir("var").DS."jetcsv".DS."Jet_Taxonomy_attribute.csv";
        $taxonomy = $csv->getData($file);
        unset($taxonomy[0]);
        $comma_seperated_ids=array();
        foreach ($attribute_id_cs as $idd=>$idd1)
        {
            try{
                foreach($taxonomy as $txt){
                    if($idd1==number_format($txt[0], 0, '', '')){
                        //var_dump($idd1);echo "<br/>";var_dump(number_format($txt[0],0,'',''));
                        
                            $comma_seperated_ids[] = $txt[2];
                    }
                }
            
                //return $attrstring;
            }catch(Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return '';
            }
        }

        $attrstring1  ="";
        if(count($comma_seperated_ids)>0){
            $attrstring1  = implode(',', $comma_seperated_ids);
        }else{
            $attrstring1  ="";
        }

        return $attrstring1;
    }


    public function getVariantAttributeNames()
    {
        $csv= new Varien_File_Csv();
        $file= Mage::getBaseDir("var").DS."jetcsv".DS."Jet_Taxonomy_attribute.csv";
        $taxonomy = $csv->getData($file);
        unset($taxonomy[0]);
        $variantAttributes = array();

        try{
            foreach($taxonomy as $txt){
                if($txt[4] == 'Yes' && $txt[3] == 'Yes'){
                    $variantAttributes[$txt[0]] = $txt[2];
                }
            }
        }catch(Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            //return '';
        }

        return $variantAttributes;
    }
    
}    
