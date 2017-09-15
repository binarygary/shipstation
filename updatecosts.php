<?php

spl_autoload_register( function ( $class ) {
	include 'classes/' . $class . '.class.php';
} );
require_once( 'classes/meekrodb.class.php' );

$qb = new QBUpdateCost;
$qb->setTable();

//OPEN CSV & Save to Temp Table
if ( $handle = opendir( 'csv' ) ) {
	while ( false !== ( $entry = readdir( $handle ) ) ) {
		if ( $entry != "." && $entry != ".." ) {
			echo "$entry\n";
			if ( ( $handle2 = fopen( 'csv/' . $entry, "r" ) ) !== false ) {
				$count = 1;
				while ( ( $data = fgetcsv( $handle2, "," ) ) !== false ) {
					$qb->name = $data[3];
					$qb->cost = $data[12];
					$qb->save();
				}
				fclose( $handle2 );
			}
		}
	}
	closedir( $handle );
}

$results = DB::queryFirstColumn( "SELECT name FROM productCost WHERE cost IS NULL" );
foreach ( $results as $result ) {
	$item = explode( ' ', $result );
	if ( count( $item ) == 2 ) {
		$quantity      = preg_replace( "/[^0-9]/", "", $item[0] );
		$partNumber    = $item[1];
		$QBresult      = DB::queryFirstField( "SELECT cost FROM QBImportProductCost WHERE name=%s", $partNumber );
		$items[ cost ] = $QBresult * $quantity;
		$items[ name ] = $result;

		if ( $items[ cost ] != 0 ) {
			DB::insertUpdate( 'productCost', (array) $items );
		}

	}

}