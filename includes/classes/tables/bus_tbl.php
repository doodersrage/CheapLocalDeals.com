<?PHP

// class for interacting with businesses table
class bus_tbl {
	public $id;
	public $name;
	public $address;
	public $city;
	public $state;
	public $zip;
	public $phone;
	public $email;
	public $url;
	public $description;
	public $latitude;
	public $longitude;
	// table name used throughout queries within page
	private $tbl_nme = "businesses";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->name = NULL;
		$this->address = NULL;
		$this->city = NULL;
		$this->state = NULL;
		$this->zip = NULL;
		$this->phone = NULL;
		$this->email = NULL;
		$this->url = NULL;
		$this->description = NULL;
		$this->latitude = NULL;
		$this->longitude = NULL;
	}
	
	// insert new orders
	public function insert() {
		global $dbh;
			
		$this->set_long_lat();
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						name,
						address,
						city,
						state,
						zip,
						phone,
						email,
						url,
						description,
						latitude,
						longitude
					 )
					 VALUES
					 (
						?,
						?,
						?,
						?,
						?,
						?,
						?,
						?,
						?,
						?
					 );";
				 
		$update_vals = array(
							$this->name,
							$this->address,
							$this->city,
							$this->state,
							$this->zip,
							$this->phone,
							$this->email,
							$this->url,
							$this->description,
							$this->latitude,
							$this->longitude
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing orders
	public function update() {
			global $dbh;
		
		$this->set_long_lat();

		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						name = ?,
						address = ?,
						city = ?,
						state = ?,
						zip = ?,
						phone = ?,
						email = ?,
						url = ?,
						description = ?,
						latitude = ?,
						longitude = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->name,
							$this->address,
							$this->city,
							$this->state,
							$this->zip,
							$this->phone,
							$this->email,
							$this->url,
							$this->description,
							$this->latitude,
							$this->longitude,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->name = (isset($_POST['name']) ? $_POST['name'] : '');
		$this->address = (isset($_POST['address']) ? $_POST['address'] : '');
		$this->city = (isset($_POST['city']) ? $_POST['city'] : '');
		$this->state = (isset($_POST['state']) ? $_POST['state'] : '');
		$this->zip = (isset($_POST['zip']) ? $_POST['zip'] : '');
		$this->phone = (isset($_POST['phone']) ? $_POST['phone'] : '');
		$this->email = (isset($_POST['email']) ? $_POST['email'] : '');
		$this->url = (isset($_POST['url']) ? $_POST['url'] : '');
		$this->description = (isset($_POST['description']) ? $_POST['description'] : '');
		$this->latitude = (isset($_POST['latitude']) ? $_POST['latitude'] : '');
		$this->longitude = (isset($_POST['longitude']) ? $_POST['longitude'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						name,
						address,
						city,
						state,
						zip,
						phone,
						email,
						url,
						description,
						latitude,
						longitude
					 FROM
						".$this->tbl_nme."
					 WHERE
						id = ?
					  LIMIT 1;";

		$values = array(
						$id
						);
		
		$row = db_memc_str($sql_query,$values);
		
		if(!empty($row)){
		  $this->id = $row['id'];
		  $this->name = $row['name'];
		  $this->address = $row['address'];
		  $this->city = $row['city'];
		  $this->state = $row['state'];
		  $this->zip = $row['zip'];
		  $this->phone = $row['phone'];
		  $this->email = $row['email'];
		  $this->url = $row['url'];
		  $this->description = $row['description'];
		  $this->latitude = $row['latitude'];
		  $this->longitude = $row['longitude'];
		} else {
		  $this->reset_vars();
		}
	}
		
	// added to get long-lat data
	private function set_long_lat() {
		
		$obj_google=new googleRequest;
		
		$obj_google->address = $this->address;
		$obj_google->city = $this->city;
		$obj_google->zip = $this->zip;
		
		$obj_google->gKey=GOOGLE_MAPS_API_KEY;
		$latlng=$obj_google->GetRequest();
		//var_dump($latlng);  
		$this->longitude = $latlng[1];
		$this->latitude = $latlng[0];
		
	}
}

?>