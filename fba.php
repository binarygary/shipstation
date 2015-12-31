<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once('classes/meekrodb.class.php');

$starttime=time();




//FIRST PARSE FILE INTO TABLE
//SECOND SORT TABLE AND COMBINE TRANSACTIONS
//THIRD INSERT INTO REGULAR TABLES

$fba=new Fba;
$fba->createTable();

$handle = fopen("amazon.csv", "r");
if ($handle) {
    while (($line = fgetcsv($handle)) !== false) {
        //print_r($line);
		
		if ($line[8]=='Amazon' & $line[2]=='Order') {
			$fba->date=$line[0];
			$fba->settlement=$line[1];
			$fba->type=$line[2];
			$fba->orderId=$line[3];
			$fba->sku=$line[4];
			$fba->description=$line[5];
			$fba->quantity=$line[6];
			$fba->marketplace=$line[7];
			$fba->fulfillment=$line[8];
			$fba->orderCity=$line[9];
			$fba->orderState=$line[10];
			$fba->orderPosta=$line[11];
			$fba->productSales=$line[12];
			$fba->shippingCredits=$line[13];
			$fba->giftWrapCredits=$line[14];
			$fba->promotionalRebates=$line[15];
			$fba->salesTaxCollected=$line[16];
			$fba->sellingFees=$line[17];
			$fba->fbaFees=$line[18];
			$fba->otherTransactionFees=$line[19];
			$fba->other=$line[20];
			$fba->total=$line[21];
			$fba->save();
		}
    }

    fclose($handle);
}

$fbaId=DB::queryFirstColumn("SELECT DISTINCT orderID from fba");

//print_r($fbaId);
//exit;

foreach ($fbaId as $id) {
	$shipment=new Order;
	$shipmentId=substr(bin2hex(openssl_random_pseudo_bytes($id)),0,18);
	$orders=DB::query("Select * FROM fba WHERE orderId=%s",$id);
	foreach ($orders as $order) {
		
		//print_r($order);
		$date=(date_parse($order[date]));
		if (strlen($date[month])==1) {
			$date[month]='0'.$date[month];
		}
		if (strlen($date[day])==1) {
			$date[day]='0'.$date[day];
		}		
		$date=$date[year].'-'.$date[month].'-'.$date[day].'T'.$date[hour].':'.$date[minute].':'.$date[second];
		
		$shipment->orderId=$order[orderId];
		$shipment->orderNumber=$order[orderId];
		$shipment->orderKey=$order[orderId];
		$shipment->orderStatus="closed";
		$shipment->orderTotal=$order[productSales]+$shipment->orderTotal;
		$shipment->taxAmount=$order[salesTaxCollected]+$shipment->taxAmount;
		$shipment->amountPaid=$shipment->orderTotal;
		$shipment->shippingAmount=0;
		$shipment->orderDate=$date;
		$shipment->modifyDate=$date;
		$shipment->paymentDate=$date;
		$shipment->shipDate=$date;
		$shipment->storeId='1234';
				
		$ship=new Shipment;
		$ship->orderId=$order[orderId];
		$ship->shipmentCost=$ship->shipmentCost+$order[fbaFees];
		$ship->serviceCode='FBA';
		$ship->shipDate=substr($date,0,10);
		$ship->shipmentId=$shipmentId;
		
		$item=new Product;
		$item->shipmentId=$shipmentId;
		$item->orderId=$order[orderId];
		$item->orderItemId=$shipmentId;
		$item->sku=$order[sku];
		$item->name=$order[sku];
		$item->quantity=$order[quantity];
		$item->unitPrice=$order[productSales]/$order[quantity];
		//print_r($item);
		$item->save();
	}
	if ($ship->shipmentCost<0) {
		$ship->shipmentCost=$ship->shipmentCost*-1;
	}
	//print_r($shipment);
	//print_r($ship);
	$shipment->save();
	$ship->save();
}


	
			
			
			
			/*
			$shipment=new Order;
		
		
		*/
			
			
			
			
			
			
		
			/*$order=new Order;
			$order->load($shipment->orderId);
			$order->orderStatus="closed";
			$order->save();*/
		
		


$endtime=time();

$runtime=$endtime-$starttime;

echo "$runtime seconds\r\n";
echo memory_get_peak_usage();
echo " peak memory usage";
