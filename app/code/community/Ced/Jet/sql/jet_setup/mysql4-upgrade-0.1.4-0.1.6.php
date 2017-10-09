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
 
$installer = $this; 

 
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');

$groupName = 'jetcom';
$entityTypeId = $installer->getEntityTypeId('catalog_product');
$attributeSetId= $installer->getDefaultAttributeSetId($entityTypeId);

$installer->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 100);
$attributeGroupId = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

$installer->endSetup();

