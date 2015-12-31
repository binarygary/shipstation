<?php

require_once('classes/meekrodb.class.php');

$db_tables = DB::tableList();
foreach (array_reverse($db_tables) as $table) {
	if (strlen($table)=='29') {
		
		$profitDollar=DB::queryFirstField("SELECT sum(profitDollars)
			FROM $table, items, stores
			WHERE $table.orderId = items.orderId
			AND $table.orderTotal <> 0
			AND sku <> ''
			AND productCost <> 0
			AND stores.storeId = $table.storeId
			ORDER BY sku");
		$loss=DB::queryFirstField("SELECT sum(profitDollars)
			FROM $table, items, stores
			WHERE $table.orderId = items.orderId
			AND $table.profitDollars <0
			AND $table.orderTotal <> 0
			AND sku <> ''
			AND productCost <> 0
			AND stores.storeId = $table.storeId
			ORDER BY sku");
		$lossCount=DB::queryFirstField("SELECT count(profitDollars)
			FROM $table, items, stores
			WHERE $table.orderId = items.orderId
			AND $table.profitDollars < 0
			AND $table.orderTotal <> 0
			AND sku <> ''
			AND productCost <> 0
			AND stores.storeId = $table.storeId
			ORDER BY sku");
		$profitMargin=DB::queryFirstField("SELECT avg(profitPercent)
			FROM $table, items, stores
			WHERE $table.orderId = items.orderId
			AND $table.orderTotal <> 0
			AND sku <> ''
			AND productCost <> 0
			AND stores.storeId = $table.storeId
			ORDER BY sku");
		$transactions=DB::queryFirstField("SELECT count(profitPercent)
			FROM $table, items, stores
			WHERE $table.orderId = items.orderId
			AND $table.orderTotal <> 0
			AND sku <> ''
			AND productCost <> 0
			AND stores.storeId = $table.storeId
			ORDER BY sku");
		
		
		
		$dateDescriptor=date("F j Y",strtotime(substr($table,-16,8)))." - ".date("F j Y",strtotime(substr($table,-8)));
		
		//echo $dateDescriptor;
		
		
		$profitReport[$table][dateRange]=$dateDescriptor;
		$profitReport[$table][profitPercent]=$profitMargin;
		//$profitReport[$table][loss]=$loss;
		//$profitReport[$table][lossCount]=$lossCount;
		$profitReport[$table][transaction]=$transactions;
		if ($transactions==0) {
			$transactions=1;
		}
		$profitReport[$table][transAtLoss]=$lossCount/$transactions;
		$profitReport[$table][dollarPerTicket]=$profitDollar/$transactions;
		
		$profitReport[$table][profitDollar]=$profitDollar;
		
		$profitReport[$table][loss]=$loss;
		
		//echo "$table\n\r";
		
		
		if (is_null($db_first_table)) {
			$db_first_table=$table;
		} elseif (is_null($db_second_table)) {
			$db_second_table=$table;
			$lossHeader=$dateDescriptor;
		}
		
		
		
		
	}
	
}






$results=DB::query("SELECT items.quantity, items.sku, orderTotal, productCost, shippingCost, channelFees, $db_second_table.profitDollars, $db_second_table.profitPercent, stores.storeName
FROM $db_second_table, items, stores
WHERE $db_second_table.orderId = items.orderId
AND ($db_second_table.profitDollars <0
OR profitPercent < 0)
AND $db_second_table.orderTotal <>0
AND sku <> ''
AND productCost <>0
AND stores.storeId = $db_second_table.storeId
ORDER BY sku");

header("Content-Type: text/csv");
header('Content-disposition: attachment;filename=WeeklyProfitability.csv');

$header = array("Date Range","Margin","Transactions","Percent Sold At A Loss","Average Dollar Per Order","Profit","Loss In Dollars");

   $fp = fopen("php://output", "w");
   fputcsv ($fp, $header, ",");
   foreach($profitReport as $row){
        fputcsv($fp, $row, ",");
   }

$blank = array();
$i=1;
while ($i<2) {
	fputcsv($fp,$blank,",");
	$i++;
}

$lossHeader = array($lossHeader);
fputcsv ($fp, $lossHeader, ",");
$header = array("Quantity","SKU","Order Total","Product Cost","Shipping Cost","Channel Fees","Profit in Dollars","Profit in Percent","Store Name");
fputcsv ($fp, $header, ",");
foreach($results as $row) {
	fputcsv($fp,$row,",");
}	

   fclose($fp);



//RUN FOR ALL TRANSACTIONS
DB::query("DROP TABLE meldedprofit");

$query="CREATE TABLE meldedprofit";

$db_tables = DB::tableList();
foreach ($db_tables as $table) {
	if (strlen($table)=='29') {
		$profit_tables[]=$table;
	}
}

foreach ($profit_tables as $table) {
	$query.=" SELECT * FROM $table";
	if (end($profit_tables)==$table) {
		$query.=";";
	} else {
		$query.=" UNION ";
	}
}

DB::query("$query");
