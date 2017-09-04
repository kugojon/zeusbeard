<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Helper_Retina extends Mage_Core_Helper_Abstract
{
	public function getRetinaData ($data, $_item=0, $_itemparameter=0) {
 		$helpImg = MAGE::helper('ThemeOptionsHarbour/images');
 		if (Mage::getStoreConfig('meigee_harbour_general/retina/status')) {
			$appearance = MAGE::helper('ThemeOptionsHarbour')->getThemeOptionsHarbour('meigee_harbour_appearance');
			$logo = $appearance['logo'];
			$html ='';
			switch ($data) {
				case 'default_logo':
					$html = 'data-srcX2="' . Mage::getDesign()->getSkinUrl('images/@x2/logo@x2.png') . '"';
				break;
				case 'logo':
    				$mediaurl = MAGE::helper('ThemeOptionsHarbour')->getThemeOptionsHarbour('mediaurl');
					$html = 'data-srcX2="' . $mediaurl.$logo['logo_retina'] . '"';
				break;
				case 'second_logo':
    				$mediaurl = MAGE::helper('ThemeOptionsHarbour')->getThemeOptionsHarbour('mediaurl');
					$html = 'data-srcX2="' . $mediaurl.$logo['second_logo_retina'] . '"';
				break;
				case 'default_small_logo':
					$html = 'data-srcX2="' . Mage::getDesign()->getSkinUrl('images/@x2/small_logo@x2.png') . '"';
				break;
				case 'small_logo':
    				$mediaurl = MAGE::helper('ThemeOptionsHarbour')->getThemeOptionsHarbour('mediaurl');
					$html = 'data-srcX2="' . $mediaurl.$logo['small_logo_retina'] . '"';
				break;
				default:
					# code...
					break;
			}
			return $html;
		}
		else return false;
	}
}