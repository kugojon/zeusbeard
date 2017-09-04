<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

// Change current directory to the directory of current script
chdir(dirname(__FILE__));

require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
}

// Only for urls
// Don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);

if (!defined('MAGENTO_ROOT')) {
    define('MAGENTO_ROOT', getcwd());
}

$time = Mage::getSingleton('core/date')->gmtTimestamp();
$istalledAt = strtotime(Mage::getConfig()->getNode('global/install/date'));

if ($time < $istalledAt + 3600) { ?>
    <h4>Impossible to detect. This Magento instant was installed less than an hour ago. Please try later.</h4>
<?php } else {

    $cronSchedule = Mage::getSingleton('cron/schedule');

    $collection = $cronSchedule->getCollection()
        ->addFieldToFilter('scheduled_at', array('gteq' => date('Y-m-d H:i:s', $time - 3600)))
        ->setPageSize(1);

    if (!count($collection)) {
    ?>
        <h4>Magento cron job is MISSING in your crontab. (Error)</h4>
        In UNIX/BSD/linux systems you will need to add this line (or a similar line) to your crontab:<br/>
        <strong>0,5,10,15,20,25,30,35,40,45,50,55 * * * * /bin/sh <?php echo MAGENTO_ROOT ?>/cron.sh</strong>
        <br/><br/>
        For this you can use command "crontab -e" in shell, or use hosting panel.
        <br/><br/>
        <hr/>
        <h5>Note that if you recently set up a cron job and still see an error, please for wait 30 minutes and try again.</h5>

    <?php } else { ?>
        <h4>Magento cron job is installed in your crontab. (OK)</h4>

        <?php
        $collection = $cronSchedule->getCollection()
            ->addFieldToFilter('executed_at', array('gteq' => date('Y-m-d H:i:s', $time - 3600 * 2)))
            ->addFieldToFilter(
                array('status', 'status'),
                array(
                    array('eq'=>Mage_Cron_Model_Schedule::STATUS_MISSED),
                    array('eq'=>Mage_Cron_Model_Schedule::STATUS_ERROR),
                )
            )
            ->setPageSize(100);

        if (count($collection)) { ?>


        <h5>However, you still have some problems:</h5>
        <table cellspacing="0" cellpadding="5" border="1">
            <tr>
                <?php foreach($collection->getFirstItem()->getData() as $key => $value) { ?>
                <td><?php echo htmlspecialchars($key) ?></td>
                <?php } ?>
            </tr>
            <?php foreach($collection as $item) { ?>
                <tr>
                <?php foreach($item->getData() as $value) { ?>
                    <td><?php echo htmlspecialchars($value) ?></td>
                <?php } ?>
                </tr>
            <?php } ?>
        </table>

        <?php } else { ?>
            <h4>Cron works well. (OK)</h4>
        <?php } ?>
    <?php } ?>
<?php } ?>

<br/>
<a href="https://store.plumrocket.com/" target="_blank" title="Plumrocket Inc Magento Store - Unique Magento Extensions and High Quality Magento Development">
    Plumrocket Inc. &#169; 2008 - <?php echo date('Y') ?>
</a>