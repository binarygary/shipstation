<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once 'classes/meekrodb.class.php';

$round_numerator=60*60*24;
$roundedTime=(round(time()/$round_numerator)*$round_numerator);
$modifydateend=date('Y-m-d',$roundedTime-(86400*2));


$shipStation=new ShipStation;

$shipStation->requestString="orders?orderStatus=awaiting_shipment&pageSize=500&modifyDateEnd=$modifydateend";
$response=$shipStation->query();

print_r($response);