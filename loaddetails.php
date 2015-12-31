<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once('classes/meekrodb.class.php');

$starttime=time();

$shipStation=new ShipStation;

//GET SHIPMENT INFO

$oldDate=date('Y-m-d',time()-31536000);

while (time()-$starttime<1140) {

$results=DB::queryFirstColumn("SELECT orderId FROM shipments WHERE orderStatus='shipped' AND orderDate>'$oldDate' ORDER BY shipDate ASC LIMIT 0,1");

foreach ($results as $result) {
	//print_r($result);
	$result=trim($result);
	
	echo "shipments?orderId=$result&includeShipmentItems=true\n";
	
	$shipStation->requestString="shipments?orderId=$result&includeShipmentItems=true";
	$response=$shipStation->query();
	//print_r($response);
	
	if ($response[total] == 0) {
		$order=new Order;
		$order->load($result);
		$order->modifyDate="1450462668";
		$order->orderStatus="weird";
		$order->save();
	} else {
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
			$shipment->save();

			$order=new Order;
			$order->load($shipment->orderId);
			$order->modifyDate="1450462668";
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
	}
}

	
	
}

$endtime=time();

$runtime=$endtime-$starttime;

echo "$runtime seconds\r\n";
echo memory_get_peak_usage();
echo " peak memory usage";
