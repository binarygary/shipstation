<?php

require_once('meekrodb.class.php');

class Fba {
	
	var $date;
	var $settlement;
	var $type;
	var $orderId;
	var $sku;
	var $description;
	var $quantity;
	var $marketplace;
	var $fulfillment;
	var $orderCity;
	var $orderState;
	var $orderPosta;
	var $productSales;
	var $shippingCredits;
	var $giftWrapCredits;
	var $promotionalRebates;
	var $salesTaxCollected;
	var $sellingFees;
	var $fbaFees;
	var $otherTransactionFees;
	var $other;
	var $total;
		
	function save() {
		DB::insertUpdate('fba',(array)$this);
		//print_r($this);
	}
	
	function createTable() {
		DB::query("CREATE TABLE IF NOT EXISTS fba (`date` VARCHAR(128), 
			`settlement` VARCHAR(128),
			`type` VARCHAR(128),
			`orderId` VARCHAR(128),
			`sku` VARCHAR(128),
			`description` VARCHAR(128),
			`quantity` VARCHAR(128),
			`marketplace` VARCHAR(128),
			`fulfillment` VARCHAR(128),
			`orderCity` VARCHAR(128),
			`orderState` VARCHAR(128),
			`orderPosta` VARCHAR(128),
			`productSales` VARCHAR(128),
			`shippingCredits` VARCHAR(128),
			`giftWrapCredits` VARCHAR(128),
			`promotionalRebates` VARCHAR(128),
			`salesTaxCollected` VARCHAR(128),
			`sellingFees` VARCHAR(128),
			`fbaFees` VARCHAR(128),
			`otherTransactionFees` VARCHAR(128),
			`other` VARCHAR(128),
			`total` VARCHAR(128))");
		DB::query("TRUNCATE TABLE fba");		
	}
		
	
}
