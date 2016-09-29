<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once 'classes/meekrodb.class.php';
require_once 'classes/Mandrill.php';

$shipStation=new ShipStation;
$mandrill=new Mandrill('TWpU_BLdlFQKlWM0A2kjmw');

$round_numerator=60*60*24;
$roundedTime=(round(time()/$round_numerator)*$round_numerator);

$startdate=date('Y-m-d',$roundedTime-(86400*7));
$startshipdate=date('Y-m-d',$roundedTime-(86400*8));
$modifydateend=date('Y-m-d',$roundedTime-(86400*3));
$today=date('Y-m-d');


$shipStation->requestString="orders?orderStatus=awaiting_shipment&modifyDateEnd=$modifydateend&pageSize=50";
$response=$shipStation->query();

$message="The following orders are 3+ days old:";

foreach ($response[orders] as $shipment) {
	
	//print_r($shipment);
	
	//echo $shipment[orderNumber];
	//echo "<BR>";
	//echo substr($shipment[orderDate],0,10);
	$orderDate=substr($shipment[orderDate],0,10);
	//echo "<BR>";
	
	$store=DB::queryOneField('storeName',"SELECT * FROM stores WHERE storeId=%s",$shipment[advancedOptions][storeId] );
	
	//echo $store;
	foreach ($shipment[items] as $item) {
		$missingitems[$shipment[orderNumber]][item]=$item[name];
		$missingitems[$shipment[orderNumber]][quantity]=$item[quantity];
	}
	
	
	
	$message.="<BR>$shipment[orderNumber] from $store ordered on <B>$orderDate</B><BR>";
	foreach ($missingitems as $missingitem) {
		$message.="Containing $missingitem[quantity] of $missingitem[item]<BR>";
		//print_r($missingitem);
	}
	$missingitems=null;	
}

$message = array(
        'html' => $message,
        'subject' => 'Late Orders',
        'from_email' => 'glohr@sunnbattery.net',
        'from_name' => 'Greg Lohr',
        'to' => array(
			
			array (
				'email' => 'jwatterson@motobatt.com',
                'name' => 'Jerry Watterson',
                'type' => 'to'
			),
			array (
				'email' => 'mfinch@motobatt.com',
                'name' => 'Matt Finch',
                'type' => 'to'
			),
			array (
				'email' => 'ptron1@sunnbattery.net',
                'name' => 'Michael Glover',
                'type' => 'to'
			),
			array (
				'email' => 'glohr@sunnbattery.net',
                'name' => 'Greg Lohr',
                'type' => 'to'
			)
        ),
        'headers' => array('Reply-To' => 'glohr@motobatt.com'),
        'important' => false,
        'track_opens' => null,
        'track_clicks' => null,
        'auto_text' => null,
        'auto_html' => null,
        'inline_css' => null,
        'url_strip_qs' => null,
        'preserve_recipients' => null,
        'view_content_link' => null,
        'bcc_address' => null,
        'tracking_domain' => null,
        'signing_domain' => null,
        'return_path_domain' => null,
    );
   $result = $mandrill->messages->send($message);
?>