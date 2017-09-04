<?php
class Firebear_ConfigUrl_IndexController extends Mage_Core_Controller_Front_Action
{
    public function mediaAction()
    {
        if($this->getRequest()->isXmlHttpRequest()) {

            $product = Mage::getModel("catalog/product")
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($this->getRequest()->getParam('product_id'));

            $onlyMedia = $this->getRequest()->getParam('onlyMedia');

            Mage::register('current_product', $product);
            Mage::register('product', $product);

            $this->loadLayout('catalog_product_view');


            if (Mage::getStoreConfig('firebear_configurl/general/image_enabled')){
                $id = Mage::getStoreConfig('firebear_configurl/general/image_id');
                $block = Mage::app()->getLayout('catalog_product_view')->getBlock('product.info.media');
                $block_html = $block->toHtml();

                if (!strstr($block_html, 'placeholder')){
	                if (!empty($id) && !empty($block)){
	                    $result['update'][$id] = $block_html;
	                    $result['update']['image_id'] = $id;
	                }
	            }
            }

            if (!$onlyMedia){

                if (Mage::getStoreConfig('firebear_configurl/general/short_description_enabled')){
                    $id = Mage::getStoreConfig('firebear_configurl/general/short_description_id');
                    $data = $product->getShortDescription();
                    if (!empty($id) && !empty($data)){
                        $result['update'][$id] = $data;
                        $result['update']['short_description_id'] = $id;
                    }
                }

                if (Mage::getStoreConfig('firebear_configurl/general/description_enabled')){
                    $id = Mage::getStoreConfig('firebear_configurl/general/description_id');
                    $data = $product->getDescription();
                    if (!empty($id) && !empty($data)){
                        $result['update'][$id] = $data;
                        $result['update']['description_id'] = $id;
                    }
                }

                if (Mage::getStoreConfig('firebear_configurl/general/title_enabled')){
                    $id = Mage::getStoreConfig('firebear_configurl/general/title_id');
                    $data = $product->getName();
                    if (!empty($id) && !empty($data)){
                        $result['update'][$id] = '<h1>'.$data.'</h1>';
                        $result['update']['title_id'] = $id;
                    }
                }

            }

            //$result['update'][$custom1['block']] = $product->getData($custom1['attribute']);


            echo json_encode($result);

        } else {

            $this->norouteAction();
        }

        return;
    }
}