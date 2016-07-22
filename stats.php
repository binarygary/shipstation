<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once 'classes/meekrodb.class.php';

$results=DB::query("SELECT * FROM sslogs");

$max=0;
$min=time();

foreach ($results as $result) {
    
  if ($result[time]>$max) {
    $max=$result[time];
  }
  if ($result[time]<$min) {
    $min=$result[time];
  }
  
  $count=$count+1;
  
  $source[$result[source]]=$source[$result[source]]+1;
  
  $url[$result[url]]=$url[$result[url]]+1;
  
}

$avg=($count/($max-$min)*60);
echo $avg;

echo "<PRE>";

arsort($source);
print_r($source);

arsort($url);
print_r($url);

echo "</PRE>";