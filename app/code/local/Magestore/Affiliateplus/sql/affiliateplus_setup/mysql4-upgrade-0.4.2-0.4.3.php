<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('affiliateplus_account')}
  ADD COLUMN `add_urlso` varchar(255) NOT NULL default '',
  ADD COLUMN `website_url` varchar(255) NOT NULL default '',
  ADD COLUMN `social_facebook` varchar(255) NOT NULL default '',
  ADD COLUMN `social_instagram` varchar(255) NOT NULL default '',
  ADD COLUMN `other` varchar(255) NOT NULL default ''; 

");
$installer->getConnection()->resetDdlCache($this->getTable('affiliateplus_account'));

$installer->endSetup();