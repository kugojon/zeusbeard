<?php
include_once("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class Bss_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $productId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->_filterStockData($data['product']['stock_data']);
            $yt_url = $data['product']['video'];
            if($yt_url) {
                $url_parsed_arr = parse_url($yt_url);
                $check = $url_parsed_arr['host'] == "www.youtube.com" && $url_parsed_arr['path'] == "/watch" && substr($url_parsed_arr['query'], 0, 2) == "v=" && substr($url_parsed_arr['query'], 2) != "";
                if (!$check) {
                    $this->_getSession()->addError($this->__('Not a valid YouTube link'));
                    $this->_redirect('*/*/edit', array(
                        'id' => $productId,
                        '_current' => true
                    ));
                    return;
                }
            }

            $product = $this->_initProductSave();
            // check sku attribute
            $productSku = $product->getSku();
            if ($productSku && $productSku != Mage::helper('core')->stripTags($productSku)) {
                $this->_getSession()->addError($this->__('HTML tags are not allowed in SKU attribute.'));
                $this->_redirect('*/*/edit', array(
                    'id' => $productId,
                    '_current' => true
                ));
                return;
            }

            try {
                $product->save();
                $productId = $product->getId();

                if (isset($data['copy_to_stores'])) {
                    $this->_copyAttributesBetweenStores($data['copy_to_stores'], $product);
                }

                $this->_getSession()->addSuccess($this->__('The product has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $productId,
                '_current'=>true
            ));
        } elseif($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                '_current'   => true,
                'id'         => $productId,
                'edit'       => $isEdit
            ));
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }
}
?>