<?

require_once('classes/meekrodb.class.php');

class Shipment {
	
	var $shipmentId;
	var $orderId;
	var $userId;
	var $shipDate;
	var $shipmentCost;
	var $serviceCode;
			
	function save() {
		DB::insertUpdate('shipmentdetails',(array)$this);
		
		
		
		print_r($this);
		echo "SHIPMENT SAVED";
	}

}