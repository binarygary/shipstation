<?php

//include 'includes/classes/AmazonOrderCore.php';
spl_autoload_register(function ($class) {
	include 'classes/' . $class . '.php';
});
require_once('classes/meekrodb.class.php');


$mws=new AmazonOrder;
