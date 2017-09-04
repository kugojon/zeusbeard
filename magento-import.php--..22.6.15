<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *\
 * MagentoCommerce/Order Manager integration script					  *
 * (C) Stone Edge Technologies Inc.  All rights reserved.             *
 *                                                                    *
 * Processes requests for data transfer between MagentoCommerce       *
 * shopping cart and Stone Edge Order Manager.          			  *
 * By Mark Setzer, October 2008 for use with Magento 1.x.	      	  *
 *                                                                    *
 \* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

// PHP 5.2 or higher?
if (version_compare(phpversion(), '5.2.0', '<')===true) {
    echo 'SETIError: Magento integration requires PHP version 5.2.0 or newer. Please contact your webhost to resolve this issue.';
	exit;	
}

// Load Magento core
$mageFilename = 'app/Mage.php';

if (!file_exists($mageFilename)) {
	echo 'SETIError: Could not locate "app" directory or load Magento core files. Please check your script installation and try again.';
	exit;
}
require_once $mageFilename;
// Call script dispatcher and exit
$debug = isset($_REQUEST['debug']) && ($_REQUEST['debug'] == 'true');
// 2013-07-24:Add reindex support
$reindex = isset($_REQUEST['reindex']) && ($_REQUEST['reindex'] == 'true');
$dssMode = isset($_REQUEST['rundssmode']) && ( (strtolower($_REQUEST['rundssmode']) == '1') || (strtolower($_REQUEST['rundssmode']) == 'true')    ); 
$handler = new StoneEdge_MagentoImport($debug, $dssMode, $reindex);
$handler->dispatcher();
exit;

final class StoneEdge_MagentoImport {
	private static $_debug, $_storeId, $_dssMode, $_reindex;
	const SCRIPT_VERSION = 1.243;
	const ZEND_DATE_FORMAT = 'dd MMM YYYY HH:mm:ss';

	const QOH_RESULT_NOT_FOUND = 'NF';
	const QOH_RESULT_OK = 'OK';
	const QOH_RESULT_NA = 'NA';
	
	public function __construct($debug, $dssMode,$reindex) {
		self::$_debug = $debug;
		self::$_dssMode = $dssMode;
		self::$_reindex = $reindex;
	}
	
	public function dispatcher() {
		try {
			$function = (isset($_REQUEST['setifunction']) ? strtolower($_REQUEST["setifunction"]) : '');
			
			if ($function == 'sendversion') { 
				self::sendversion(); 
			} elseif (method_exists($this, $function)) {
	 			if (self::getStore()) { call_user_func(array($this, $function)); }
			} else {
				echo 'SETIError: Function "'.$function.'" was not found.';
			}	
		} catch (Exception $e) {
			echo "SETIError: Unexpected error, can't continue!\r\nDetails:\r\n$e";
		}
	}

	private static function sendversion() {
		echo 'SETIResponse: version=' . self::SCRIPT_VERSION;
	}
	
	private static function getStore() {
		$user = (isset($_REQUEST['setiuser']) ? $_REQUEST['setiuser'] : ''); 
		$pass = (isset($_REQUEST['password']) ? $_REQUEST['password'] : '');
		$code = (isset($_REQUEST['code']) ? $_REQUEST['code'] : '');

		try {
			$allStores = Mage::app()->getStores();
			foreach ($allStores as $_eachStoreId => $val) 
			{
			$_storeCode = Mage::app()->getStore($_eachStoreId)->getCode();
			$_storeName = Mage::app()->getStore($_eachStoreId)->getName();
			$_storeId = Mage::app()->getStore($_eachStoreId)->getId();
			/*echo $_storeId;
			echo $_storeCode;
			echo $_storeName;*/
			}
			if (self::$_debug) { echo "Acquiring app handle for store code '$code'...\r\n"; }
			$app = Mage::app($code);
			self::$_storeId = $app->getStore()->getData('store_id');
			if (self::$_debug) { echo "Using store id '"  . self::$_storeId ."'.\r\n"; }		
		} catch (Exception $e) {
			echo "SETIError: Couldn't open store connection. Please check your username, password and code (if provided) and try again. (Details: $e )";
			return false;
		}

		$admin = Mage::getModel('admin/user');
		if (!$admin->authenticate($user, $pass)) { 	
			echo 'SETIError: Access denied. Please check your login and store code (if provided).';
			return false;
		} 
		
		return true;
	}
	
	private static function getOrderEntityId($db, $sql, DateTime $lastDate, $lastOrder) {
		$res = Mage::getSingleton('core/resource');
		$ordersTable = $res->getTableName('sales/order');
				
		$sql = $db->select()
				  ->from($ordersTable, 'entity_id')
				  ->where('store_id=?', self::$_storeId, Zend_Db::INT_TYPE)
				  ->where("increment_id = '$lastOrder'") // OR updated_at >= '{$lastDate->format('Y-m-d')}'")
				  ->order('entity_id');
		if (self::$_debug) { echo 'Executing SQL: '.$sql->__toString()."\r\n"; }
		$entityId = (int)$db->fetchOne($sql);
		return $entityId;
	}
	
	private static function ordercount() {
		$lastDate = new DateTime();
		$lastOrder = ((isset($_REQUEST['lastorder']) && strtolower($_REQUEST['lastorder']) != 'all') ? $_REQUEST['lastorder'] : 0);
		$lastDate = ((isset($_REQUEST['lastdate']) && strtolower($_REQUEST['lastdate']) != 'all') ? date_create($_REQUEST['lastdate']) : date_create(date('Y-m-d')));

		$res = Mage::getSingleton('core/resource'); $ordersTable = $res->getTableName('sales/order');
		$db = $res->getConnection('sales_read'); 
		$lastEntityId = 0; $sql = new Varien_Db_Select($db);
		if ($lastOrder) { $lastEntityId = self::getOrderEntityId($db, $sql, $lastDate, $lastOrder); }
		$sql = $db->select()
				  ->from($ordersTable, 'COUNT(*)')
				  ->where('store_id=?', self::$_storeId, Zend_Db::INT_TYPE)
				  ->where("entity_id > $lastEntityId"); // OR updated_at >= '{$lastDate->format('Y-m-d')}'");
		if (self::$_debug) { echo "Executing SQL: $sql\r\n"; }
		$orderCount = $db->fetchOne($sql);
		echo "SETIResponse: ordercount=$orderCount";
	}
	
	private static function getcustomerscount() {
		$res = Mage::getSingleton('core/resource');
		$db = $res->getConnection('customer_read');
		$custTable = $res->getTableName('customer/entity');
		$sql = $db->select()
				  ->from($custTable, 'COUNT(*)')
			  	  ->where('store_id=?', self::$_storeId);
		$custCount = $db->fetchOne($sql);
		echo "SETIResponse: itemcount=$custCount";
	}
	
	private static function getproductscount() {		
        $res = Mage::getSingleton('core/resource');
        $db = $res->getConnection('catalog_read');	
        
        $productTable = $res->getTableName('catalog/product');
        $sql = $db->select()
        		  ->from($productTable, 'COUNT(*)');
        $productCount = $db->fetchOne($sql);
		echo "SETIResponse: itemcount=$productCount";
	}

