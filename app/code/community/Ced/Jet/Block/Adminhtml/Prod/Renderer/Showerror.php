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

class Ced_Jet_Block_Adminhtml_Prod_Renderer_Showerror extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
 
    public function render(Varien_Object $row) 
    {


                    $id=$row->getId();
                    $profile = Mage::getModel('jet/profileproducts')->loadByField('product_id', $id);
                    $view_url = $this->getUrl('adminhtml/adminhtml_jetrequest/productDetails', array('id'=>$id, 'profile_id' => $profile->getProfileId()));
                    $editurl = $this->getUrl('adminhtml/catalog_product/edit', array('id'=>$id));
                    $html='<a href="'.$view_url.'">View</a>';
                    $html= $html.' |&nbsp;&nbsp;<a target="_blank" href="'.$editurl.'">Edit</a>';
                    
                    $batch_id=Mage::helper('jet')->getBatchIdFromProductId($id);
                    $error="";
                    $date="";
                    $date1="";
                    if($batch_id){
                        $batchmod="";
                        $batchmod=Mage::getModel('jet/batcherror')->load($batch_id);
                        $error=$batchmod->getData('error');
                        $date=$batchmod->getData('date_added');
                        $date1=Mage::app()->getLocale()->date(
                            $date,
                            Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                            null, false
                        );
                    }

                    if(strlen($error)>0){
                                $errorhtml="";
                                $url=$this->getUrl('adminhtml/adminhtml_jetajax/errordetails', array('id'=>$id));
                                //$errorhtml='<a title="" onclick="showerror'.$id.'('."'".$url."'".')" href="#">Last Upload Error: '.$date1.'</a>';
                                $errorhtml='<a title="" onclick="showerror'.$id.'('."'".$url."'".')" href="#">Log</a>';
                            $newhtml='<script type="text/javascript">function showerror'.$id.'(sUrl) {
										    oPopup = new Window({
																	id:"popup_window",
																			className: "magento",
																			windowClassName: "popup-window",
															    title: "Last Occurred Error Details",
																url: sUrl,
																width: 750,
																height: 200,
																minimizable: false,
																maximizable: false,
																destroyOnClose: true,
																showEffectOptions: {}
																});
																						oPopup.setZIndex(100);
																						oPopup.showCenter(true);
										}
								</script>';
                                $errorhtml=$errorhtml.$newhtml;
                                $html=$html." | ".$errorhtml;
                    }

                        return $html;
     
    }
}
