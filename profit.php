<?

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once('classes/meekrodb.class.php');

$starttime=time();

DB::query("TRUNCATE TABLE `profitability`");
$results=DB::query("SELECT COUNT( * ) AS instances, name FROM items GROUP BY name ORDER BY instances DESC");
DB::query("CREATE TABLE IF NOT EXISTS productCost (`name` VARCHAR(128) NOT NULL UNIQUE, `count` VARCHAR(12) NOT NULL, `cost` VARCHAR(10))");
foreach ($results as $result) {
	$items[name]=$result[name];
	$items[count]=$result[instances];
	DB::insertUpdate('productCost',(array)$items);
}

$startDate=date('Y-m-d',time()-7884000);
$endDate=date('Y-m-d',time());

$results=DB::queryFirstColumn("SELECT orderId FROM shipments WHERE shipDate>'$startDate' AND shipDate<'$endDate' AND orderStatus='closed'");
foreach ($results as $result) {
	$profit=new Profit;
	$profit->orderId=$result;
	$profit->orderTotal=DB::queryFirstField("SELECT orderTotal FROM shipments WHERE orderId=$result");
	$profit->shippingAmount=DB::queryFirstField("SELECT shippingAmount FROM shipments WHERE orderId=$result");
	$profit->sellingPrice=$profit->orderTotal-$profit->shippingAmount;
	$profit->userId=DB::queryFirstField("SELECT userId FROM shipmentdetails WHERE orderId=$result");
	$profit->storeId=DB::queryFirstField("SELECT storeId from shipments WHERE orderId=$result");
	$profit->shippingCost=DB::queryFirstField("SELECT shipmentCost from shipmentdetails WHERE orderId=$result");
	$items=DB::query("SELECT * FROM items WHERE orderId=$result");
	foreach ($items as $item) {
		$cost=DB::queryFirstField("SELECT cost FROM productCost WHERE name=%s",$item[name]);
		$cost=$cost*$item[quantity];
		$profit->productCost=$profit->productCost+$cost;
	}
	$profit->channelFees=$profit->channelFeeCalculator($profit->orderTotal,$profit->storeId);

	$profit->profitDollars=$profit->profitCalculator('dollar');
	$profit->profitPercent=$profit->profitCalculator('percent');
	$profit->save();
	unset($profit);
}

$endtime=time();

$runtime=$endtime-$starttime;

echo "$runtime seconds\r\n";
echo memory_get_peak_usage();
echo " peak memory usage";