	private static function downloadcustomers() {
		$startnum =  (isset($_REQUEST['startnum']) && ((int)$_REQUEST['startnum'] > 0) ? (int)$_REQUEST['startnum'] - 1 : 0);
		$batchsize = (isset($_REQUEST['batchsize']) && (int)$_REQUEST['batchsize'] > 0 ? $_REQUEST['batchsize'] : 100);

		$res = Mage::getSingleton('core/resource');
		$db = $res->getConnection('customer_read');
		$custTable = $res->getTableName('customer/entity');
		
		$sql = $db->select()
				  ->from($custTable, array('entity_id', 'email'))
				  ->where('store_id=?', self::$_storeId, Zend_Db::INT_TYPE)
				  ->limit($batchsize, $startnum);
		if (self::$_debug) { echo "Executing SQL: $sql\r\n"; }
		$custRows = $db->fetchAll($sql);

		$xd = new DOMDocument("1.0", "UTF-8");
		if (sizeof($custRows) == 0) {
			// no products
			$ndCustomers = self::writeResponse($xd, 'Customers', 2);	
		} else {
			$ndCustomers = self::writeResponse($xd, 'Customers');
			foreach ($custRows as $custRow) {
				$custId = $custRow["entity_id"];
				$email = $custRow["email"];
				$cust = Mage::getModel('customer/customer')->load($custId);

				$ndCust = $xd->createElement("Customer");
				self::xmlAppend("WebID", $cust->getEntityId(), $ndCust, $xd);
				if ($cust->getPrimaryBillingAddress()) {
					$ndBill = $xd->createElement("BillAddr");
					self::writeCustAddress($ndBill, $cust->getPrimaryBillingAddress(), $email, $xd); 
					$ndCust->appendChild($ndBill);
				}
				if ($cust->getPrimaryShippingAddress()) { 
					$ndShip = $xd->createElement("ShipAddr");
					self::writeCustAddress($ndShip, $cust->getPrimaryShippingAddress(), '', $xd); 
					$ndCust->appendChild($ndShip);
				} 
				$ndCustomers->appendChild($ndCust);
			}
		}
		if (!self::$_debug) { header('Content-type: text/xml'); }
		$xd->appendChild($ndCustomers);
		echo $xd->saveXML();
	}
	
	private static function writeCustAddress(DOMElement $ndAddr, Mage_Customer_Model_Address $addr, 
			$email, DOMDocument $xd) {
		
		$street = explode("\n", $addr->getStreetFull());
		self::xmlAppend("FirstName", $addr->getData('firstname'), $ndAddr, $xd);
		self::xmlAppend("LastName", $addr->getData('lastname'), $ndAddr, $xd);
		if ($email != '') { self::xmlAppend("Email", $email, $ndAddr, $xd); }
		self::xmlAppend("Company", $addr->getData('company'), $ndAddr, $xd);
		self::xmlAppend("Phone", $addr->getData('telephone'), $ndAddr, $xd);
		self::xmlAppend("Fax", $addr->getData('fax'), $ndAddr, $xd);
		if (sizeof($street) > 0) { self::xmlAppend("Addr1", $street[0], $ndAddr, $xd); }
		if (sizeof($street) > 1) { self::xmlAppend("Addr2", $street[1], $ndAddr, $xd); }
		self::xmlAppend("City", $addr->getData('city'), $ndAddr, $xd);
		self::xmlAppend("State", $addr->getRegionCode(), $ndAddr, $xd);
		self::xmlAppend("Zip", $addr->getData('postcode'), $ndAddr, $xd);
		self::xmlAppend("Country", $addr->getData('country_id'), $ndAddr, $xd);		
	}
	
	private static function downloadprods() {
		self::writeAllProds(false);
	}

	private static function downloadqoh() {
		self::writeAllProds(true);
	}

	private static function writeAllProds($qohOnly = false) {
		$startnum =  (isset($_REQUEST['startnum']) && ((int)$_REQUEST['startnum'] > 0) ? (int)$_REQUEST['startnum'] - 1 : 0);
		$batchsize = (isset($_REQUEST['batchsize']) && (int)$_REQUEST['batchsize'] > 0 ? $_REQUEST['batchsize'] : 100);
		
		$res = Mage::getSingleton('core/resource');
        $db = $res->getConnection('catalog_read');	     
        $productTable = $res->getTableName('catalog/product');
		$sql = $db->select()
				  ->from($productTable, array('entity_id', 'sku'))
				  ->limit($batchsize, $startnum);
		if (self::$_debug) { echo "Executing SQL: $sql\r\n"; }
		$prodRows = $db->fetchAll($sql);

		$xd = new DOMDocument("1.0", "UTF-8");
		if (sizeof($prodRows) == 0) {
			// no products
			$ndProds = self::writeResponse($xd, 'Products', 2);	
		} else {
			$ndProds = self::writeResponse($xd, 'Products');
			foreach ($prodRows as $prodRow) {
				$prodId = $prodRow['entity_id'];
				$sku = $prodRow['sku'];
				if (!$qohOnly) { $prod = Mage::getModel('catalog/product')->load($prodId); } else { $prod = new Mage_Catalog_Model_Product; }
				$item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($prodId);		
				$ndProd = $xd->createElement("Product");
				self::writeProduct($ndProd, $prod, $item, $sku, $qohOnly, $xd);
				$ndProds->appendChild($ndProd);			
			}
		}
		if (!self::$_debug) { header('Content-type: text/xml'); }
		$xd->appendChild($ndProds);
		echo $xd->saveXML();
	}
	
	private static function writeProduct(DOMElement $ndProd, Mage_Catalog_Model_Product $prod, 
			Mage_CatalogInventory_Model_Stock_Item $item, $sku, $qohOnly, DOMDocument $xd) {
		
		self::xmlAppend("Code", $sku, $ndProd, $xd);
		self::xmlAppend("QOH", $item->getData('qty'), $ndProd, $xd);
		
		if (!$qohOnly) {
			self::xmlAppend("WebID", $prod->getId(), $ndProd, $xd);
			self::xmlAppend("Name", $prod->getName(), $ndProd, $xd);
			self::xmlAppend("Price", $prod->getPrice(), $ndProd, $xd);
			self::xmlAppend("Description", $prod->getData('short_description'), $ndProd, $xd);
			self::xmlAppend("Weight", $prod->getData('weight'), $ndProd, $xd);
	
			// custom options
			$options = $prod->getOptions();
			if (is_array($options) && sizeof($options) > 0) { 
				$ndOpt = $xd->createElement("OptionLists");
				foreach ($options as $option) {
					$ndProdOpt = $xd->createElement("ProductOption");
					self::writeProductOption($ndProdOpt, $prod, $item, $option, $xd);
					$ndOpt->appendChild($ndProdOpt);
				}
				$ndProd->appendChild($ndOpt);
			}
		}
	}
	
