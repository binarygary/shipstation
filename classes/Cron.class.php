<?php

require_once('meekrodb.class.php');

class Cron {
  
  var $runtime;
  
  function start($scriptName,$start) {
    DB::startTransaction();
    DB::insert('cron', array('script'=>$scriptName, 'start'=>$start));
    $runs=DB::query("SELECT * FROM cron WHERE script=%s AND end=''",$scriptName);
    if (DB::count()>1) {
      DB::rollback();
      $started=DB::queryFirstColumn("SELECT start FROM cron WHERE script=%s",$scriptName);
      foreach ($started as $start) {
        if (($start+(($this->runtime*60)*10))>time()) {
          //echo "exit";
          exit;
        }
      }
    } else {
      DB::commit();
    }
  }
  
  function end($scriptName,$end) {
    
    DB::update('cron', 
      array(
        'end' => $end
      ), "script=%s AND end=''", $scriptName );
  }
  
}