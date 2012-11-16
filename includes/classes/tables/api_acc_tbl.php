<?PHP

// class for interacting with api_access table
class api_acc_tbl {
	public $id;
	public $apikey;
	public $name;
	public $image;
	public $website;
	public $show_address;
	public $address;
	public $address1;
	public $city;
	public $state;
	public $zip;
	public $enabled;
	public $show_all;
	public $password;
	public $ptpassword;
	// table name used throughout queries within page
	private $tbl_nme = "api_access";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->apikey = $this->generate_api_key();
		$this->name = NULL;
		$this->image = NULL;
		$this->website = NULL;
		$this->show_address = NULL;
		$this->address = NULL;
		$this->address1 = NULL;
		$this->city = NULL;
		$this->state = NULL;
		$this->zip = NULL;
		$this->enabled = NULL;
		$this->hide_header = NULL;
		$this->hide_footer = NULL;
		$this->show_all = NULL;
		$this->password = NULL;
		$this->ptpassword = NULL;
	}
	
	// insert new state
	public function insert() {
			global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						apikey,
						name,
						image,
						website,
						show_address,
						address,
						address1,
						city,
						state,
						zip,
						enabled,
						hide_header,
						hide_footer,
						show_all,
						password,
						ptpassword
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
						?
					 );";
				 
		$update_vals = array(
							$this->apikey,
							$this->name,
							$this->image,
							$this->website,
							$this->show_address,
							$this->address,
							$this->address1,
							$this->city,
							$this->state,
							$this->zip,
							$this->enabled,
							$this->hide_header,
							$this->hide_footer,
							$this->show_all,
							$this->password,
							$this->ptpassword
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing state
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						apikey = ?,
						name = ?,
						image = ?,
						website = ?,
						show_address = ?,
						address = ?,
						address1 = ?,
						city = ?,
						state = ?,
						zip = ?,
						enabled = ?,
						hide_header = ?,
						hide_footer = ?,
						show_all = ?,
						password = ?,
						ptpassword = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->apikey,
							$this->name,
							$this->image,
							$this->website,
							$this->show_address,
							$this->address,
							$this->address1,
							$this->city,
							$this->state,
							$this->zip,
							$this->enabled,
							$this->hide_header,
							$this->hide_footer,
							$this->show_all,
							$this->password,
							$this->ptpassword,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->apikey = (isset($_POST['apikey']) ? $_POST['apikey'] : '');
		$this->name = (isset($_POST['name']) ? $_POST['name'] : '');
		$this->website = (isset($_POST['website']) ? $_POST['website'] : '');
		$this->show_address = (isset($_POST['show_address']) ? $_POST['show_address'] : '');
		$this->address = (isset($_POST['address']) ? $_POST['address'] : '');
		$this->address1 = (isset($_POST['address1']) ? $_POST['address1'] : '');
		$this->city = (isset($_POST['city']) ? $_POST['city'] : '');
		$this->state = (isset($_POST['state']) ? $_POST['state'] : '');
		$this->zip = (isset($_POST['zip']) ? $_POST['zip'] : '');
		$this->enabled = (isset($_POST['enabled']) ? $_POST['enabled'] : '');
		$this->hide_header = (isset($_POST['hide_header']) ? $_POST['hide_header'] : '');
		$this->hide_footer = (isset($_POST['hide_footer']) ? $_POST['hide_footer'] : '');
		$this->show_all = (isset($_POST['show_all']) ? $_POST['show_all'] : '');
		
		$this->password = (!empty($_POST['ptpassword']) ? encrypt_password($_POST['ptpassword']) : '');
		$this->ptpassword = (!empty($_POST['ptpassword']) ? $_POST['ptpassword'] : '');
		
		// upload new image
		$target_path = API_IMAGES_DIRECTORY . md5($_POST['name']) . "-" . basename( $_FILES['image']['name']); 
		if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
			$this->image = md5($_POST['name']) . "-" . basename( $_FILES['image']['name']);
		} else {
			$this->image = $_POST['old_image'];
		}
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						apikey,
						name,
						image,
						website,
						show_address,
						address,
						address1,
						city,
						state,
						zip,
						enabled,
						hide_header,
						hide_footer,
						show_all,
						password,
						ptpassword
					 FROM
						".$this->tbl_nme."
					 WHERE
						id = ?
					  LIMIT 1;";
		
		$update_vals = array(
							$id
							);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($update_vals);
		$row = $result->fetchRow();
				
		$this->id = $row['id'];
		$this->apikey = $row['apikey'];
		$this->name = $row['name'];
		$this->image = $row['image'];
		$this->website = $row['website'];
		$this->show_address = $row['show_address'];
		$this->address = $row['address'];
		$this->address1 = $row['address1'];
		$this->city = $row['city'];
		$this->state = $row['state'];
		$this->zip = $row['zip'];
		$this->enabled = $row['enabled'];
		$this->hide_header = $row['hide_header'];
		$this->hide_footer = $row['hide_footer'];
		$this->show_all = $row['show_all'];
		$this->password = $row['password'];
		$this->ptpassword = $row['ptpassword'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database if api user assigned domains match
	public function api_usr_dom_get($website) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						apikey,
						name,
						image,
						website,
						show_address,
						address,
						address1,
						city,
						state,
						zip,
						enabled,
						hide_header,
						hide_footer,
						show_all,
						password,
						ptpassword
					 FROM
						".$this->tbl_nme."
					 WHERE
						website like '%?%'
					  LIMIT 1;";
		
		$update_vals = array(
							$website
							);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($update_vals);
		$row = $result->fetchRow();
				
		$this->id = $row['id'];
		$this->apikey = $row['apikey'];
		$this->name = $row['name'];
		$this->image = $row['image'];
		$this->website = $row['website'];
		$this->show_address = $row['show_address'];
		$this->address = $row['address'];
		$this->address1 = $row['address1'];
		$this->city = $row['city'];
		$this->state = $row['state'];
		$this->zip = $row['zip'];
		$this->enabled = $row['enabled'];
		$this->hide_header = $row['hide_header'];
		$this->hide_footer = $row['hide_footer'];
		$this->show_all = $row['show_all'];
		$this->password = $row['password'];
		$this->ptpassword = $row['ptpassword'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function key_search($apikey) {
			global $dbh;
		
		$sql_query = "SELECT
						id
					 FROM
						".$this->tbl_nme."
					 WHERE
						apikey = ?
					 LIMIT 1;";

		$values = array(
						$apikey
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->get_db_vars($row['id']);

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
	
	function randomPrefix($length) {
	  $random= "";
	  
	  srand((double)microtime()*1000000);
	  
	  $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
	  $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
	  $data .= "0FGH45OP89";
	  
	  for($i = 0; $i < $length; $i++) {
		$random .= substr($data, (rand()%(strlen($data))), 1);
	  }
	  
	return $random;
	} 
	
	// generates api user key
	public function generate_api_key() {
			global $dbh;
		
		$cur_apikey = $this->randomPrefix(18);
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE 
					 	apikey = ? 
					 LIMIT 1;";
	
		$values = array(
						$cur_apikey
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$row_count = $row['rcount'];
		
		if (empty($row_count)) {
			$aff_code = $cur_apikey;
		} else {
			$this->generate_api_key();
		}

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
		
	return $aff_code;
	}
}

?>