	private static function writeProductOption(DOMElement $ndOpt, Mage_Catalog_Model_Product $prod, 
			Mage_CatalogInventory_Model_Stock_Item $item, Mage_Catalog_Model_Product_Option $option,
			DOMDocument $xd) {
		
		self::xmlAppend("WebID", $option->getId(), $ndOpt, $xd);
		self::xmlAppend("Name", $option->getData('title'), $ndOpt, $xd);

		foreach ($option->getValues() as $optId => $optValue) {
			if ($optValue->getData('title') == '') { continue; }
			$ndOptVal = $xd->createElement("OptionValue");

			self::xmlAppend("Name", $optValue->getData('title'), $ndOptVal, $xd);
			
			$hasCode = ($optValue->getData('sku') != '');
			if ($hasCode) { self::xmlAppend("Code", $optValue->getData('sku'), $ndOptVal, $xd); }
			
			$hasPrice = ((float)$optValue->getData('price') != 0);
			if ($hasPrice) {
				if ($optValue->getData('price_type') == 'fixed') {	
					self::xmlAppend("Price", $optValue->getData('price'), $ndOptVal, $xd);
				} else {
					self::xmlAppend("Price", (float)($optValue->getdata('price') * $prod->getPrice), $ndOptVal, $xd);	
				}
			}
			$ndOpt->appendChild($ndOptVal);
		}
	}

	private static function downloadorders() {
		$lastDate = new DateTime();
		$startnum =  (isset($_REQUEST['startnum']) && ((int)$_REQUEST['startnum'] > 0) ? (int)$_REQUEST['startnum'] - 1 : 0);
		$batchsize = (isset($_REQUEST['batchsize']) && (int)$_REQUEST['batchsize'] > 0 ? $_REQUEST['batchsize'] : 100);
		$lastOrder = ((isset($_REQUEST['lastorder']) && strtolower($_REQUEST['lastorder']) != 'all') ? $_REQUEST['lastorder'] : 0);
		$lastDate = ((isset($_REQUEST['lastdate']) && strtolower($_REQUEST['lastdate']) != 'all') ? date_create($_REQUEST['lastdate']) : date_create(date('Y-m-d')));
		
		$res = Mage::getSingleton('core/resource'); $ordersTable = $res->getTableName('sales/order');
		$db = $res->getConnection('sales_read'); 
		$lastEntityId = 0; $sql = new Varien_Db_Select($db);
		if ($lastOrder) { $lastEntityId = self::getOrderEntityId($db, $sql, $lastDate, $lastOrder); }
		$sql = $db->select()
				  ->from($ordersTable, 'entity_id')
				  ->where('store_id=?', self::$_storeId, Zend_Db::INT_TYPE)
				  ->where("entity_id > $lastEntityId") // OR updated_at >= '{$lastDate->format('Y-m-d')}'")
				  ->limit($batchsize, $startnum);
		if (self::$_debug) { echo "Executing SQL: $sql\r\n"; }
		$ordRows = $db->fetchAll($sql);
		$xd = new DOMDocument("1.0", "UTF-8");
		if (sizeof($ordRows) == 0) {
			// no new orders :(
			$ndOrders = self::writeResponse($xd, 'Orders', 2);	
		} else {
			$ndOrders = self::writeResponse($xd, 'Orders');
			foreach ($ordRows as $ordRow) {
				$orderId = $ordRow['entity_id'];
				$order = Mage::getModel('sales/order')->load($orderId);
				$ndOrd = $xd->createElement("Order");
				if (self::writeOrder($ndOrd, $order, $xd)) { 
					$ndOrders->appendChild($ndOrd); 
				}
			}
		}
		if (!self::$_debug) { header('Content-type: text/xml'); }
		$xd->appendChild($ndOrders);

		echo $xd->saveXML();
	}
	
	private static function writeOrder(DOMElement $ndOrder, Mage_Sales_Model_Order $order, DOMDocument $xd) {
		self::xmlAppend("OrderNumber", $order->getData('increment_id'), $ndOrder, $xd);

		$orderDate = new DateTime();
		$orderDate = date_create($order->getData('created_at'));
		self::xmlAppend("OrderDate", $orderDate->format('d-M-Y g:i:s A'), $ndOrder, $xd);
				
		if (!$order->getBillingAddress()) { 
			if (self::$_debug) { echo "Order {$order->getData('increment_id')} was missing a billing address, skipping."; }
			return false; 
		}
		$ndBill = $xd->createElement("Billing");
		self::writeOrderAddress($ndBill, $order->getBillingAddress(), $order->getData('customer_email'), $xd);
		$ndOrder->appendChild($ndBill);
		
		$ndShip = $xd->createElement("Shipping");
		if (!$order->getShippingAddress()) { $addr = $order->getBillingAddress(); } else { $addr = $order->getShippingAddress(); }
		self::writeOrderAddress($ndShip, $addr, '', $xd);

		foreach ($order->getAllItems() as $orderItem) {
			if ($orderItem->getData('product_type') == 'configurable') { continue; }
			$ndProd = $xd->createElement("Product");
			self::writeOrderItem($ndProd, $orderItem, $order, $xd);
			$ndShip->appendChild($ndProd);
		}
		$ndOrder->appendChild($ndShip);
		
		self::writeOrderPayment($ndOrder, $order, $xd);
		
		$ndTot = $xd->createElement("Totals");
		self::writeOrderTotals($ndTot, $order, $xd);
		$ndOrder->appendChild($ndTot);
		
		self::writeOrderAdjustments($ndOrder, $order, $xd);
		
		$ndOther = $xd->createElement("Other");
			self::xmlAppend("IPHostName", $order->getData('remote_ip'), $ndOther, $xd);
			self::xmlAppend("TotalOrderWeight", $order->getData('weight'), $ndOther, $xd);
			self::xmlAppend("GiftMessage", self::getGiftMessage($order), $ndOther, $xd);
			self::xmlAppend("Comments", $order->getData('customer_note'), $ndOther, $xd);
		$ndOrder->appendChild($ndOther);
		return true;
	}
	
	private static function writeOrderAddress(DOMElement $nd, Mage_Sales_Model_Order_Address $addr, $email = '', DOMDocument $xd) {
		self::xmlAppend("FullName", $addr->getName(), $nd, $xd);
		self::xmlAppend("Company", $addr->getData('company'), $nd, $xd);
		self::xmlAppend("Email", $email, $nd, $xd);
		self::xmlAppend("Phone", $addr->getData('telephone'), $nd, $xd);
		
		$ndAddr = $xd->createElement("Address");
			$country = $addr->getCountry();
			if ($country != 'US') { $state = $addr->getData('region'); } else { $state = $addr->getRegionCode(); }
		
			$street = explode("\n", $addr->getStreetFull());
			self::xmlAppend("Street1", $street[0], $ndAddr, $xd);
			if (sizeof($street) > 1) { self::xmlAppend("Street2", $street[1], $ndAddr, $xd); }
			self::xmlAppend("City", $addr->getData('city'), $ndAddr, $xd);
			self::xmlAppend("State", $state, $ndAddr, $xd);
			self::xmlAppend("Code", $addr->getData('postcode'), $ndAddr, $xd);
			self::xmlAppend("Country", $country, $ndAddr, $xd);
		$nd->appendChild($ndAddr);
	}

