<?

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once('classes/meekrodb.class.php');

$starttime=time();


$db_tables = DB::tableList();
foreach ($db_tables as $table) {
	if (strlen($table)=='29') {
		DB::query("DROP TABLE $table");	
	}
}

$epoch=time()-(86400*90);

$date=$epoch;
while ($date<time()) {
	$range[]=array(date("Y-m-d",$date),date("Y-m-d",$date+518400));
	$date=$date+604800;
}


foreach ($range as $dates) {
unset($items);


DB::query("TRUNCATE TABLE `profitability`");
$results=DB::query("SELECT COUNT( * ) AS instances, name FROM items GROUP BY name ORDER BY instances DESC");
DB::query("CREATE TABLE IF NOT EXISTS productCost (`name` VARCHAR(128) NOT NULL UNIQUE, `count` VARCHAR(12) NOT NULL, `cost` VARCHAR(10))");
foreach ($results as $result) {
	$items[name]=$result[name];
	$items[count]=$result[instances];
	DB::insertUpdate('productCost',(array)$items);
}

$startDate=$dates[0];
$endDate=$dates[1];

$results=DB::queryFirstColumn("SELECT orderId FROM shipments WHERE shipDate>='$startDate' AND shipDate<='$endDate' AND orderStatus='closed'");
foreach ($results as $result) {
	echo $result;
	$profit=new Profit;
	$profit->orderId=$result;
	$profit->orderTotal=DB::queryFirstField("SELECT orderTotal FROM shipments WHERE orderId=%s",$result);
	$profit->shippingAmount=DB::queryFirstField("SELECT shippingAmount FROM shipments WHERE orderId=%s",$result);
	$profit->sellingPrice=$profit->orderTotal-$profit->shippingAmount;
	$profit->userId=DB::queryFirstField("SELECT userId FROM shipmentdetails WHERE orderId=%s",$result);
	$profit->storeId=DB::queryFirstField("SELECT storeId from shipments WHERE orderId=%s",$result);
	$profit->shippingCost=DB::queryFirstField("SELECT shipmentCost from shipmentdetails WHERE orderId=%s",$result);
	$items=DB::query("SELECT name,quantity FROM items WHERE orderId=$result");
	foreach ($items as $item) {
		$cost=DB::queryFirstField("SELECT cost FROM productCost WHERE name=%s",$item[name]);
		$cost=$cost*$item[quantity];
		$profit->productCost=$profit->productCost+$cost;
	}
	$profit->channelFees=$profit->channelFeeCalculator($profit->orderTotal,$profit->storeId);

	//if ($profit->productCost!=0 AND $profit->orderTotal!=0) {
		$profit->profitDollars=$profit->profitCalculator('dollar');
		$profit->profitPercent=$profit->profitCalculator('percent');
		$profit->save();
		print_r($profit);
	//}
	unset($profit);
}
$s="profitability$startDate$endDate";
$tablename=preg_replace("/[^a-zA-Z0-9]+/", "", $s);

DB::query("CREATE TABLE $tablename LIKE profitability");
DB::query("INSERT $tablename SELECT * FROM profitability");
	
	
} //END LOOP



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


$endtime=time();

$runtime=$endtime-$starttime;

echo "$runtime seconds\r\n";
echo memory_get_peak_usage();
echo " peak memory usage";

/* SQL export used 5/7/15
SELECT 
profitability.orderId,profitability.orderTotal,profitability.shippingAmount,profitability.productCost,profitability.shippingCost,profitability.channelFees,profitability.profitDollars,productCost.name,items.quantity,productCost.cost

FROM profitability, shipments,items,productCost WHERE profitability.orderId=shipments.orderId AND profitability.orderId=items.orderId and profitability.profitDollars<0 AND items.name=productCost.name ORDER BY profitDollars DESC
*/

/*
SELECT DISTINCT (T.name) as Name, AVG(T.profitDollars) as Profit FROM
(
SELECT items.name, profitability.profitDollars
FROM

items, profitability where items.orderId=profitability.orderId ORDER BY name ASC) as T GROUP BY T.name ORDER BY Profit DESC
*/