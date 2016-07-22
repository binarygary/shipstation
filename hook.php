<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once('classes/meekrodb.class.php');

$starttime=time();

$oldDate=date('Y-m-d',time()-31536000);

$inbound=json_decode(file_get_contents('php://input'),true);

DB::query("INSERT INTO callbacks (url,type) VALUES (%s,%s)",$inbound[resource_url],$_GET[type]);