	private static function writeOrderItem(DOMElement $nd, Mage_Sales_Model_Order_Item $item, 
			Mage_Sales_Model_Order $order, DOMDocument $xd) {		

		$ok = false;
		$isBundleItem = false;
		
		// FLS:04/30/14:SEOM-3578: Variables for tax check
		$store = Mage::app()->getStore($order->getStoreId());
		$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		$tax_calc = Mage::getSingleton('tax/calculation');
		$tax_rate_req = $tax_calc->getRateRequest(
			$order->getShippingAddress(), 
			$order->getBillingAddress(), 
			$customer->getTaxClassId(), 
			$store);
		$tax_total = 0;
		$args = array();
		
		if ($item->getParentItemId()) {
			$parent = $item->getParentItem();
			if (is_object($parent)) {
				$price = $parent->getData('base_price');
				$qty = $parent->getQtyOrdered();
				$opt = $parent->getProductOptions();
				$type = $parent->getData('product_type');
				$weight = $item->getData('weight');
				if ($type == 'bundle') { 
					$price = 0; 
					$weight = 0;
					$isBundleItem = true; 
					$qty = $item->getQtyOrdered();
				}
				$ok = true;
			}
		} 
		
		// FLS:04/30/14:SEOM-3578: Get tax data
		$children = $item->getChildrenItems();
		if(count($children) && ($product->getData('price_type') != 1))
		{
			foreach($children as $child)
			{
				$product = Mage::getModel('catalog/product')
					->load($child->getProductId());

				/* If tax_percent is not set?
				Mage::getSingleton('tax/calculation')->getRate(
					$tax_rate_req->setProductClassId($product->getTaxClassId()))
				*/
				$tax_mod = (float)$child->getData('tax_percent');
				$tax_mod /= 1000;

				$tax_qty = (float)$child->getData('qty_ordered');

				// FLS:11/03/14:SEOM-4244: don't overwrite price
				$tax_price = (float)$child->getData('row_total_incl_tax');
				$tax_price -= (float)$child->getData('discount_amount');

				$base_price = (($tax_price / (1 + $tax_mod)) / $tax_qty);
				$base_price = $store->roundPrice($base_price);

				$tax_total += (($base_price * (1 + $tax_mod)) * $tax_qty);
				
				$args[] = array
                (
                    'name'          => $product->getData('name'),
                    'sku'           => $child->getData('sku'),
                    'tax_mod'       => $tax_mod,
                    'qty'           => $tax_qty,
                    'price'         => $tax_price,
                    'base_price'    => $base_price
                );
			}
		}
		else
		{
			/* If tax_percent is not set?
			Mage::getSingleton('tax/calculation')->getRate(
				$tax_rate_req->setProductClassId($product->getTaxClassId()))
			*/
			$tax_mod = (float)$item->getData('tax_percent');
			$tax_mod /= 1000;

			$tax_qty = (float)$item->getData('qty_ordered');

			// FLS:11/03/14:SEOM-4244: don't overwrite price
			$tax_price = (float)$item->getData('row_total_incl_tax');
			$tax_price -= (float)$item->getData('discount_amount');

			$base_price = (($tax_price / (1 + $tax_mod)) / $tax_qty);
			$base_price = $store->roundPrice($base_price);

			$tax_total += (($base_price * (1 + $tax_mod)) * $tax_qty);

			$args[] = array
				(
					'name'          => $product->getData('name'),
					'sku'           => $item->getData('sku'),
					'tax_mod'       => $tax_mod,
					'qty'           => $tax_qty,
					'price'         => $tax_price,
					'base_price'    => $base_price
				);
		}
		
		if (!$ok) {
			$price = $item->getData('base_price');
			$qty = $item->getQtyOrdered();
			$opt = $item->getProductOptions();
			$type = $item->getData('product_type');
			$weight = $item->getData('weight');
		}
		
		self::xmlAppend("Name", $item->getData('name'), $nd, $xd);
		self::xmlAppend("SKU", $item->getData('sku'), $nd, $xd);
		self::xmlAppend("ItemPrice", $price, $nd, $xd);
		self::xmlAppend("Quantity", $qty, $nd, $xd);
		self::xmlAppend("Weight", $weight, $nd, $xd);
		self::xmlAppend("CustomerText", self::getGiftMessage($item), $nd, $xd);		
			
		// FLS:04/30/14:SEOM-3578: Check if product is taxable
		$sku = $item->getData('sku');
		if (self::$_debug) { 
			echo "Tax Percent for sku = $tax_total";
		}
		if ($tax_total > 0) {
			self::xmlAppend("Taxable", "Yes", $nd, $xd);
		} else {
			self::xmlAppend("Taxable", "No", $nd, $xd);
		}
		
		$ok = (is_array($opt));
		if (!$ok) { return; } // no options

		$ok = false;
		if (!$isBundleItem && isset($opt['options']) && is_array($opt['options'])) {
			// has regular options
			foreach ($opt['options'] as $op) {
				$opName = $op['label'];
				$opVal = $op['value'];
				self::writeOrderOption($nd, $xd, $opName, $opVal);
			}
			$ok = true;
		}
		
		if (!$isBundleItem && isset($opt['attributes_info']) && is_array($opt['attributes_info'])) {
			// has inherited attributes
			foreach ($opt['attributes_info'] as $att) {
				$opName = $att['label'];
				$opVal = $att['value'];
				self::writeOrderOption($nd, $xd, $opName, $opVal);
			}
			$ok = true;
		}
		
		if (!$ok && !$isBundleItem && isset($opt['info_buyRequest']) && isset($opt['info_buyRequest']['options'])) {		
			$opt = $item->getProductOptionByCode('info_buyRequest');
			$opt = new Varien_Object($opt);
			if (!is_array($opt->getData('options')) || sizeof($opt->getData('options')) == 0) { return; }
			$prod = Mage::getModel('catalog/product')->load($item->getData('product_id'));
			
			foreach ($opt->getData('options') as $optId => $optValId) {
				$option = $prod->getOptionById($optId);
				if (!is_object($option)) continue;
				self::writeOrderOptions($nd, $optId, $optValId, $prod, $xd);
			}		
		}
	}

	private static function getGiftMessage(Varien_Object $entity) {
        if ($giftMessageId = $entity->getGiftMessageId()) {
            $giftMessage = Mage::getModel('giftmessage/message')->load($giftMessageId);
			return $giftMessage->getMessage();            
        }
        return '';
	}
		
	private static function writeOrderOption(DOMElement $nd, $xd, $opName, $opVal, $opCode = '', $opPrice = 0) {
		$ndOpt = $xd->createElement("OrderOption");
		self::xmlAppend("OptionName", $opName, $ndOpt, $xd);
		self::xmlAppend("SelectedOption", $opVal, $ndOpt, $xd);
		if ($opCode != '') { self::xmlAppend("OptionCode", $opCode, $ndOpt, $xd); }
		if ($opPrice != 0) {  self::xmlAppend("OptionPrice", $opPrice, $ndOpt, $xd); }
		$nd->appendChild($ndOpt);
	}
	
