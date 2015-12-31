<?php

require_once('classes/meekrodb.class.php');
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