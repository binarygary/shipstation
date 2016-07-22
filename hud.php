<?php


spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once 'classes/meekrodb.class.php';

$cron=new Cron;
$cron->runtime=2;
$cron->start(basename(__FILE__),time());

DB::startTransaction();

$shipStation=new ShipStation;

$round_numerator=60*60*24;
$roundedTime=(round(time()/$round_numerator)*$round_numerator);

$startdate=date('Y-m-d',$roundedTime-(86400*7));
$startshipdate=date('Y-m-d',$roundedTime-(86400*8));
$modifydateend=date('Y-m-d',$roundedTime-(86400*4));
$today=date('Y-m-d');

$ssusername="24dd86da9e7643c296e104e4eba9e74b";
$sspassword="faa488390fdd4212a2aa8027804e8c43";

//awaiting shipment
$shipStation->requestString="orders/listbytag?orderStatus=awaiting_shipment&pageSize=1&tagID=18950";
$response=$shipStation->query();
$awaitingShipment=$response['total'];

//late orders
$shipStation->requestString="orders?orderStatus=awaiting_shipment&modifyDateEnd=$modifydateend&pageSize=1";
$response=$shipStation->query();
$late=$response['total'];

//total shipped
$shipStation->requestString="shipments?shipDateStart=$startshipdate&pageSize=1";
$response=$shipStation->query();
$totalOrders=$response['total'];

$count=1;

while ($count<=$response['pages']) {
	
	
	$shipStation->requestString="orders?modifyDateStart=$startdate&pageSize=500&page=$count";
	$response=$shipStation->query();
		
	$orders=$response['orders'];
	
	
	if (!empty($orders)) {
		foreach ($orders as $order) {
			
			if ($order['orderStatus']=='shipped') {
				switch ($order['serviceCode']) {
				case "usps_first_class_mail":
					$service="FirstClass";
					break;
				case "usps_priority_mail":
					$service="Priority";
					break;
				case "fedex_smartpost_parcel_select":
				case "fedex_smartpost_parcel_select_lighweight":
					$service="SmartPost";
					break;
				case "fedex_ground":
					$service="FedExGround";
					break;
				/*case "ups_ground":
					$service="UPSGround";
					break;*/
				default:
					$service="Other";
				}
				if (substr($order['shipDate'],0,10)>$startshipdate){
					$shipdate=substr($order['shipDate'],0,10);
					$table[$shipdate][$service]=$table[$shipdate][$service]+1;
				}

				$ordercount=$ordercount+1;
				if (strtotime(substr($order['shipDate'],2,8))-strtotime(substr($order['orderDate'],2,8))<(86400*3)) {
					$fast=$fast+1;
				}
				
				

				//get shipped status!
				//$shipStation->requestString="shipments?orderId=$order[orderId]";
				//$shipperresponse=$shipStation->query();
				//if ($shipperresponse[userId]!='') {
				//	$user[$order[userId]]++;
				//}	

			} else if (is_array($order['items'])) {
				foreach ($order['items'] as $item) {
					//print_r($item);
					$dateFactor=round((time()-strtotime($item['createDate']))/86400,0);
					if ($dateFactor<10) {
						$itemsArray[$item['sku']]=$itemsArray[$item['sku']]+($item['quantity']*$dateFactor*$item['unitPrice']);
					}
				}
			}


		//print_r($order);
		}
	}
	
	$count++;
	//break;
	


}

$itemsArray=serialize($itemsArray);

//print_r($user);

$fastPercent=round(($fast/$ordercount)*100,0);
if ($fastPercent>79) {
	$fastColor="green";
} else {
	$fastColor="yellow";
}

ksort($table);

//save late
DB::insertUpdate('stats', array(
  'NAME' => late, //primary key
  'VALUE' => $late
), array ('VALUE' => $late));

//save awaitingshipment
DB::insertUpdate('stats', array(
  'NAME' => awaitingShipment, //primary key
  'VALUE' => $awaitingShipment
), array ('VALUE' => $awaitingShipment));

//save fastpercent
DB::insertUpdate('stats', array(
  'NAME' => fastPercent, //primary key
  'VALUE' => $fastPercent
), array ('VALUE' => $fastPercent));

//save fastColor
DB::insertUpdate('stats', array(
  'NAME' => fastColor, //primary key
  'VALUE' => $fastColor
), array ('VALUE' => $fastColor));

//table
$serialtable=serialize($table);
DB::insertUpdate('stats', array(
  'NAME' => table, //primary key
  'VALUE' => $serialtable
), array ('VALUE' => $serialtable));


//totalOrders
DB::insertUpdate('stats', array(
  'NAME' => totalOrders, //primary key
  'VALUE' => $totalOrders
), array ('VALUE' => $totalOrders));


DB::insertUpdate('stats', array(
	'NAME' => orderCloud,
	'VALUE' => $itemsArray
), array ('VALUE' => $itemsArray));

DB::commit();

$cron->end(basename(__FILE__),time());

?>