<?php

/**
 * Magmodules.eu - http://www.magmodules.eu.
 *
 * NOTICE OF LICENSE
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.magmodules.eu/MM-LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magmodules.eu so we can send you a copy immediately.
 *
 * @category      Magmodules
 * @package       Magmodules_Richsnippets
 * @author        Magmodules <info@magmodules.eu>
 * @copyright     Copyright (c) 2017 (http://www.magmodules.eu)
 * @license       https://www.magmodules.eu/terms.html  Single Service License
 */
class Magmodules_Snippets_Block_Adminhtml_Widget_Info_Info
    extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $magento_version = Mage::getVersion();
        $module_version = Mage::getConfig()->getNode()->modules->Magmodules_Snippets->version;
        $logo_link = '//www.magmodules.eu/logo/reviewemail/' . $module_version . '/' . $magento_version . '/logo.png';

        $html = '<div style="background:url(\'' . $logo_link . '\') no-repeat scroll 15px center #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 200px;">
            <h4>About Magmodules.eu</h4>
                <p>We are a Magento only E-commerce Agency located in the Netherlands.<br>
                <br />
                <table width="500px" border="0">
                    <tr>
                        <td width="58%">View more extensions from us:</td>
                        <td width="42%">
                          <a href="http://www.magentocommerce.com/magento-connect/developer/Magmodules" 
                          target="_blank">Magento Connect</a>
                        </td>
                    </tr>
                    <tr>
                        <td>For Help:</td>
                        <td>
                          <a href="https://www.magmodules.eu/support.html?ext=rich-snippets">Visit our Support Page</a>
                        </td>
                    </tr>
                    <tr>
                        <td height="30">Visit our website:</td>
                        <td><a href="http://www.magmodules.eu" target="_blank">www.magmodules.eu</a></td>
                    </tr>
                    <tr>
                        <td height="30"><strong>Need help?</strong></td>
                        <td>
                           <strong>
                            <a href="http://www.magmodules.eu/help/rich-snippets" target="_blank">Online manual</a>
                           </strong>
                         </td>
                    </tr>
                </table>
            </div>';
        return $html;
    }

}