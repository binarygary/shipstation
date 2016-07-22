<?php

spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.class.php';
});
require_once '../classes/meekrodb.class.php';
require_once '../classes/Mandrill.php';

//print_r($_GET);

$orderItems=DB::query("SELECT * FROM items,shipments WHERE items.OrderId=shipments.orderId AND shipments.orderNumber=%s",$_GET[id]);


//print_r($orderItems);

foreach ($orderItems as $item) {
  $lookup = array_map('str_getcsv', file('lookup.csv'));
  foreach ($lookup as $masterSku) {
    if ($item[sku]==$masterSku[0]) {
      $quantity=$item[quantity]*$masterSku[2];
      $partArray[]="$quantity X $masterSku[1]";
    }    
  }
  if (!isset($partArray)) {
    $partArray[]="$item[quantity] X $item[sku]";
  }
}


$output=serialize($_SERVER);
$file = 'log.txt';
$current = file_get_contents($file);
$current .= "$_SERVER[REQUEST_URI]\n";
file_put_contents($file, $current);

//mail('gtyler@sunnbattery.net','Order Shipstation',$output);

$font='font/'.$_POST[font];


error_reporting(E_ALL); 
ini_set( 'display_errors','1');

$image = new Imagick();
$draw = new ImagickDraw();
$pixel = new ImagickPixel( 'white' );

/* New image */
$image->newImage(500, 220, $pixel);

/* Black text */
$draw->setFillColor('black');

/* Font properties */
$draw->setFont('font/OpenSans-Regular.ttf');
$draw->setFontSize( 14 );

/* Create text */

$height=15;

foreach ($partArray as $part) {
  $image->annotateImage($draw, 10, $height, 0, $part);
  $height=$height+20;
}
 
$image->setImageFormat('png');
  
  
/* Output the image with headers */
header('Content-type: image/png');
echo $image;