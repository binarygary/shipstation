<?php

require_once('meekrodb.class.php');

class Order {
	
	var $orderId;
	var $orderNumber;
	var $orderKey;
	var $orderDate;
	var $modifyDate;
	var $paymentDate;
	var $orderStatus;
	var $customerEmail;
	var $orderTotal;
	var $amountPaid;
	var $taxAmount;
	var $shippingAmount;
	var $shipDate;
	var $userId;
	var $storeId;
		
	function save() {
		DB::insertUpdate('shipments',(array)$this);
		print_r($this);
	}
	
	function load($orderId) {
		$order=DB::queryFirstRow("SELECT * FROM shipments where orderId=%s",$orderId);
		foreach ($order as $key=>$value) {
			$this->$key=$value;
		}
	}
	
	
}
