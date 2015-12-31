<?php

require_once('meekrodb.class.php');

class Store {
	
	var $storeId;
	var $storeName;
	var $marketplaceId;
	var $marketplaceName;
		
	function save() {
		
		DB::insertUpdate('stores',(array)$this);
	}
	
	
}