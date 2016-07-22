<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once('classes/meekrodb.class.php');
require_once('classes/Mandrill.php');


$cron=new Cron;
$cron->runtime=1;
$cron->start(basename(__FILE__),time());


$calls = DB::query("SELECT * FROM callbacks WHERE type='order' LIMIT 0,19");

foreach ($calls as $call) {
	
	echo $call['ID'];	
	
	$shipStation=new ShipStation;

	$shipStation->endpoint=$call[url];
	$response=$shipStation->query();
	
	foreach ($response[orders] as $shipmentDetails) {
		
		DB::insert('pushcallbacklog', array(
			'url' => $call['url'],
			'response'	=> serialize($response),
		));
		
		
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
		//print_r($shipment);
		
		foreach ($shipmentDetails[items] as $shipItem) {
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
				//print_r($item);
			
				if ($shipment->storeId=='53424') {
					DB::useDB('endofdayreporting');
					$kit=DB::query("SELECT amazonkitcomponents.prodquant, product.prodname FROM `amazonkit`, amazonkitcomponents,product WHERE amazonkit.kitid=amazonkitcomponents.kitid AND amazonkitcomponents.prodid=product.prodid AND kitname=%s",$item->sku);
					
					$object[]=$kit;
		
					
					DB::useDB('inventory_sunn');
				}
				//exit;
			}
		
		
			if (is_array($object)) {
		
			$content=serialize($object);
		
			DB::insert('pushorderss', array(
				'ss_orderid'	=>	$shipment->orderId,
				'ss_orderobjects'	=>	$content,
			));
		}
		$object=null;
		
    
	}
	
	
	//DB::debugMode();
	DB::delete('callbacks', "ID=%s", $call[ID]);
	
	
	

	$cron->end(basename(__FILE__),time());
	
	
	
	//DB::debugMode(false);
}

/*
//GET STORE INFO
$shipStation=new ShipStation;
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
$shipStation=new ShipStation;
$shipStation->requestString="users";
$response=$shipStation->query();

foreach ($response as $userDetails) {
	
	$user=new User;
	$user->userId=$userDetails[userId];
	$user->userName=$userDetails[userName];	
	$user->name=$userDetails[name];
	$user->save();

}
*/