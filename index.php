<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once('classes/meekrodb.class.php');

$shipStation=new ShipStation;

$i=1;

$shipStation->requestString="orders?page=$i&pageSize=500";
$firstResponse=$shipStation->query();

$i=$firstResponse[pages]-5;

while ($i<=$firstResponse[pages]) {

	$shipStation->requestString="orders?page=$i&pageSize=500";
	$response=$shipStation->query();
	
	foreach ($response[orders] as $shipmentDetails) {

		//print_r($shipmentDetails);

		$shipment=new Order;
		$shipment->orderId=$shipmentDetails[orderId];
		$shipment->orderNumber=$shipmentDetails[orderNumber];
		$shipment->orderKey=$shipmentDetails[orderKey];
		$shipment->orderDate=$shipmentDetails[orderDate];
		$shipment->modifyDate=$shipmentDetails[modifyDate];
		$shipment->paymentDate=$shipmentDetails[paymentDate];
		$shipment->orderStatus=$shipmentDetails[orderStatus];
		$shipment->customerEmail=$shipmentDetails[customerEmail];
		$shipment->orderTotal=$shipmentDetails[orderTotal];
		$shipment->amountPaid=$shipmentDetails[amountPaid];
		$shipment->taxAmount=$shipmentDetails[taxAmount];
		$shipment->shippingAmount=$shipmentDetails[shippingAmount];
		$shipment->shipDate=$shipmentDetails[shipDate];
		$shipment->userId=$shipmentDetails[userId];
		$shipment->storeId=$shipmentDetails[advancedOptions][storeId];
		$shipment->tagId=serialize($shipmentDetails[tagIds]);
		$shipment->save();
	}
	$i++;
}


//GET STORE INFO
$shipStation->requestString="stores";
$response=$shipStation->query();

foreach ($response as $storeDetails) {
	
	$store=new Store;
	$store->storeId=$storeDetails[storeId];
	$store->storeName=$storeDetails[storeName];
	$store->marketplaceId=$storeDetails[marketplaceId];
	$store->marketplaceName=$storeDetails[marketplaceName];
	$store->save();
}

//GET SHIPPER INFO
$shipStation->requestString="users";
$response=$shipStation->query();

foreach ($response as $userDetails) {
	
	$user=new User;
	$user->userId=$userDetails[userId];
	$user->userName=$userDetails[userName];	
	$user->name=$userDetails[name];
	$user->save();

}

