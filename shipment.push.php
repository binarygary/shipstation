<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once('classes/meekrodb.class.php');

$starttime=time();

$shipStation=new ShipStation;

//GET SHIPMENT INFO

$oldDate=date('Y-m-d',time()-31536000);

$results=DB::query("SELECT * FROM callbacks WHERE type='shipment' LIMIT 0,24");

	
print_r($results);


foreach ($results as $result) {
	//print_r($result);
	$shipStation->endpoint=$result[url];
	$response=$shipStation->query();
	//print_r($response);
	
	/*if ($response[total] == 0) {
		$order=new Order;
		$order->load($result);
		$order->modifyDate=$startime;
		$order->orderStatus="weird";
		$order->save();
	} else {*/
	//exit;
	
		foreach ($response[shipments] as $shipmentDetails) {
			//print_r($shipmentDetails);	
			$shipment=new Shipment;
			$shipment->shipmentId=$shipmentDetails[shipmentId];
			$shipment->orderId=$shipmentDetails[orderId];
			$shipment->userId=$shipmentDetails[userId];
			$shipment->shipDate=$shipmentDetails[shipDate];
			$shipment->shipmentCost=$shipmentDetails[shipmentCost];
			$shipment->serviceCode=$shipmentDetails[serviceCode];
			//if 
			$shipment->save();
			
			
			$order=new Order;
			$order->load($shipment->orderId);
			$order->modifyDate=$starttime;
			$order->orderStatus="closed";
			$order->save();


			foreach ($shipmentDetails[shipmentItems] as $shipItem) {
				//print_r($shipItem);
				$item=new Product;
				$item->shipmentId=$shipmentDetails[shipmentId];
				$item->orderId=$shipmentDetails[orderId];
				$item->orderItemId=$shipItem[orderItemId];
				$item->sku=$shipItem[sku];
				$item->name=$shipItem[name];
				$item->quantity=$shipItem[quantity];
				$item->unitPrice=$shipItem[unitPrice];
				$item->save();
			}
		}
	//}
	DB::delete('callbacks', "ID=%s", $result[ID]);
}


$endtime=time();

$runtime=$endtime-$starttime;

//echo "$runtime seconds\r\n";
//echo memory_get_peak_usage();
//echo " peak memory usage";
