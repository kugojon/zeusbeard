<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("customer", "acc_phone",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Phone/Ext",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_phone");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_fax",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Fax",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_fax");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_bname",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Business Name",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_bname");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_cemail",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Company Email",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_cemail");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_cphone",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Company Phone",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_cphone");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_cfax",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Company Fax",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_cfax");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_tnumber",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Tax ID Number",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_tnumber");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_cwebsite",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Company Website",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_cwebsite");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_kbusiness",  array(
    "type"     => "text",
    "backend"  => "",
    "label"    => "Kind of Business",
    "input"    => "multiselect",
    "source"   => "newattcustomer/eav_entity_attribute_source_customeroptions14707404749",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_kbusiness");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_mcountry",  array(
    "type"     => "int",
    "backend"  => "",
    "label"    => "Multiple Countries",
    "input"    => "select",
    "source"   => "eav/entity_attribute_source_boolean",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_mcountry");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_wcountry",  array(
    "type"     => "text",
    "backend"  => "",
    "label"    => "Which countries",
    "input"    => "textarea",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_wcountry");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_sonline",  array(
    "type"     => "int",
    "backend"  => "",
    "label"    => "Sell Online",
    "input"    => "select",
    "source"   => "eav/entity_attribute_source_boolean",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_sonline");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer", "acc_wonline",  array(
    "type"     => "text",
    "backend"  => "",
    "label"    => "Web Address(es)",
    "input"    => "textarea",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "acc_wonline");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	
$installer->endSetup();
	 