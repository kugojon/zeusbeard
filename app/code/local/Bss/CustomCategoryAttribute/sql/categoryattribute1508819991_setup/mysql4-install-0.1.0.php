<?php
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
try {
    $setup->addAttribute('catalog_category', 'popup_box', array(
        'group' => 'General Information',
        'input' => 'select',
        'type' => 'int',
        'label' => 'Hide Newsletter Popup',
        'backend' => '',
        'visible' => true,
        'required' => false,
        'user_defined' => 1,
        'visible_on_front' => true,
        'source'   => 'eav/entity_attribute_source_boolean',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'default'  => 1
    ));
} catch (Exception $e) {
    Mage::logException($e);
}
$installer->endSetup();
	 