	private static function writeOrderOptions(DOMElement $nd, $optId, $optValId, Mage_Catalog_Model_Product $prod, 
			DOMDocument $xd) {
		$opPrice = 0;
		$selectedVal = '';
		$opName = '';
		$opCode = '';
		
		$option = $prod->getOptionById($optId);
		$opName = $option->getData('title');
		
		if (is_array($optValId)) {
			foreach ($optValId as $subval => $subvalId) {
				// checkbox values?
				$optVal = $option->getValueById($subvalId);
				if ($selectedVal != '') { $selectedVal .= ','; }
				$selectedVal .= $optVal->getData('title');
				$opPrice += (float)$optVal->getData('price');	
			}
		} else {
			$optVal = $option->getValueById($optValId);
			if (is_object($optVal)) { 
				$opPrice = (float)$optVal->getData('price');
				$selectedVal = $optVal->getData('title');
				$opCode = ($optVal->getData('sku') != '');
			} else {
				$selectedVal = '';
			}
		}
		self::writeOrderOption($nd, $xd, $opName, $selectedVal, $opCode, $opPrice);
	}
	
	private static function writeOrderPayment(DOMElement $ndOrd, Mage_Sales_Model_Order $order, DOMDocument $xd) {
		$payment = self::getOrderPayment($order);
		if (!$payment) { return; }

		// credit card variable initialization
		$ccNum = $ccIssuer = $ccExp = $ccName = $transId = $authCode = $avs = $cvv2 = $secKey = '';
		
		$ndPay = $xd->createElement("Payment");
		
		$payMeth = $payment->getData('method');


		if (self::$_debug) { echo "Payment Method is: $payMeth  " ; }
		
		switch ($payMeth) {
			case 'free':
				return;
			case 'checkmo':
				self::xmlAppend("Check", " ", $ndPay, $xd);
				break;
				
			case 'linkpoint':
				$secKey = self::getCcTransId($payment);
				$transId = self::getCcApproval($payment);
				$authCode = false;

			case 'cc':
			case 'ccsave':
			case 'authorizenet':	
			case 'verisign':
			case 'usaepay':
				$ndCc = $xd->createElement("CreditCard");
				
				self::xmlAppend("Issuer", self::getCcIssuer($payment->getData('cc_type')), $ndCc, $xd);
				
				$ccNum = $payment->getData('cc_number');
				if (!$ccNum || self::$_dssMode) { $ccNum = $payment->getData('cc_last4'); }

				self::xmlAppend("Number", (is_numeric($ccNum) ? utf8_encode($ccNum) : ''), $ndCc, $xd); 
				self::xmlAppend("ExpirationDate", $payment->getData('cc_exp_month') . '/' . $payment->getData('cc_exp_year'), $ndCc, $xd);
				
				$ccName = $payment->getData('cc_owner');
				if (!$ccName) { $ccName = $order->getBillingAddress()->getName(); }
				self::xmlAppend("FullName", $ccName, $ndCc, $xd);
				
				if ($transId !== false) { 
					if (!$transId) { $transId = self::getCcTransId($payment); }
					if ($transId) { self::xmlAppend("TransID", $transId, $ndCc, $xd); }
				}
				
				if ($authCode !== false) {
					if (!$authCode) { $authCode = self::getCcApproval($payment); }
					if ($authCode) { self::xmlAppend("AuthCode", $authCode, $ndCc, $xd); }
				}
				
				if ($secKey) {
					self::xmlAppend("SecurityKey", $secKey, $ndCc, $xd);
				}
				
				$avs = '';
				if (method_exists($payment, "getCcAvsStatus")) { $avs = $payment->getCcAvsStatus(); }
				if ($avs == '') { $avs = $payment->getData('cc_avs_status'); }
				self::xmlAppend("AVS", $avs, $ndCc, $xd); 
				
				self::xmlAppend("VerificationValue", $payment->getData('cc_cid_status'), $ndCc, $xd);
				
				$ndPay->appendChild($ndCc);
				break;
				
			case 'paypal_direct':
			case 'paypal_express':
			case 'paypal_standard':
			case 'paypaluk_direct':
			case 'paypaluk_express':
				$ndPP = $xd->createElement("PayPal");

				$transId = self::getCcTransId($payment);
				self::xmlAppend("TransID", $transId, $ndPP, $xd);				
				
				$ccNum = '';
				$ccNum = $payment->getData('cc_number');
				if (!$ccNum || self::$_dssMode) { $ccNum = $payment->getData('cc_last4'); }
				if ($ccNum != '') {
					self::xmlAppend("Issuer", self::getCcIssuer($payment->getData('cc_type')), $ndPP, $xd);
					self::xmlAppend("Number", utf8_encode($ccNum), $ndPP, $xd); 
				
					self::xmlAppend("ExpirationDate", $payment->getData('cc_exp_month') . '/' . $payment->getData('cc_exp_year'), $ndPP, $xd);
				
					//AVS	
					$avs = '';
					if ($avs == '') { $avs = $payment->getData('cc_avs_status'); }
					if ($avs == '') {
						if (method_exists($payment,"getAdditionalInformation") ) {
							$avs= $payment->getAdditionalInformation('paypal_avs_code');
						}
					} 
					if (self::$_debug) { echo "AVS is: $avs \r\n  "; }
					self::xmlAppend("AVS", $avs, $ndPP, $xd); 
					
					//CVV2
					if (method_exists($payment,"getAdditionalInformation") ) {
						$cvv= $payment->getAdditionalInformation('paypal_cvv2_match');
					}
				
					if (self::$_debug) { echo "CVV2 is: $cvv \r\n  "; }
					self::xmlAppend("CVV2", $cvv, $ndPP, $xd); 
				
					self::xmlAppend("VerificationValue", $payment->getData('cc_cid_status'), $ndPP, $xd);
				
				}
				$ndPay->appendChild($ndPP);

				break;
				
			case 'purchaseorder':
				$ndPo = $xd->createElement("PurchaseOrder");
				self::xmlAppend("PurchaseNumber", $payment->getData('po_number'), $ndPo, $xd);
				$ndPay->appendChild($ndPo);
				break;

			case 'googlecheckout':
			case 'google checkout':
				self::xmlAppend("GoogleCheckout", " ", $ndPay, $xd);
				break;
				
		    case 'authnetcim':
				// Get Customer Profile ID
				$profileId		= $payment->getAdditionalInformation('profile_id');
				
				if( empty( $profileId ) ) {
					$customer		= Mage::getModel('customer/customer')->load( $order->getCustomerId() );
					$profileId		= $customer->getAuthnetcimProfileId();
				}
				
				// Get Payment Profile ID
				$paymentId		= $payment->getAdditionalInformation('payment_id');
				
				if( empty( $paymentId ) ) {
					$paymentId		= $order->getExtCustomerId();
				}
				
				// Get card number
				$ccNum			= $payment->getData('cc_last4');
				
				if( empty( $ccNum ) && substr( Mage::getConfig()->getNode()->modules->ParadoxLabs_AuthorizeNetCim->version, 0, 1 ) == '1' ) {
					$cimPayment		= Mage::getModel('authnetcim/payment')->setStore( $order->getStoreId() );
					$card			= $cimPayment->getPaymentInfoById( $paymentId, false, $profileId );
					
					if( $card instanceof Varien_Object ) {
						$ccNum			= substr( $card->getCardNumber(), -4 );
					}
				}
				
				// Get expiration date
				if( $payment->getData('cc_exp_year') == '' ) {
					$expirationDate	= 'XXXX';
				}
				else {
					$expirationDate = $payment->getData('cc_exp_month') . '/' . $payment->getData('cc_exp_year');
				}
				
				
				$ndAc			= $xd->createElement("CreditCard");
				
				self::xmlAppend("Issuer", self::getCcIssuer($payment->getData('cc_type')), $ndAc, $xd);
				self::xmlAppend("Number", $ccNum, $ndAc, $xd);
				self::xmlAppend("ExpirationDate", $expirationDate, $ndAc, $xd);
				self::xmlAppend("FullName", $order->getBillingAddress()->getName(), $ndAc, $xd);
				self::xmlAppend("VerificationValue", $payment->getAdditionalInformation('card_code_response_code'), $ndAc, $xd);
				self::xmlAppend("AVS", $payment->getAdditionalInformation('avs_result_code'), $ndAc, $xd);
				self::xmlAppend("TransID", self::getCcTransId($payment), $ndAc, $xd);
				self::xmlAppend("AuthCode", $payment->getAdditionalInformation('approval_code'), $ndAc, $xd);
				self::xmlAppend("ProcessLevel", "AuthnetCim", $ndAc, $xd);
				self::xmlAppend('Amount', $payment->getData('base_amount_authorized'), $ndAc, $xd);
				
				self::xmlAppend('PayToken', $profileId . ':' . $paymentId, $ndAc, $xd);
				
				if ($secKey) {
					self::xmlAppend("SecurityKey", $secKey, $ndAc, $xd);
				}
			
				
				$ndPay->appendChild($ndAc);
				break;
				
			default:
				$nd = $xd->createElement("Generic1");
				self::xmlAppend("Name", $payMeth, $nd, $xd);
				self::xmlAppend("Description", "Unrecognized payment type: '{$payMeth}'", $nd, $xd);
				$ndPay->appendChild($nd);
				break;


		}
		if (self::$_debug) { echo "CCNum is: $ccNum <br>"; }
		$ndOrd->appendChild($ndPay);
	}

