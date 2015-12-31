
<?

require_once('classes/meekrodb.class.php');

class QBUpdateCost {
	
	var $name;
	var $cost;

	function setTable() {
		DB::query("CREATE TABLE IF NOT EXISTS QBImportProductCost (`name` VARCHAR(128) NOT NULL UNIQUE, `cost` VARCHAR(10))");
		DB::query("TRUNCATE TABLE `QBImportProductCost`");
	}
	
	function save() {
		DB::insertUpdate('QBImportProductCost',(array)$this);
	}
	
	
}


