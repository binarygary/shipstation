<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once 'classes/meekrodb.class.php';
require_once 'classes/Mandrill.php';


function cleanDate($dateFull) {
  $date=date_parse($dateFull);
  return "$date[month]/$date[day]/$date[year]";
}

function storeName($storeId) {
  return DB::queryFirstField("SELECT storeName FROM stores WHERE storeId=%s",$storeId);
}

$round_numerator=60*60*24;
$roundedTime=(round(time()/$round_numerator)*$round_numerator);
$modifydateend=date('Y-m-d',$roundedTime-(86400*3));

$shipStation=new ShipStation;
$mandrill=new Mandrill('TWpU_BLdlFQKlWM0A2kjmw');

$shipStation->requestString="orders?orderStatus=awaiting_shipment&modifyDateEnd=$modifydateend&pageSize=100";
$response=$shipStation->query();

//print_r($response);
$lateOrder=1;
foreach ($response[orders] as $order) {
  $outstandingTotal=$outstandingTotal+$order[orderTotal];
  $lateOrders++;
  $message.="<B>$order[orderNumber]</B> (\$$order[orderTotal]) ordered on ". cleanDate($order[orderDate]) ." on ". storeName($order[advancedOptions][storeId]) ." containing:<BR />";
  foreach($order[items] as $item) {
    $message.="$item[quantity] of $item[name] ($item[sku])<BR />";  
  }
  if (!IS_NULL($order[customerNotes])) {
    $message.="<i>customer notes: $order[customerNotes]</i>";
  }
  $message.="<BR><HR>";
  unset($items);
}

$message = array(
        'html' => $message,
        'subject' => "$lateOrders Late Orders valued at \$$outstandingTotal did not leave shipping today",
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
			/*array (
				'email' => 'ptron1@sunnbattery.net',
                'name' => 'Michael Glover',
                'type' => 'to'
			),*/
			array (
				'email' => 'glohr@sunnbattery.net',
                'name' => 'Greg Lohr',
                'type' => 'to'
			),
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