	private static function getOrderPayment(Mage_Sales_Model_Order $order) {
		$payments = $order->getAllPayments();
		if (!$payments || !is_array($payments)) { return false; }
		$payment = $payments[0]; // for now, we're only interested in the first payment

		if (!$payment || !is_object($payment)) { return false; }

		$method = $payment->getData('method');
		if (!self::isCcPayment($method)) { return $payment; }
		if ($payment->getData('cc_type')) { return $payment; }
		
		// Payment data may be nested. We have to go deeper...
		$p = self::getPayData($payment->getData());
		if (!$p) { return false; }
		$p->setData('method', $method);
		return $p;
	}
	
	private static function isCcPayment($method) {
		switch ($method) {
			case 'cc':
			case 'ccsave':
			case 'authorizenet':
			case 'linkpoint':
			case 'verisign':
			case 'usaepay':
				return true;
			default:		
				return false;
		}
	}	
	
	private static function getPayData($p) {
		if (is_array($p)) { 
			if (isset($p['cc_type'])) { return new Varien_Object($p); }
			
			foreach ($p as $val) {
				$x = self::getPayData($val);
				if (is_object($x)) { return $x; }
			}
		}
		return false;
	}	
	
	private static function getCcIssuer($issuer) {
		switch (strtolower($issuer)) {
			case 'vi':
				return "Visa";
			case 'di':
				return "Discover";
			case 'mc':
				return "MasterCard";
			case 'ae':
				return "AMEX";
			case 'ot':
				return "Other";				
		}
		
	}	
	
	private static function getCcTransId(Varien_Object $payment) {
		$transId = false;
		$transId = $payment->getData('last_trans_id');
		if (!$transId && method_exists($payment, "getRefundTransactionId")) { $transId = $payment->getRefundTransactionId(); }
		if (!$transId) { $transId = $payment->getData('cc_trans_id'); }
		return $transId;		
	}
	
	private static function getCcApproval(Varien_Object $payment) {
		$authCode = false;
		if (method_exists($payment, "getCcApproval")) { $authCode = $payment->getCcApproval(); }
		if (!$authCode) { $authCode = $payment->getData('cc_approval'); }
		return $authCode;		
	}

	private static function writeOrderTotals(DOMElement $ndTot, Mage_Sales_Model_Order $order, DOMDocument $xd) {	
		self::xmlAppend("ProductTotal", $order->getData('base_subtotal'), $ndTot, $xd);
		
		$ndTax = $xd->createElement("Tax");
			self::xmlAppend("TaxAmount", $order->getData('base_tax_amount'), $ndTax, $xd);
		$ndTot->appendChild($ndTax);
			
		self::xmlAppend("GrandTotal", $order->getData('base_grand_total'), $ndTot, $xd);
		
		$ndShip = $xd->createElement("ShippingTotal");
			self::xmlAppend("Total", $order->getData('base_shipping_amount'), $ndShip, $xd);
			self::xmlAppend("Description", $order->getData('shipping_description'), $ndShip, $xd);
		$ndTot->appendChild($ndShip);
		
		if ((float)$order->getData('reward_currency_amount') > 0) {
			$ndDiscount = $xd->createElement("Discount");
				self::xmlAppend('Description', 'Reward Points Redemption', $ndDiscount, $xd);
				self::xmlAppend('Amount', $order->getData('reward_currency_amount'), $ndDiscount, $xd);
			$ndTot->appendChild($ndDiscount);
		}

		
	}

        private static function isEnterpriseGiftCardsInstalled() {
                $modules = Mage::getConfig()->getNode('modules')->children();
                $modulesArray = (array)$modules;

                if(isset($modulesArray['Enterprise_GiftCardAccount'])) {
                return true;
                } else {
                return false;
                }
        }



