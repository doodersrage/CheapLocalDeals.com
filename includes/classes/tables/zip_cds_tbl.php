<?PHP

// class for interacting with zip codes table

class zip_cds_tbl {
	public $id;
	public $zip;
	public $latitude;
	public $longitude;
	public $url_name;
	public $url_id;
	public $views;
	public $updated;
	public $city_id;
	// table name used throughout queries within page
	private $tbl_nme = "zip_codes";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->zip = NULL;
		$this->latitude = NULL;
		$this->longitude = NULL;
		$this->url_name = NULL;
		$this->url_id = NULL;
		$this->views = NULL;
		$this->updated = NULL;
		$this->city_id = NULL;
	}
	
	// update zip views
	public function update_zip_views() {
			global $dbh;

		$this->views++;

		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						views = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array($this->views,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
	}
		
	// added to get long-lat data
	private function set_long_lat() {
		
		$obj_google=new googleRequest;
		$obj_google->zip=$this->zip;
		
		$obj_google->gKey=GOOGLE_MAPS_API_KEY;
		$latlng=$obj_google->GetRequest();
		//var_dump($latlng);  
		$this->longitude = $latlng[1];
		$this->latitude = $latlng[0];
		
	}
	
	// insert new category
	public function insert() {
			global $dbh;
				
		$this->set_long_lat();
		
		$this->update_url_name();
		
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						zip,
						latitude,
						longitude,
						updated,
						url_name,
						city_id
					 )
					 VALUES
					 (?,?,?,?,?,?);
					 ";
				 
		$update_vals = array($this->zip,
							$this->latitude,
							$this->longitude,
							$today,
							$this->url_name,
							$this->city_id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing zip code
	public function update() {
			global $dbh;
		
		$this->set_long_lat();
		
		$this->update_url_name();
		
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						zip = ?,
						latitude = ?,
						longitude = ?,
						updated = ?,
						url_name = ?,
						city_id = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array($this->zip,
							$this->latitude,
							$this->longitude,
							$today,
							$this->url_name,
							$this->city_id,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update seo friendly url name
	public function update_url_name() {
		global $url_nms_tbl;
		
		if ($this->url_name != '' || $this->url_name != 0) {
			$url_nms_tbl->id = $this->url_id;
			$url_nms_tbl->url_name = $this->url_name;
			$url_nms_tbl->parent_id = $this->id;
			$url_nms_tbl->type = 'zip';
		
			if ($url_nms_tbl->id == '' || $url_nms_tbl->id == 0) {
				$url_nms_tbl->insert();
				$url_nms_tbl->assign_parent_type_db_vars($this->id,'zip');
			} else {
				$url_nms_tbl->update();
			}
			$this->url_name = $url_nms_tbl->id;
		}
	}
		
	// write post vars to class variables
	public function get_post_vars() {
		
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->zip = (isset($_POST['zip']) ? $_POST['zip'] : '');
		$this->latitude = (isset($_POST['latitude']) ? $_POST['latitude'] : '');
		$this->longitude = (isset($_POST['longitude']) ? $_POST['longitude'] : '');
		$this->url_name = strtolower(preg_replace("/[^a-zA-Z0-9s]/", "-", (isset($_POST['url_name']) ? $_POST['url_name'] : '')));
		$this->url_id = (isset($_POST['url_id']) ? $_POST['url_id'] : '');
		$this->city_id = (isset($_POST['city_id']) ? $_POST['city_id'] : '');
		
	}
		
	// get vars from database
	public function get_url_id($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id
					 FROM
						url_names
					 WHERE
						parent_id = ?
					 AND
						type = 'zip'
					  LIMIT 1;";

		$values = array(
						$id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->url_id = $row['zip'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						zip,
						latitude,
						longitude,
						url_name,
						updated,
						city_id
					 FROM
						".$this->tbl_nme."
					 WHERE
						id = ?
					  LIMIT 1;";

		$values = array(
						$id
						);
		
		$row = db_memc_str($sql_query,$values);
		
		$this->id = $row['id'];
		$this->zip = $row['zip'];
		$this->latitude = $row['latitude'];
		$this->longitude = $row['longitude'];
		$this->url_name = $row['url_name'];
		$this->updated = $row['updated'];
		$this->city_id = $row['city_id'];

	}
		
	// get vars from database
	public function search($zip) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						zip,
						latitude,
						longitude,
						url_name,
						views,
						city_id
					 FROM
						".$this->tbl_nme."
					 WHERE
						zip = ?
					  LIMIT 1;";
		

		$values = array(
						$zip
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->zip = $row['zip'];
		$this->latitude = $row['latitude'];
		$this->longitude = $row['longitude'];
		$this->url_name = $row['url_name'];
		$this->views = $row['views'];
		$this->city_id = $row['city_id'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function city_search($city_id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						zip,
						latitude,
						longitude,
						url_name,
						views,
						city_id
					 FROM
						".$this->tbl_nme."
					 WHERE
						city_id = ?
					  ;";
		

		$values = array(
						$city_id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->zip = $row['zip'];
		$this->latitude = $row['latitude'];
		$this->longitude = $row['longitude'];
		$this->url_name = $row['url_name'];
		$this->views = $row['views'];
		$this->city_id = $row['city_id'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
   
   /**
     * Returns an array containing the zip codes with the specified radius of
     * the specified zip codes.  Zips can be a single zip or a comma delimited
     * list of zips.
     */
    public function fetchZipsInRadiusByZip($zips, $radius, $precision) {
	  global $dbh;
	  
	  // find zips within range of each specified zip
	  // $loopZips = $zipsArray;
	  $zipsArray = null;
		  
	  $def_long_lat_qry = "SELECT latitude as src_lat, longitude as src_long FROM zip_codes WHERE zip = '".$zips."'";
	  $def_long_lat = $dbh->queryRow($def_long_lat_qry);
	  
	  $res_long_lat_qry = "SELECT zip
		FROM ".$this->tbl_nme."
		WHERE (POW((69.1 * (longitude - '".$def_long_lat['src_long']."') * COS('".$def_long_lat['src_lat']."' / 57.3)), 2) + POW((69.1 * (latitude - '".$def_long_lat['src_lat']."')), 2)) <= (".$radius." * ".$radius.")
		ORDER BY zip ASC;";

	  $res_long_lat = $dbh->queryAll($res_long_lat_qry);
		
	  foreach($res_long_lat as $row) {
		 $zipsArray[] = $row['zip'];
	  }
					
	return $zipsArray;
	}
   
   // pulls views count
   public function get_views_cnt() {
		global $dbh;
		
		$sql_query = "SELECT
						sum(views) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE
						city_id = ?
					  LIMIT 1;";
		
		$values = array(
						$this->city_id,
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		   
   return $row['rcount'];
   }
      
   // pulls city zip codes list
   public function get_list() {
		global $dbh;
		
		$new_zip_array = array();
		
		$sql_query = "SELECT
						zip
					 FROM
						".$this->tbl_nme."
					 WHERE
						city_id = ?
					 ;";
	
		$values = array(
						$this->city_id,
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);

		$results = array();
		while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$results[] = $row['zip'];
		}
 
		// clear result set
		$result->free();
 		
		// reset DB conn
		db_check_conn();
 
   return $results;
   }
}

?>