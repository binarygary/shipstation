<?php

//print_r($_SERVER);

if (is_null($_GET[qty]) || $_GET[qty]==0) {
  $_GET[qty]=1;
}

if (isset($_GET[part])) {
  $lookup = array_map('str_getcsv', file('lookup.csv'));
  foreach ($lookup as $masterSku) {
    if ($_GET[part]==$masterSku[0]) {
      $quantity=$_GET[qty]*$masterSku[2];
      $partArray[]="$quantity X $masterSku[1]";
    }    
  }
  if (!isset($partArray)) {
    $partArray[]="$_GET[qty] X $_GET[part]";
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