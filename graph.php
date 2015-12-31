<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once 'classes/meekrodb.class.php';
require_once 'classes/Cache.class.php';

//start with old date and work forward
$date=time()-(86400*90);

$cache = phpFastCache();

$array=$cache->get("db_array");

if($array == null) {
  while ($date<time()-86400) {
    $fdate=date('Y-m-d',$date);
    $array[$fdate][orders]=DB::queryFirstField("SELECT COUNT(*) FROM shipments WHERE orderDate LIKE '$fdate%'");
    $array[$fdate][shipments]=DB::queryFirstField("SELECT COUNT(*) FROM shipments WHERE shipDate LIKE '$fdate%'");
    $array[$fdate][orderTotal]=DB::queryFirstField("SELECT SUM(orderTotal) FROM shipments WHERE orderDate LIKE '$fdate%'");
  
    $date=$date+86400;
  }
}
$cache->set("db_array",$array , 3600);


//$chart="['Date', 'Orders', 'Shipments', 'Dollars'],\n";
foreach ($array as $date=>$values) {
  $chart.="['$date', $values[orders], $values[orderTotal]],\n";    
}

?>



 <html>
  <head>
   <script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1.1','packages':['line', 'corechart']}]}"></script>

    <script type="text/javascript">
      google.setOnLoadCallback(drawChart);

      function drawChart() {
        
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
        data.addColumn('number', 'Orders');
        //data.addColumn('number', 'Shipments');
        data.addColumn('number', 'Dollars');

        data.addRows([
          <?php echo $chart; ?>
        ]);
        
        
        
        var materialOptions = {
          title: 'Company Performance',
          series: {
            // Gives each series an axis name that matches the Y-axis below.
            //0: {axis: 'Shipments'},
            1: {axis: 'Orders'},
            2: {axis: 'Dollars'}
          },
          axes: {
          // Adds labels to each axis; they don't have to match the axis names.
          y: {
            //Shipments: {label: 'Shipments'},
            Orders: {label: 'Orders'},
            Dollars: {label: 'Dollars'}
          }
          }
        };
        
        
        var materialChart = new google.charts.Line(chartDiv);

        materialChart.draw(data, materialOptions);
      }
    </script>
  </head>
  <body>
    <div id="chartDiv" style="width: 100%; height: 500px"></div>
    
    <TABLE>
      <TR><TH>Date</TH><TH>Total Orders</TH><TH>Dollar Value</TH></TR>
      <?php
        foreach ($array as $date=>$values) {
          echo "<TR><TD>$date</TD><TD ALIGN=CENTER>$values[orders]</TD><TD ALIGN=RIGHT>$";
          $prettySales=number_format($values[orderTotal],2);
          echo "$prettySales</TD></TR>\n";
        }
      ?>
    </TABLE>
  </body>
</html>