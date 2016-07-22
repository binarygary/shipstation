<?php

require_once('classes/meekrodb.class.php');

$weekStart=date('Y-m-d',time()-(21*24*60*60));

if (isset($_GET[margin])) {
	
	$profitPercent=$_GET[margin];
	
	$results=DB::query("SELECT items.quantity, items.sku, meldedprofit.orderTotal, meldedprofit.productCost, meldedprofit.shippingCost, meldedprofit.channelFees, meldedprofit.profitDollars, meldedprofit.profitPercent, stores.storeName, shipments.shipDate
	FROM meldedprofit, items, stores, shipments
	WHERE meldedprofit.orderId = items.orderId
	AND meldedprofit.orderId=shipments.orderId
	AND shipments.shipDate>'$weekStart'
	AND (meldedprofit.profitDollars <0
	OR profitPercent < $profitPercent)
	AND meldedprofit.orderTotal <>0
	AND sku <> ''
	AND productCost <> 0
	AND stores.storeId = meldedprofit.storeId
	ORDER BY shipments.shipDate DESC");

	$margin=$profitPercent*100;
	
	header("Content-Type: text/csv");
	header("Content-disposition: attachment;filename=ItemsBelow".$margin."PercentAfter$weekStart.csv");

	$header = array("Quantity","SKU","Order Total","Product Cost","Shipping Cost","Channel Fees","Profit in Dollars","Profit in Percent","Store Name","Ship Date");
	$fp = fopen("php://output", "w");
	fputcsv ($fp, $header, ",");
	foreach($results as $row) {
		fputcsv($fp,$row,",");
	}	

	fclose($fp);
	
} else {
	
	$i = .05;
	while ($i<.40) {
		$iDisplay=$i*100;
		echo "<a href=?margin=$i>Items with a profit margin lower than $iDisplay%</a><BR>";
		$i=$i+.05;
	}
	
}