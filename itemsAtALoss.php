<?php

require_once('classes/meekrodb.class.php');

$weekStart=date('Y-m-d',time()-(21*24*60*60));

$results=DB::query("SELECT items.quantity, items.sku, meldedprofit.orderTotal, meldedprofit.productCost, meldedprofit.shippingCost, meldedprofit.channelFees, meldedprofit.profitDollars, meldedprofit.profitPercent, stores.storeName, shipments.shipDate
FROM meldedprofit, items, stores, shipments
WHERE meldedprofit.orderId = items.orderId
AND meldedprofit.orderId=shipments.orderId
AND shipments.shipDate>'$weekStart'
AND (meldedprofit.profitDollars <0
OR profitPercent < 0)
AND meldedprofit.orderTotal <>0
AND sku <> ''
AND productCost <> 0
AND stores.storeId = meldedprofit.storeId
ORDER BY shipments.shipDate DESC");

header("Content-Type: text/csv");
header("Content-disposition: attachment;filename=ItemsAtALossAfter$weekStart.csv");

$header = array("Quantity","SKU","Order Total","Product Cost","Shipping Cost","Channel Fees","Profit in Dollars","Profit in Percent","Store Name","Ship Date");
$fp = fopen("php://output", "w");
fputcsv ($fp, $header, ",");
foreach($results as $row) {
	fputcsv($fp,$row,",");
}	

fclose($fp);