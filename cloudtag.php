<?php

require_once 'classes/meekrodb.class.php';
DB::$dbName = 'shipstats';
$late = DB::queryOneField('VALUE', "SELECT * FROM stats WHERE NAME=%s", 'orderCloud');
$tags=unserialize($late);

?>
<html>
  <head>
 <style type="text/css">
#tagcloud{
        color: #dda0dd;
        font-family: Arial, verdana, sans-serif;
        width:100%;
        border: 1px solid black;
	      text-align: center;
}

#tagcloud a{
        color: green;
        text-decoration: none;
        text-transform: capitalize;
}
</style>
  </head>
<body>
<div id="tagcloud">
  
  
<?

/*** create a new tag cloud object ***/
$tagCloud = new tagCloud($tags);

echo $tagCloud -> displayTagCloud();

  echo "<!--";
  print_r($tags);
  echo "-->";
  
?>
 
</div>
</body>
</html>
<?php

class tagCloud{

/*** the array of tags ***/
private $tagsArray;

public function shuffle_assoc($list) { 
  if (!is_array($list)) return $list; 
  arsort($list);
  //print_r($list);
  $list=array_slice($list,0,29);
  //print_r($list);
  $keys = array_keys($list); 
  shuffle($keys); 
  $random = array(); 
  foreach ($keys as $key) {
    $random[$key] = $list[$key]; 
  }
    

  return $random; 
} 
  
  
public function __construct($tags){
 /*** set a few properties ***/
 $this->tagsArray = $tags;
}

/**
 *
 * Display tag cloud
 *
 * @access public
 *
 * @return string
 *
 */
public function displayTagCloud(){
 $ret = '';
 $this->tagsArray=$this->shuffle_assoc($this->tagsArray);
  
  //print_r($this->tagsArray);
  foreach($this->tagsArray as $tag=>$weight) {
    $totalWeight=$totalWeight+(int)$weight;
    //echo "$totalWeight<HR>";
  }  
  foreach($this->tagsArray as $tag=>$weight) {
    $displayWeight=ceil((($weight*300)/$totalWeight));
    
    //echo "$displayWeight out of $totalWeight<BR>";
    if ($displayWeight>1) {
      $displayWeight=$displayWeight*3;
      $ret.='<a style="font-size: '.$displayWeight.'px;">'.$tag.'</a>'."\n";  
    }
    
    
  }
  return $ret;
}
    

} /*** end of class ***/

?>