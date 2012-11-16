<?php
class googleRequest {

  public $gKey, $code, $Accuracy, $latitude, $longitude, $address, $city, $country, $error;

  function GetRequest() {

    if (strlen($this->gKey) > 1) {
      $q = str_replace(' ', '_', $this->address.','.$this->zip.'+'.$this->city.','.$this->country);
	  $url = "http://maps.google.com/maps/geo?q=$q&output=csv&key=".$this->gKey;
	
	  $curl_handle=curl_init();
	  curl_setopt($curl_handle,CURLOPT_URL,$url);
	  curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
	  curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,TRUE);
	  $buffer = curl_exec($curl_handle);
	  $httpcode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
	  curl_close($curl_handle);
	  
      if (!empty($buffer)) {
//        $gcsv = fread($d, 30000);
//        fclose($d);

       $output=array();
       $tmp = explode(",", $buffer);

       // $this->code      = $tmp[0];
       // $this->Accuracy  = $tmp[1];
        $output[0]=$this->latitude  = $tmp[2];
        $output[1]=$this->longitude = $tmp[3];
//		$this->error = $gcsv.'897897';
        $this->error = $buffer.$httpcode;
		
        return $output;     

      } else {
        $this->error = "NO_CONNECTION" ;
      }
    } else {
      $this->error = "No Google Maps Api Key" ;
    }
  }

}

?>