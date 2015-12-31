<?php

require_once('meekrodb.class.php');

class Profit {
	
	var $orderId; //
	var $userId; //
	var $storeId; //
	var $orderTotal; //
	var $shippingAmount; //
	var $sellingPrice; //
	var $productCost; //
	var $shippingCost; //
	var $channelFees; //
	var $profitDollars; 
	var $profitPercent;
	
	
	//ACCURATE FOR ebay/amazon/newegg
	function channelFeeCalculator($total,$channel) {
		if ($channel='53424') {  //amazon
			return $total*.15;
		} elseif ($channel='53968') {  //ebay
			$standardFee=($total*.102)+.30;
			$microFee=($total*.13)+.05;
			if ($standardFee>$microFee) {
				return $microFee;
			} else {
				return $standardFee;
			}
		} elseif ($channel='54319') { //newegg
			return $total*.10;
		} else {
			return $total;
		}
		
	}
	
	function profitCalculator($return) {
		if ($return=='dollar') {
			return $this->orderTotal-($this->productCost+$this->shippingCost+$this->channelFees);
		} elseif ($return=='percent') {
			if ($this->orderTotal!=0) {
				return $this->profitDollars/$this->orderTotal;	
			} else {
				return 0;
			}
			
		}
	}
	
	function save() {
		DB::insertUpdate('profitability',(array)$this);
	}
	
		
}
