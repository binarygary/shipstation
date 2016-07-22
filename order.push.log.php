<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});
require_once 'classes/meekrodb.class.php';

/*$results=db::query("SELECT * FROM pushorderss");

echo "<PRE>";
foreach ($results as $result) {
	print_r($result);
}
echo "</PRE>";
*/
echo "<HR>";

$results=db::queryOneColumn('response',"SELECT * FROM pushcallbacklog LIMIT 0,50");

echo "<PRE>";
foreach ($results as $result) {
	if (!is_array($result)) {
		$result=unserialize($result);
		if (!is_array($result['orders'])) {
			print_r($result);
		}
		if (count($result['orders'])>0 ) {
			foreach ($result['orders'] as $order) {
				print_r($order['items']);
				echo "<HR>";
			}
		}
	}
}
echo "</PRE>";
