<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_ActivationController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
     $this->loadLayout(array('default'));

         $this->_addLeft($this->getLayout()
                ->createBlock('core/text')
                ->setText('
                    <h5>Predefined pages:</h5>
                    <ul>
                        <li>home</li>
						<li>home2</li>
						<li>home3</li>
						<li>home4</li>
						<li>home5</li>
						<li>no-route</li>
                    </ul><br />
                    <h5>Predefined static blocks:</h5>
                    <ul>
                        <li>harbour_footer</li>
						<li>harbour_footer2</li>
						<li>harbour_footer3</li>
						<li>harbour_product_custom</li>
						<li>harbour_product_banner</li>
						<li>harbour_sidebar_banner</li>
						<li>harbour_popup_content</li>
						<li>harbour_contact_map_block</li>
						<li>harbour_header2_text_banners</li>
						<li>new_arrivals</li>
						<li>bags</li>
						<li>hoodies</li>
						<li>harbour_phones</li>
						<li>harbour_right_menu</li>
						<li>harbour_header5_text_banner</li>
                    </ul><br />
                    <strong style="color:red;">To get more info regarding these blocks please read documentation that comes with this theme.</strong>'));
		$this->_addContent($this->getLayout()->createBlock('themeoptionsharbour/adminhtml_activation_edit'));
        $block = $this->getLayout()->createBlock('core/text')->setText('<strong style="color:red;">Activation feature is provided only for testing. Please do not use it on real stores! Make backup of your database every time when you use activation. </strong><br /><strong>Note:</strong> Please make sure you have at least 8 products marked as new to display homepage widgets correctly.');
        $this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
        	
        $stores = $this->getRequest()->getParam('stores', array(0));
        $activate_theme = $this->getRequest()->getParam('activate_theme', 0);
        $setup_pages = $this->getRequest()->getParam('setup_pages', 0);
        $setup_blocks = $this->getRequest()->getParam('setup_blocks', 0);

        try {

            if ($activate_theme) {
                foreach ($stores as $store) {
                    $scope = ($store ? 'stores' : 'default');

                    Mage::getConfig()->saveConfig('design/package/name', 'harbour', $scope, $store);
                    Mage::getConfig()->saveConfig('design/footer/copyright', 'Meigee &copy; 2014 <a href="http://meigeeteam.com" >Premium Magento Themes</a>', $scope, $store);
                }
            }

            if ($setup_pages) {
                Mage::getModel('ThemeOptionsHarbour/activation')->setupPages();
            }

            if ($setup_blocks) {
                Mage::getModel('ThemeOptionsHarbour/activation')->setupBlocks();
            }

            Mage::app()->cleanCache();
            $model = Mage::getModel('core/cache');
            $options = $model->canUse();
            foreach($options as $option=>$value) {
                $options[$option] = 0;
            }
            $model->saveOptions($options);

            $adminSession = Mage::getSingleton('admin/session');
            $adminSession->unsetAll();
            $adminSession->getCookie()->delete($adminSession->getSessionName());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ThemeOptionsHarbour')->__('An error occurred while activating theme. '.$e->getMessage()));
        }

        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
        }
    }
}