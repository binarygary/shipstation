<?php 

class ShipStation {
	
	var $apiKey='24dd86da9e7643c296e104e4eba9e74b';
	var $apiSecret='faa488390fdd4212a2aa8027804e8c43';
	
	var $endpoint='https://ssapi.shipstation.com/';
	
	var $remainingRequests=40;
  var $resetTime=0;
  var $lastRequestTime=null;

	var $requestString;
	
	var $response;
	
	function query() {
		$this->URL="$this->endpoint"."$this->requestString";
		
		$time_start = microtime(true);
		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey.":".$this->apiSecret);  
		curl_setopt($ch, CURLOPT_URL, $this->URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		$response=curl_exec($ch);
		
		$header_size=curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header=substr($response,0,$header_size);
		$body=substr($response,$header_size);
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		//echo "$this->URL ran in $time seconds\n";
		
		if ($this->headerHandler($header)) {
			return json_decode($body,1);
		} else {
			return false;
		}
					
	}
	
	function headerHandler($header) {
		$headers=explode("\r\n",$header);
		//print_r($headers);
		foreach ($headers as $headerLine) {
			if (strpos($headerLine,'200') !== false) {
				//echo $headerLine;
 			   	return true;
			} elseif (strpos($headerLine,'404') !== false) {
				echo "bollocks!! ". $this->URL . "is probably wrong";
				return false;
			} elseif (strpos($headerLine,'429') !== false){
				$lineArr=explode(":",$headerLine);
				if (trim($lineArr[0])=='X-Rate-Limit-Remaining') {
					$this->remainginRequests=trim($lineArr[1]);
				} elseif (trim($lineArr[0])=='X-Rate-Limit-Reset') {
					$this->resetTime=time()+trim($lineArr[1]);
				}
				//echo $this->resetTime;
				$sleep=$this->resetTime-time();
				//echo "SLEEPING";
				if (is_null($sleep)){
					$sleep=10;
				}
				sleep($sleep);
				$this->query();
			}
		}
	}
}