	private static function writeOrderAdjustments(DOMElement $ndOrd, Mage_Sales_Model_Order $order, DOMDocument $xd) {
        // 6/9/13:MES - look for coupon code, not discount amount. Return zero-value coupon redemptions to SE
	// 6/11/14:RJG - refactor to detect coupons and discounts that are associated w/general rules (e.g. not a coupon, but still a discount)
		if ($order->getData('coupon_code') != '') {
			if (self::$_debug) { echo "Coupon detected\r\n"; }
		
			$ndCoupon = $xd->createElement("Coupon");
			$scratch = $order->getData('coupon_code');
			if (self::$_debug) { echo "Coupon code is $scratch\r\n"; }
			
			self::xmlAppend('Name', $order->getData('coupon_code'), $ndCoupon, $xd);
	            	self::xmlAppend('Status', $order->getData('coupon_rule_name'), $ndCoupon, $xd);
			self::xmlAppend('Total', $order->getData('base_discount_amount'), $ndCoupon, $xd);		
			$scratch = $order->getData('base_discount_amount');
			if (self::$_debug) { echo "Base discount associated with Coupon is $scratch\r\n"; }

			$ndOrd->appendChild($ndCoupon);
		}
		if ($order->getDiscountDescription() != '' ) {
			$scratch = $order->getDiscountDescription();
			if (self::$_debug) { echo "Discount detected and is $scratch\r\n"; }
			$ndDiscount = $xd->createElement("Discount");
			self::xmlAppend('Description', $order->getDiscountDescription(), $ndDiscount, $xd);
			self::xmlAppend('Total', $order->getDiscountAmount(), $ndDiscount, $xd);		
			$ndOrd->appendChild($ndDiscount);
		}

        // 6/9/13:MES - Add support for Magento Enterprise Gift Card redemptions
		$cardsOn= self::isEnterpriseGiftCardsInstalled();
		if ($cardsOn)
		{
			$cards = false;
        		$helper = Mage::helper('enterprise_giftcardaccount');
        		if ($helper) { $cards = $helper->getCards($order); }
        		if ($cards && is_array($cards) && sizeof($cards) > 0) {
            		foreach ($cards as $card) {
                		$id = $card['i'];
                		$code = $card['c'];
                		$amount = $card['a'];
                		$base_amount = $card['ba'];
                		$authorized = $card['authorized'];
		
                		$ndCoupon = $xd->createElement("Coupon");
                		self::xmlAppend('Name', "Gift card $code", $ndCoupon, $xd);
                		self::xmlAppend('Total', $authorized, $ndCoupon, $xd);
                		$ndOrd->appendChild($ndCoupon);
            		}
			}
		}
	}

	
	private static function qohreplace() {
		$update = (isset($_REQUEST['update']) ? $_REQUEST['update'] : false);
		if (!$update) {
			echo 'SETIError: Invalid/missing update command: ' . $_REQUEST['update'];
			return;
		}

		$res = Mage::getSingleton('core/resource');
        	$db = $res->getConnection('catalog_read');
		
		$response =  "SETIResponse\r\n";
		
		$skuList = explode( '|', $update);
		foreach ($skuList as $skuQohPair) {
			if ($skuQohPair == '') { continue; }
			$skuAndQoh = explode('~', $skuQohPair);
			if (sizeof($skuAndQoh) < 2) {
				// 2013-03-11:MES:Fix runtime error (use of undefined variable $sku).
				//$response .= "$sku=Err: Invalid SKU pair '$skuQohPair'\r\n";
				$response .= "Invalid SKU pair '$skuQohPair'\r\n";
				continue;
			}				
			$sku = $skuAndQoh[0];
			$qoh = $skuAndQoh[1];
			
			$result = self::updateStock($res, $db, $sku, $qoh, false);
			switch ($result) {
				case self::QOH_RESULT_OK:
				    // 2013-07-24:Add reindex support
					if (self::$_reindex) {
						$_process = Mage::getSingleton('index/process')->load(8);
						if (!$_process->isLocked());
						{
							$_process -> reindexAll();
						}
					}
					$response .= "$sku=OK\r\n";
					break;
				case self::QOH_RESULT_NOT_FOUND:
					$response .= "$sku=NF\r\n";
					break;
				case self::QOH_RESULT_NA:
					$response .= "$sku=NA\r\n";
					break;
				default:
					$response .= "$sku=Err\r\n";
					break;				
			}
		}
		
		$response .= "SETIEndOfData";
		echo $response;		
	}
	
	private static function invupdate() {
		$update = (isset($_REQUEST['update']) ? $_REQUEST['update'] : false);
		if (!$update) {
			echo 'SETIError: Invalid/missing update command: ' . $_REQUEST['update'];
			return;
		}

		$skuAndQoh = explode('~', $update);
		if (sizeof($skuAndQoh) < 1) {
			echo 'SETIError: Invalid update command: ' . $update;
			return;
		}
				
		$sku = $skuAndQoh[0];
		$qoh = $skuAndQoh[1];

		$res = Mage::getSingleton('core/resource');
        $db = $res->getConnection('catalog_read');	     

        $result = self::updateStock($res, $db, $sku, $qoh, true);
		switch ($result) {
			case self::QOH_RESULT_OK:
				echo 'SETIResponse=OK;QOH='.$qoh;
				break;
			case self::QOH_RESULT_NOT_FOUND:
				echo 'SETIResponse=OK;Note=SKU ' . $sku . ' was not found.';
				break;
			case self::QOH_RESULT_NA:
				echo 'SETIResponse=OK;Note=SKU ' . $sku . ' is not being tracked.';
				break;
			default:
				echo 'SETIResponse=False;Note=Unknown error updating stock, will retry';
				break;
		}
	        	        
	}
	
	private static function updateStock($res, $db, $sku, &$qoh, $relative = false) {
        $productTable = $res->getTableName('catalog/product');
		$sql = $db->select()
				  ->from($productTable, 'entity_id')
				  ->where('sku = ?', $sku);			
		if (self::$_debug) { echo "Executing SQL: $sql\r\n"; }
		$entityId = $db->fetchOne($sql);
		
		if ($entityId == '' || $entityId == null) {
			return self::QOH_RESULT_NOT_FOUND;
		}

		$item = new Mage_CatalogInventory_Model_Stock_Item();
		$item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($entityId);
		if (!$item->getManageStock()) {
			return self::QOH_RESULT_NA;	
		}

		if ($relative) {
			$change = (float)$qoh;
		} else {
			$change = (float)$qoh - (float)$item->getData('qty');			
		}
		
		if ($change < 0) {
			$item->subtractQty(abs($change));
		} else {
			$item->addQty($change);
		}
		
		$item->save();
		
		$qoh = $item->getData('qty');
		if ($qoh > 0) {
			// set the stock status to "In Stock" again
			$newStatus = Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK;
			$item->setData('is_in_stock', $newStatus);
			$item->save();
		
			// 2013-07-24:Add reindex support
			if (self::$_reindex) {
				if ($relative) {
					$item->afterCommitcallback();
				}
				else {
					$stockStatus = Mage::getSingleton('cataloginventory/stock_status');
					$stockStatus->changeItemStatus($item);
					}
			}
			else {
				$stockStatus = Mage::getSingleton('cataloginventory/stock_status');
				$stockStatus->changeItemStatus($item);			
			}


		}
		return self::QOH_RESULT_OK;
	}

	private static function updatestockstatus() {
		$update = (isset($_REQUEST['update']) ? $_REQUEST['update'] : false);
		if (!$update) {
			echo 'SETIError: Invalid/missing update command: ' . $_REQUEST['update'];
			return;
		}

		$res = Mage::getSingleton('core/resource');
        $db = $res->getConnection('catalog_read');
				
		$skuList = explode( '|', $update);
		foreach ($skuList as $skuQohPair) {
			if ($skuQohPair == '') { continue; }
			$skuAndQoh = explode('~', $skuQohPair);
			if (sizeof($skuAndQoh) < 2) {
				$response .= "Invalid SKU pair '$skuQohPair'\r\n";
				continue;
			}				
			$sku = $skuAndQoh[0];
			$active = $skuAndQoh[1];
			$status = (strtolower($active) == "true" ? 1 : 0);
			
			self::updateStockItemStatus($res, $db, $status);
		}
		
		echo "SETIResponse: OK";		
	}
	
