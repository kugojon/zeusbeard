<?php
/**
 * Call actions after configuration is saved
 */
class Meigee_ThemeOptionsHarbour_Model_Observer
{
	/**
     * After any system config is saved
     */
	public function cssgenerate()
	{
		$section = Mage::app()->getRequest()->getParam('section');

		if ($section == 'meigee_harbour_design')
		{
			Mage::getSingleton('ThemeOptionsHarbour/Cssgenerate')->saveCss();
			$response = Mage::app()->getFrontController()->getResponse();
			$response->sendResponse();
		}
		if ($section == 'meigee_harbour_appearance')
		{
			/* Presets: */
			/* Preset #1 */
			$preset1 = array(
				'meigee_harbour_appearance/presets/preset' => '99',
				'meigee_harbour_appearance/header/headertype' => '0',
				'meigee_harbour_appearance/header/transparent_header_home' => '1',
				'meigee_harbour_appearance/header/transparent_header_home_bg' => '1',
				'meigee_harbour_appearance/header/transparent_header_all' => '0',
				'meigee_harbour_appearance/layout/sitelayout' => '0',
				'web/default/cms_home_page' => 'home',
				'meigee_harbour_appearance/mycart/label' => '0',
				'meigee_harbour_appearance/logo/logo' => 'harbour_logo_white.png',
				'meigee_harbour_appearance/logo/logo_retina' => 'harbour_logo_white@x2.png',
				'meigee_harbour_appearance/logo/second_logo' => 'harbour_logo.png',
				'meigee_harbour_appearance/logo/second_logo_retina' => 'harbour_logo@x2.png',
				'meigee_harbour_appearance/footer/footer_static' => 'harbour_footer'
			);
			
			/* Preset #2 */
			$preset2 = array(
				'meigee_harbour_appearance/presets/preset' => '99',
				'meigee_harbour_appearance/header/headertype' => '0',
				'meigee_harbour_appearance/header/transparent_header_home' => '0',
				'meigee_harbour_appearance/header/transparent_header_all' => '0',
				'meigee_harbour_appearance/layout/sitelayout' => '1',
				'meigee_harbour_design/appearance/patern' => 'harbour',
				'web/default/cms_home_page' => 'home2',
				'meigee_harbour_appearance/mycart/label' => '0',
				'meigee_harbour_appearance/logo/logo' => 'harbour_logo.png',
				'meigee_harbour_appearance/logo/logo_retina' => 'harbour_logo@x2.png',
				'meigee_harbour_appearance/logo/second_logo' => 'harbour_logo.png',
				'meigee_harbour_appearance/logo/second_logo_retina' => 'harbour_logo@x2.png',
				'meigee_harbour_appearance/footer/footer_static' => 'harbour_footer'
			);
			
			/* Preset #3 */
			$preset3 = array(
				'meigee_harbour_appearance/presets/preset' => '99',
				'meigee_harbour_appearance/header/headertype' => '0',
				'meigee_harbour_appearance/header/transparent_header_home' => '1',
				'meigee_harbour_appearance/header/transparent_header_home_bg' => '2',
				'meigee_harbour_appearance/header/transparent_header_all' => '1',
				'meigee_harbour_appearance/layout/sitelayout' => '0',
				'web/default/cms_home_page' => 'home3',
				'meigee_harbour_appearance/mycart/label' => '0',
				'meigee_harbour_appearance/logo/logo' => 'harbour_logo_white.png',
				'meigee_harbour_appearance/logo/logo_retina' => 'harbour_logo_white@x2.png',
				'meigee_harbour_appearance/logo/second_logo' => 'harbour_logo_white.png',
				'meigee_harbour_appearance/logo/second_logo_retina' => 'harbour_logo_white@x2.png',
				'meigee_harbour_appearance/footer/footer_static' => 'harbour_footer2'
			);
			
			/* Preset #4 */
			$preset4 = array(
				'meigee_harbour_appearance/presets/preset' => '99',
				'meigee_harbour_appearance/header/headertype' => '1',
				'meigee_harbour_appearance/header/transparent_header_home' => '1',
				'meigee_harbour_appearance/header/transparent_header_home_bg' => '1',
				'meigee_harbour_appearance/header/transparent_header_all' => '1',
				'meigee_harbour_appearance/layout/sitelayout' => '0',
				'web/default/cms_home_page' => 'home',
				'meigee_harbour_appearance/mycart/label' => '1',
				'meigee_harbour_appearance/logo/logo' => 'harbour_logo_white.png',
				'meigee_harbour_appearance/logo/logo_retina' => 'harbour_logo_white@x2.png',
				'meigee_harbour_appearance/logo/second_logo' => 'harbour_logo_white.png',
				'meigee_harbour_appearance/logo/second_logo_retina' => 'harbour_logo_white@x2.png',
				'meigee_harbour_appearance/footer/footer_static' => 'harbour_footer'
			);
			
			/* Preset #5 */
			$preset5 = array(
				'meigee_harbour_appearance/presets/preset' => '99',
				'meigee_harbour_appearance/header/headertype' => '0',
				'meigee_harbour_appearance/header/transparent_header_home' => '1',
				'meigee_harbour_appearance/header/transparent_header_home_bg' => '1',
				'meigee_harbour_appearance/header/transparent_header_all' => '2',
				'meigee_harbour_appearance/layout/sitelayout' => '0',
				'web/default/cms_home_page' => 'home4',
				'meigee_harbour_appearance/mycart/label' => '0',
				'meigee_harbour_appearance/logo/logo' => 'harbour_logo_white.png',
				'meigee_harbour_appearance/logo/logo_retina' => 'harbour_logo_white@x2.png',
				'meigee_harbour_appearance/logo/second_logo' => 'harbour_logo_white.png',
				'meigee_harbour_appearance/logo/second_logo_retina' => 'harbour_logo_white@x2.png',
				'meigee_harbour_appearance/footer/footer_static' => 'harbour_footer'
			);
			
			
			/* Preset #6 */
			$preset6 = array(
				'meigee_harbour_appearance/presets/preset' => '99',
				'meigee_harbour_appearance/header/headertype' => '0',
				'meigee_harbour_appearance/header/transparent_header_home' => '0',
				'meigee_harbour_appearance/header/transparent_header_all' => '0',
				'meigee_harbour_appearance/layout/sitelayout' => '0',
				'web/default/cms_home_page' => 'home5',
				'meigee_harbour_appearance/mycart/label' => '0',
				'meigee_harbour_appearance/logo/logo' => 'harbour_logo.png',
				'meigee_harbour_appearance/logo/logo_retina' => 'harbour_logo@x2.png',
				'meigee_harbour_appearance/logo/second_logo' => 'harbour_logo.png',
				'meigee_harbour_appearance/logo/second_logo_retina' => 'harbour_logo@x2.png',
				'meigee_harbour_appearance/footer/footer_static' => 'harbour_footer3'
			);
			
			/* Preset #7 */
			$preset7 = array(
				'meigee_harbour_appearance/presets/preset' => '99',
				'meigee_harbour_appearance/header/headertype' => '1',
				'meigee_harbour_appearance/header/transparent_header_home' => '0',
				'meigee_harbour_appearance/header/transparent_header_all' => '0',
				'meigee_harbour_appearance/layout/sitelayout' => '0',
				'web/default/cms_home_page' => 'home5',
				'meigee_harbour_appearance/mycart/label' => '1',
				'meigee_harbour_appearance/logo/logo' => 'harbour_logo.png',
				'meigee_harbour_appearance/logo/logo_retina' => 'harbour_logo@x2.png',
				'meigee_harbour_appearance/logo/second_logo' => 'harbour_logo.png',
				'meigee_harbour_appearance/logo/second_logo_retina' => 'harbour_logo@x2.png',
				'meigee_harbour_appearance/footer/footer_static' => 'harbour_footer3'
			);
			/* //Presets */
			
			$scope = Mage::app()->getRequest()->getParam('store');
			global $scope_id;
			$scope_id = Mage::getModel('core/store')->load($scope)->getId();
			if(!$scope_id){
				$scope_id = 0;
			}
			
			/* Write to DB */
			function configWriter($configs){
				global $scope_id;
				$appearanceSwitch = new Mage_Core_Model_Config();
				foreach($configs as $section => $value){
					if($scope_id){
						$appearanceSwitch->saveConfig($section, $value, 'stores', $scope_id);
					}else{
						$appearanceSwitch->saveConfig($section, $value, 'default', 0);
					}
				}
			}
			/* Remove from DB */
			function configRestore($presets){
				global $scope_id;
				$resource = Mage::getSingleton('core/resource');
				$writeConnection = $resource->getConnection('core_write');
				$dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
				foreach($presets as $preset){
					foreach($preset as $section => $value){
						$query = "DELETE FROM `".$dbname."`.`core_config_data` WHERE scope_id = ".$scope_id." AND path = '".$section."'";
						$writeConnection->query($query);
					}
				}
				$query = "DELETE FROM `".$dbname."`.`core_config_data` WHERE scope_id = ".$scope_id." AND path = 'meigee_harbour_appearance/presets/preset'";
				$writeConnection->query($query);
			}
			
			/* Option Switcher */
			switch(Mage::getStoreConfig('meigee_harbour_appearance/presets/preset', $scope_id)){
				case 99: //Do nothing
					
				break;
				case 1: //Restore Defaults
					configRestore(array($preset1, $preset2, $preset3, $preset4, $preset5, $preset6, $preset7));
				break;
				case 2: //Preset #1
					configWriter($preset1);
				break;
				case 3: //Preset #2
					configWriter($preset2);
				break;
				case 4: //Preset #3
					configWriter($preset3);
				break;
				case 5: //Preset #4
					configWriter($preset4);
				break;
				case 6: //Preset #5
					configWriter($preset5);
				break;
				case 7: //Preset #6
					configWriter($preset6);
				break;
				case 8: //Preset #7
					configWriter($preset7);
				break;
			};
		}
		if ($section == 'meigee_harbour_general' || $section == 'meigee_harbour_appearance' || $section == 'meigee_harbour_design' || $section == 'meigee_harbour_productpage') {
			$blocks = array(
					'bags',
					'harbour_contact_map_block',
					'harbour_footer2',
					'harbour_footer3',
					'harbour_footer',
					'harbour_header_phone',
					'harbour_right_menu',
					'harbour_header2_text_banners',
					'harbour_header5_text_banner',
					'harbour_popup_content',
					'harbour_product_banner',
					'harbour_product_custom',
					'harbour_sidebar_banner',
					'hoodies',
					'new_arrivals');
			$pages = array (
				'home',
				'home2',
				'home3',
				'home4',
				'home5');

			foreach ($blocks as $block) {
				if (!Mage::getModel('cms/block')->load($block)->getIsActive()) {
					$message .= $block . '<br />';
				}
			}
			$existpages = array ();
			foreach (Mage::getModel('cms/page')->getCollection()->toOptionArray() as $value=>$label) {
				$existpages[] = $label['value'];
			};

			$message2 = implode("<br />",array_diff($pages, $existpages));

			if ($message || $message2) {

				$html = "Some of static blocks or pages weren't created so some store elements can be displayed incorrectly. Please create them manually. For more info please read the user guide that comes with the theme.<br />";
    			if ($message) {
	    			$html .= " Missed Static Blocks:<p style='color: red;'>{$message}</p>";
	    		}
	    		if ($message2) {
	    			$html .= " Missed Pages:<p style='color: red;'>{$message2}</p>";
	    		}
				Mage::getSingleton('core/session')->addWarning($html);

			}
		}
	}
}