<?

require_once('classes/meekrodb.class.php');

class Product {
	
	var $shipmentId;
	var $orderId;
	var $orderItemId;
	var $sku;
	var $name;
	var $quantity;
	var $unitPrice;
			
	function save() {
		DB::insertUpdate('items',(array)$this);
		
		print_r($this);
		echo "Product Saved";
	}

}