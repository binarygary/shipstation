<?php

require_once 'classes/meekrodb.class.php';

$today=date('Y-m-d');

//DB::$dbName = 'shipstats';

$results=DB::query("SELECT * FROM stats");


$late = DB::queryOneField('VALUE', "SELECT * FROM stats WHERE NAME=%s", 'late');
$awaitingShipment = DB::queryOneField('VALUE', "SELECT * FROM stats WHERE NAME=%s", 'awaitingShipment');
$fastPercent = DB::queryOneField('VALUE', "SELECT * FROM stats WHERE NAME=%s", 'fastPercent');
$fastColor = DB::queryOneField('VALUE', "SELECT * FROM stats WHERE NAME=%s", 'fastColor');
$table = DB::queryOneField('VALUE', "SELECT * FROM stats WHERE NAME=%s", 'table');
$table=unserialize($table);
$totalOrders = DB::queryOneField('VALUE', "SELECT * FROM stats WHERE NAME=%s", 'totalOrders');

$bestOrder = DB::queryOneField('VALUE',"SELECT * FROM stats WHERE NAME=%s", 'bestOrder');
$bestDay = DB::queryOneField('VALUE',"SELECT * FROM stats WHERE NAME=%s", 'bestDay');

if ($totalOrders>$bestOrder) {
	DB::insertUpdate('stats', array(
  'NAME' => bestOrder, //primary key
  'VALUE' => $totalOrders
), array ('VALUE' => $totalOrders));
}

?>
<HTML>
<HEAD><TITLE>SHIPMENT HUD</TITLE>
	
	<style>
#shipments {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    width: 100%;
	height: 100%;
    border-collapse: collapse;
}

#shipments td, #shipments th {
    font-size: 1.3em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;
}

#shipments th {
    font-size: 1.3em;
    text-align: left;
    padding-top: 5px;
    padding-bottom: 4px;
    background-color: #A7C942;
    color: #ffffff;
}

#shipments tr.alt td {
    color: #000000;
    background-color: #EAF2D3;
}
		
#shipments td.today {
	font-variant: bold;
	font-size: 1.5em;
}
		
		img {
			min-width: 75%;
			width: auto;
			height: auto;
			max-height: 100%;
			max-width: 100%;
		}
		
		body {
			background-color: black;
		}
		
		pre {
			color: white;
		}
</style>

<script type="text/JavaScript">
function timeRefresh(timeoutPeriod) 
{
	setTimeout("location.reload(true);",timeoutPeriod);
}
</script>	
	
