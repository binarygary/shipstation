<?php

require_once('meekrodb.class.php');

class User {
	
	var $userId;
	var $userName;
	var $name;
		
	function save() {
		DB::insertUpdate('users',(array)$this);
	}
}