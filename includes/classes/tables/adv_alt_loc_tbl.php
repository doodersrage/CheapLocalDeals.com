<?PHP

// class for interacting with advertiser_alt_locations table
class adv_alt_loc_tbl {
	public $id;
	public $advertiser_id;
	public $enabled;
	public $location_name;
	public $hours_of_operation;
	public $products_services;
	public $description;
	public $hide_address;
	public $payment_options;
	public $website;
	public $address_1;
	public $address_2;
	public $city;
	public $state;
	public $zip;
	public $phone_number;
	public $fax_number;
	public $email_address;
	public $longitude;
	public $latitude;
	// table name used throughout queries within page
	private $tbl_nme = "advertiser_alt_locations";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->advertiser_id = NULL;
		$this->enabled = NULL;
		$this->location_name = NULL;
		$this->hours_of_operation = NULL;
		$this->products_services = NULL;
		$this->description = NULL;
		$this->hide_address = NULL;
		$this->payment_options = NULL;
		$this->website = NULL;
		$this->address_1 = NULL;
		$this->address_2 = NULL;
		$this->city = NULL;
		$this->state = NULL;
		$this->zip = NULL;
		$this->phone_number = NULL;
		$this->fax_number = NULL;
		$this->email_address = NULL;
		$this->longitude = NULL;
		$this->latitude = NULL;
	}
	
	// insert new advertiser_alt_locations
	public function insert() {
		global $dbh;
		
		$this->set_long_lat();
		
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						advertiser_id,
						enabled,
						location_name,
						hours_of_operation,
						products_services,
						description,
						hide_address,
						payment_options,
						website,
						address_1,
						address_2,
						city,
						state,
						zip,
						phone_number,
						fax_number,
						email_address,
						longitude,
						latitude
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
							  $this->advertiser_id,
							  $this->enabled,
							  $this->location_name,
							  $this->hours_of_operation,
							  $this->products_services,
							  $this->description,
							  $this->hide_address,
							  $this->payment_options,
							  $this->website,
							  $this->address_1,
							  $this->address_2,
							  $this->city,
							  $this->state,
							  $this->zip,
							  $this->phone_number,
							  $this->fax_number,
							  $this->email_address,
							  $this->longitude,
							  $this->latitude
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing advertiser_alt_locations
	public function update() {
		global $dbh;
		
		$this->set_long_lat();
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						advertiser_id = ?,
						enabled = ?,
						location_name = ?,
						hours_of_operation = ?,
						products_services = ?,
						description = ?,
						hide_address = ?,
						payment_options = ?,
						website = ?,
						address_1 = ?,
						address_2 = ?,
						city = ?,
						state = ?,
						zip = ?,
						phone_number = ?,
						fax_number = ?,
						email_address = ?,
						longitude = ?,
						latitude = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							  $this->advertiser_id,
							  $this->enabled,
							  $this->location_name,
							  $this->hours_of_operation,
							  $this->products_services,
							  $this->description,
							  $this->hide_address,
							  $this->payment_options,
							  $this->website,
							  $this->address_1,
							  $this->address_2,
							  $this->city,
							  $this->state,
							  $this->zip,
							  $this->phone_number,
							  $this->fax_number,
							  $this->email_address,
							  $this->longitude,
							  $this->latitude,
							  $this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->advertiser_id = (isset($_POST['advertiser_id']) ? $_POST['advertiser_id'] : '');
		$this->enabled = (isset($_POST['enabled']) ? $_POST['enabled'] : '');
		$this->location_name = (isset($_POST['location_name']) ? $_POST['location_name'] : '');
		$this->hours_of_operation = (isset($_POST['hours_of_operation']) ? serialize($_POST['hours_of_operation']) : '');
		$this->products_services = (isset($_POST['products_services']) ? $_POST['products_services'] : '');
		$this->description = (isset($_POST['description']) ? $_POST['description'] : '');
		$this->hide_address = (isset($_POST['hide_address']) ? $_POST['hide_address'] : '');
		$this->payment_options = (isset($_POST['payment_options']) ? serialize($_POST['payment_options']) : '');
		$this->website = (isset($_POST['website']) ? $_POST['website'] : '');
		$this->address_1 = (isset($_POST['address_1']) ? $_POST['address_1'] : '');
		$this->address_2 = (isset($_POST['address_2']) ? $_POST['address_2'] : '');
		$this->city = (isset($_POST['city']) ? $_POST['city'] : '');
		$this->state = (isset($_POST['state']) ? $_POST['state'] : '');
		$this->zip = (isset($_POST['zip']) ? $_POST['zip'] : '');
		$this->phone_number = (isset($_POST['phone_number']) ? $_POST['phone_number'] : '');
		$this->fax_number = (isset($_POST['fax_number']) ? $_POST['fax_number'] : '');
		$this->email_address = (isset($_POST['email_address']) ? $_POST['email_address'] : '');
		$this->longitude = (isset($_POST['longitude']) ? $_POST['longitude'] : '');
		$this->latitude = (isset($_POST['latitude']) ? $_POST['latitude'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
		global $dbh;
		
		$sql_query = "SELECT
						id,
						advertiser_id,
						enabled,
						location_name,
						hours_of_operation,
						products_services,
						description,
						hide_address,
						payment_options,
						website,
						address_1,
						address_2,
						city,
						state,
						zip,
						phone_number,
						fax_number,
						email_address,
						longitude,
						latitude
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
		  $this->advertiser_id = $row['advertiser_id'];
		  $this->enabled = $row['enabled'];
		  $this->location_name = $row['location_name'];
		  $this->hours_of_operation = unserialize($row['hours_of_operation']);
		  $this->products_services = $row['products_services'];
		  $this->description = $row['description'];
		  $this->hide_address = $row['hide_address'];
		  $this->payment_options = unserialize($row['payment_options']);
		  $this->website = $row['website'];
		  $this->address_1 = $row['address_1'];
		  $this->address_2 = $row['address_2'];
		  $this->city = $row['city'];
		  $this->state = $row['state'];
		  $this->zip = $row['zip'];
		  $this->phone_number = $row['phone_number'];
		  $this->fax_number = $row['fax_number'];
		  $this->email_address = $row['email_address'];
		  $this->longitude = $row['longitude'];
		  $this->latitude = $row['latitude'];
		} else {
		  $this->reset_vars();
		}
	}
	
	// added to get long-lat data
	private function set_long_lat() {
		
		$obj_google=new googleRequest;
		
		$obj_google->address = $this->address_1;
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