</HEAD>
<BODY onload="JavaScript:timeRefresh(60000);">
	
	
	<?php 
	
	if (count($table)<4) {
		
		$items=array('404','i+hate+technology','computer+error','file+not+found');
		$query=$items[array_rand($items)];
		
		echo exec('figlet -w 160 "404 error - no stuff"'.$arr,$return);
	
		echo "<pre>";
		foreach ($return as $line) {
			echo "$line<BR>";
		}
		echo "</pre>";

		echo "<CENTER>";
		
		$key='dc6zaTOxFJmzC';
		
		
		
		$endpoint="http://api.giphy.com/v1/gifs/search?q=$query&api_key=$key&limit=25";
		$rand=rand(0,24);

		$json=file_get_contents($endpoint);
		$obj=json_decode($json);

		//print_r($obj);

		echo "<img src=".$obj->data[$rand]->images->downsized_large->url.">";
		echo "<BR><i>powered by giphy</i>";
		
		echo "<!--";
		print_r($obj);
		echo "-->";
	} else {
	
	
	
	
	?>
	
	
	<TABLE WIDTH=100% HEIGHT=100% CELLPADDING=0 CELLSPACING=0 BORDER=0>
		<TR>
			<TD WIDTH=50% HEIGHT=45% BGCOLOR=BLACK><CENTER><FONT style="font-size:150px; color:red"><?PHP echo $late; ?></FONT><BR><FONT style="font-size:50px; color:red">ORDERS MORE THAN 4 DAYS OLD</FONT></CENTER></TD>
			<TD WIDTH=50% HEIGHT=45% BGCOLOR=BLUE><CENTER><FONT style="font-size:150px; color:white">
				<?php
					if ( $_GET['test'] == 'true' ) {
						$awaitingShipment=0;
					}
						
					if ( $awaitingShipment!=0 ) {
						echo $awaitingShipment;
						?></FONT><BR><FONT style="font-size:50px; color:white">PRINTED AND IN SHIPPING</FONT></CENTER><?php
					} else {
						$items=array( 'celebrate' );
						$query=$items[array_rand($items)];
						echo "<CENTER>";
						$key='dc6zaTOxFJmzC';
						$endpoint="http://api.giphy.com/v1/gifs/search?q=$query&api_key=$key&limit=25";
						$rand=rand(0,24);
						$json=file_get_contents($endpoint);
						$obj=json_decode($json);
						echo "<img src=".$obj->data[$rand]->images->downsized_large->url.">";
						echo "<BR><i style=\"font-size:8px\">powered by giphy</i>";
					}
					?>
				
			</TD>
		</TR>
		<TR>
			<TD WIDTH=50% HEIGHT=45% BGCOLOR=BLACK><CENTER><FONT style="font-size:150px; color:<?php echo $fastColor; ?>"><?PHP echo $fastPercent; ?>%</FONT><BR><FONT style="font-size:50px; color:green">SHIPPED UNDER 1 BUSINESS DAY</FONT></CENTER></TD>
			<TD WIDTH=50% HEIGHT=45% BGCOLOR=GREEN><CENTER><FONT style="font-size:40px; color:white">
							<table WIDTH=100% HEIGHT=100% CELLPADDING=0 CELLSPACING=0 BORDER=0 id=shipments>
							  <thead>
								<tr>
								  <th WIDTH=13%></th><th WIDTH=13%>First Class</th><th WIDTH=13%>Priority</th><th WIDTH=13%>FedEx Smartpost</th><th WIDTH=13%>FedEx Ground</th><!--<th WIDTH=13%>UPS Ground</th>--><th WIDTH=13%>Other</th><th WIDTH=13%>TOTAL</th>
								</tr>
							  </thead>
							  <tbody>
							<?php 
									
									if (is_array($table)) {
									
									foreach ($table as $header=>$value){ 
								  $sum=array_sum($value);
									$day++;
								  ?>
								<tr>
								  <td><?php 
										if ($header==$today) {
											$class="today";
											echo "<B>TODAY</B>";
										} else {
											$class="normal";
											echo date('l',strtotime($header));
										}
									  							  
									  ?></td><td class=<?php echo $class ?>><?php echo $value[FirstClass];?></td>
									<td class=<?php echo $class ?>><?php echo $value[Priority];?></td>
									<td class=<?php echo $class ?>><?php echo $value[SmartPost];?></td>
									<td class=<?php echo $class ?>><?php echo $value[FedExGround];?></td>
									<!--<td class=<?php echo $class ?>><?php echo $value[UPSGround];?></td>-->
									<td class=<?php echo $class ?>><?php echo $value[Other];?></td>
									<td class=<?php echo $class ?>><?php echo $sum; ?></td>
								</tr>
								  <?php 
									if ($sum>$bestDay) {
									DB::insertUpdate('stats', array(
  										'NAME' => bestDay, //primary key
  										'VALUE' => $sum
										), array ('VALUE' => $sum));
									}





									} 
									}?>
							  <tbody>
							</table>
				
				</FONT></CENTER></TD>
		</TR>
		<TR>
			<TD HEIGHT=10% COLSPAN=2 BGCOLOR=PINK>
				
					<FONT style="font-size:25px; color:black"><marquee behavior="scroll" direction="left">
						<?php echo $totalOrders; ?> PACKAGES SHIPPED IN THE LAST 7 DAYS - BEST WEEK EVER IS <?php echo $bestOrder; ?><BR><?php echo $sum; ?> PACKAGES SHIPPED TODAY - BEST DAY EVER IS <?php echo $bestDay; ?>
					</marquee></FONT>
				
			</TD>
		</TR>
	</TABLE>	
			
</BODY>
</HTML>
			
			<?php }