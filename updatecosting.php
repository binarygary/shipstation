<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once('classes/meekrodb.class.php');

DB::useDB('endofdayreporting');
$results = DB::queryFirstColumn( "SELECT DISTINCT kitid from amazonkitcomponents" );

foreach ( $results as $kitid ){
  DB::useDB('endofdayreporting');
  $cost=0;
  $sku = DB::queryFirstField( "SELECT kitname FROM amazonkit WHERE kitid=$kitid");
  
  $components = DB::query( "SELECT * FROM amazonkitcomponents WHERE kitid=$kitid");
  foreach ( $components as $piece ) {
    $prodcost = DB::queryFirstField( "SELECT prodcost FROM product WHERE prodid=%s", $piece['prodid'] );
    $cost = $cost + ( $prodcost * $piece['prodquant'] );
  }

  DB::useDB('inventory_sunn');
  DB::replace('productCost', array(
    'name' => $sku,
    'cost' => $cost,
  ));
}
