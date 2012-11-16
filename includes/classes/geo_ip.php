<?PHP

// class used to set page Geo_IP data

class page_geo_ip {
	public $latitude;
	public $longitude;
	public $cityid;
	public $city;
	public $region;
		
	public function __construct() {
		global $cities_tbl;
		
		// get geoip location
		if(empty($_COOKIE["GEOCityState"])){
		// load and search geo ip database
		  require('Net/GeoIP.php');
		  $geoip = Net_GeoIP::getInstance(LIBS_DIR.'GeoLiteCity.dat');
		  $location = $geoip->lookupLocation($_SERVER['REMOTE_ADDR']);
		  
		  $city = $location->city;
		  $state = $location->region;
		  $latitude = $location->latitude;
		  $longitude = $location->longitude;
		  
		  $locDat = array();
		  $locDat['city'] = $city;
		  $locDat['state'] = $state;
		  $locDat['latitude'] = $latitude;
		  $locDat['longitude'] = $longitude;
		  $locDat = serialize($locDat);
		  setcookie("GEOCityState", $locDat, 0, "/");
		} else {
		// if geo loc cookie data exists load that instead of querying geo ip database
		  $locDat = unserialize($_COOKIE["GEOCityState"]);
		  $city = $locDat['city'];
		  $state = $locDat['state'];
		  $latitude = $locDat['latitude'];
		  $longitude = $locDat['longitude'];
		}
		
		$cities_tbl->city_state_search($city,$state);
		
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->cityid = $cities_tbl->id;
		$this->city = $city;
		$this->region = $state;

	}

}

?>