	private static function updateStockItemStatus($res, $db, $sku, $status) {
        $productTable = $res->getTableName('catalog/product');
		$sql = $db->select()
				  ->from($productTable, 'entity_id')
				  ->where('sku = ?', $sku);			
		if (self::$_debug) { echo "Executing SQL: $sql\r\n"; }
		$entityId = $db->fetchOne($sql);
		
		if ($entityId == '' || $entityId == null) {
			return;
		}

		$item = new Mage_CatalogInventory_Model_Stock_Item();
		$item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($entityId);
		if (!$item->getManageStock()) {
			return;	
		}

		$item->setData('active', $status);
		$item->save();
	}
	
	private static function updatestatus() {
		$xml = $_REQUEST['update'];
		try {
			$xd = new DOMDocument();
			if (mb_check_encoding($xml, 'utf-8') === true) { $xml = utf8_decode($xml); }
			$xd->loadXML($xml);
			$ndOrder = $xd->getElementsByTagName('Order');
			if (!$ndOrder || $ndOrder->length < 1) { throw new Exception('Invalid status update XML: ' . $xml); }
			$xPath = new DOMXPath($xd);
			
			$orders = $xPath->query('/Orders/Order');
			if (!$orders || $orders->length == 0) { throw new Exception('No orders found in request.'); }
			
			foreach ($orders as $order) {
				self::updateOrderStatus($order);
			}
			echo 'SETIResponse=OK;';
			
		} catch (Exception $e) {
			echo 'SETIResponse=False;Note=Missing or invalid status update data: ' . $e;
		}			
	}
	
	private static function updateOrderStatus(DOMElement $ndOrder) {
		$orderNumber = $ndOrder->getElementsByTagName('OrderNumber')->item(0)->nodeValue;
		$status = strtolower($ndOrder->getElementsByTagName('Status')->item(0)->nodeValue);

		// 2013-03-11:MES:SESE-70
		//$order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
		//if (!$order) { throw new Exception("Order number $orderNumber was not found."); }
		$orderId = self::getOrderEntityIdByIncrementId($orderNumber);
		if (!$orderId) { throw new Exception("Order number $orderNumber was not found."); }
        $order = Mage::getModel('sales/order')->load($orderId);
		
		// Get shipment increment ID - if there are already shipments, just get the last one
		$shipmentId = false; $shipments = false;
		$api = Mage::getModel('sales/order_shipment_api');

		try {
			$shipments = $order->getShipmentsCollection();
			
			if (!$shipments || empty($shipments) || !count($shipments)) {
				// returns incrementId of new shipment
				$shipmentId = $api->create($orderNumber);
			} else {
				// get incrementId of last shipment
				foreach ($shipments as $shipment) {
					$shipmentId = $shipment['increment_id'];
				}
				if ($shipmentId === false) { 
					$shipmentId = $api->create($orderNumber); 
				}
			}
			$shipments = $api->info($shipmentId);
		} catch (Exception $e) {
			throw $e;	
		}
		
		// Are we updating tracking data?
		$packages = $ndOrder->getElementsByTagName('Package');
		$doTracking = ($packages != null && $packages->length > 0);		
		if ($doTracking) {
			foreach ($packages as $package) {
				$trackNum = $package->getElementsByTagName('TrackNum')->item(0)->nodeValue;
				$carrier = $package->getElementsByTagName('Shipper')->item(0)->nodeValue;
				$carrierCode = self::parseShipmentCarrier($orderNumber, $carrier, $api);
				if (!$carrierCode || self::trackingNumberExists($trackNum, $shipments)) { continue; }				
				$api->addTrack($shipmentId, $carrierCode, $status, $trackNum);
			}
		}

		try {
			// Add status change to order status history (does not add comment or notify customer)
			$api = Mage::getModel('sales/order_api');
			$api->addComment($orderNumber, $status);		
		
			// Set the state, which will update the status automatically.
            // If state is "protected", set the status only.
			$order = Mage::getModel('sales/order')->load($orderId);
	
			if ($order->isStateProtected($order->getState())) {
				$order->setStatus($status);	
			} else {
				$order->setState($order->getState(), $status);
			}
			$order->save();			

		} catch (Exception $e) { 
			if (self::$_debug) { echo "Status update error: $e\r\n"; }
			throw $e; 
		}	
	}

	
	private static function trackingNumberExists($trackNum, $shipments) {
		if (!is_array($shipments)) return false;
		if (!isset($shipments['tracks']) || count($shipments['tracks']) == 0) return false;
		foreach ($shipments['tracks'] as $track) {
			if ($track['number'] == $trackNum) return true; 
		}
		return false;
	}
		
	private static function parseShipmentCarrier($orderNumber, $carrier, Mage_Sales_Model_Order_Shipment_Api $api) {
		$carrier = strtolower($carrier);
		if ($carrier == 'fx') { $carrier = 'fedex'; }
		$carriers = $api->getCarriers($orderNumber);
		foreach ($carriers as $code => $title) {
			if ($code == $carrier || $title == $carrier) { return $code; }	
		}		
		return 'custom';
	}
	
	// 2013-03-11:MES:SESE-70 - work around API changes in Magento 1.7 CE to order lookup.
	private static function getOrderEntityIdByIncrementId($incrementId) {
        $res = Mage::getSingleton('core/resource'); $ordersTable = $res->getTableName('sales/order');
        $db = $res->getConnection('sales_read');
        $sql = new Varien_Db_Select($db);
        $sql = $db->select()
            ->from($ordersTable, 'entity_id')
            ->where('store_id=?', self::$_storeId, Zend_Db::INT_TYPE)
            ->where('increment_id=?', $incrementId);
        if (self::$_debug) { echo "Executing SQL: $sql\r\n"; }
        return (int)$db->fetchOne($sql);
    }

	
	private static function writeResponse(DOMDocument $xd, $respType, $respCode = 1, $respDesc = 'success') {
		$ndDoc = $xd->createElement("SETI$respType");
		$ndResp = $xd->createElement("Response");
		self::xmlAppend("ResponseCode", $respCode, $ndResp, $xd);
		self::xmlAppend("ResponseDescription", $respDesc, $ndResp, $xd);
		$ndDoc->appendChild($ndResp);
		$xd->appendChild($ndDoc);
		return $ndDoc;		
	}

	private static function xmlAppend($ndBname, $ndBtxt, DOMElement $ndA, DOMDocument $xd) {
		$ndB = $xd->createElement($ndBname);
		$ndB->appendChild($xd->createTextNode($ndBtxt));
		$ndA->appendChild($ndB);
		return $ndA;		
	}		
}
