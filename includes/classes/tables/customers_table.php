<?PHP

// class for interacting with customers table

class customers_table {
	public $id;
	public $longitude;
	public $latitude;
	public $password;
	public $first_name;
	public $last_name;
	public $address_1;
	public $address_2;
	public $city;
	public $state;
	public $zip;
	public $phone_number;
	public $fax_number;
	public $email_address;
	public $account_enabled;
	public $date_created;
	public $last_ip;
	public $last_login;
	public $last_session_id;
	public $balance;
	public $ref_code;
	public $api_id;
	public $usr_ref_code;
	public $header_data;
	// table name used throughout queries within page
	private $tbl_nme = "customer_info";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->longitude = NULL;
		$this->latitude = NULL;
		$this->password = NULL;
		$this->first_name = NULL;
		$this->last_name = NULL;
		$this->address_1 = NULL;
		$this->address_2 = NULL;
		$this->city = NULL;
		$this->state = NULL;
		$this->zip = NULL;
		$this->phone_number = NULL;
		$this->fax_number = NULL;
		$this->email_address = NULL;
		$this->account_enabled = NULL;
		$this->date_created = NULL;
		$this->last_ip = NULL;
		$this->last_login = NULL;
		$this->last_session_id = NULL;
		$this->balance = NULL;
		$this->ref_code = NULL;
		$this->api_id = NULL;
		$this->usr_ref_code = NULL;
		$this->header_data = NULL;
	}


	// insert new retail customer
	public function insert() {
			global $dbh;
				
		$this->set_long_lat();
		
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						longitude,
						latitude,
						password,
						first_name,
						last_name,
						address_1,
						address_2,
						city,
						state,
						zip,
						phone_number,
						fax_number,
						email_address,
						account_enabled,
						balance,
						date_created,
						ref_code,
						api_id,
						usr_ref_code,
						header_data
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
						 ?,
						 ?
					 );
					 ";
				 
		$update_vals = array(
							$this->longitude,
							$this->latitude,
							$this->password,
							$this->first_name,
							$this->last_name,
							$this->address_1,
							$this->address_2,
							$this->city,
							$this->state,
							$this->zip,
							$this->phone_number,
							$this->fax_number,
							$this->email_address,
							$this->account_enabled,
							$this->balance,
							$today,
							$this->ref_code,
							$this->api_id,
							$this->usr_ref_code,
							serialize($_SERVER)
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// update existing retail customer
	public function update() {
			global $dbh;
		
		$this->set_long_lat();
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						longitude = ?,
						latitude = ?,
						password = ?,
						first_name = ?,
						last_name = ?,
						address_1 = ?,
						address_2 = ?,
						city = ?,
						state = ?,
						zip = ?,
						phone_number = ?,
						fax_number = ?,
						email_address = ?,
						account_enabled = ?,
						balance = ?,
						ref_code = ?,
						api_id = ?,
						usr_ref_code = ?,
						header_data = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->longitude,
							$this->latitude,
							$this->password,
							$this->first_name,
							$this->last_name,
							$this->address_1,
							$this->address_2,
							$this->city,
							$this->state,
							$this->zip,
							$this->phone_number,
							$this->fax_number,
							$this->email_address,
							$this->account_enabled,
							$this->balance,
							$this->ref_code,
							$this->api_id,
							$this->usr_ref_code,
							serialize($_SERVER),
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// update existing retail customer
	public function update_address() {
			global $dbh;
				
		$this->set_long_lat();
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						first_name = ?,
						last_name = ?,
						address_1 = ?,
						address_2 = ?,
						city = ?,
						state = ?,
						zip = ?,
						phone_number = ?,
						fax_number = ?,
						email_address = ?,
						longitude = ?,
						latitude = ?,
						header_data = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->first_name,
							$this->last_name,
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
							serialize($_SERVER),
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// update customer balance amount
	public function update_balance() {
			global $dbh;
						
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						balance = ?,
						header_data = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->balance,
							serialize($_SERVER),
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// check for an existing customer password
	public function pull_existing_password($cust_id) {
			global $dbh;
	
		$sql_query = "SELECT
						password
					 FROM
						".$this->tbl_nme."
					 WHERE
						id = ?
					  LIMIT 1;";

		$values = array(
						$cust_id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
			
		$password = $row['password'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
		
	return $password;
	}

	// write post vars to class variables
	public function get_post_vars() {
		
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		
		$this->longitude = (isset($_POST['longitude']) ? $_POST['longitude'] : '');
		$this->latitude = (isset($_POST['latitude']) ? $_POST['latitude'] : '');
		$this->password = (!empty($_POST['password']) ? encrypt_password($_POST['password']) : $this->pull_existing_password($_POST['id']));
		
		$this->first_name = (isset($_POST['first_name']) ? ucfirst($_POST['first_name']) : '');
		$this->last_name = (isset($_POST['last_name']) ? ucfirst($_POST['last_name']) : '');
		$this->address_1 = (isset($_POST['address_1']) ? $_POST['address_1'] : '');
		$this->address_2 = (isset($_POST['address_2']) ? $_POST['address_2'] : '');
		$this->city = (isset($_POST['city']) ? $_POST['city'] : '');
		$this->state = (isset($_POST['state']) ? $_POST['state'] : '');
		$this->zip = (isset($_POST['zip']) ? $_POST['zip'] : '');
		$this->phone_number = (isset($_POST['phone_number']) ? $_POST['phone_number'] : '');
		$this->fax_number = (isset($_POST['fax_number']) ? $_POST['fax_number'] : '');
		$this->email_address = (isset($_POST['email_address']) ? $_POST['email_address'] : '');
		
		$this->account_enabled = (isset($_POST['account_enabled']) ? $_POST['account_enabled'] : '');
		$this->balance = (isset($_POST['balance']) ? $_POST['balance'] : '');
		$this->ref_code = (!empty($_POST['ref_code']) ? $_POST['ref_code'] : $this->generate_ref_code());
		$this->api_id = (isset($_POST['api_id']) ? $_POST['api_id'] : '');
		$this->usr_ref_code = (isset($_POST['usr_ref_code']) ? $_GET['usr_ref_code'] : '');
		
	}
	
	// get vars from database
	public function get_db_vars($id) {
		global $dbh;
		
		$sql_query = "SELECT
						id,
						longitude,
						latitude,
						password,
						first_name,
						last_name,
						address_1,
						address_2,
						city,
						state,
						zip,
						phone_number,
						fax_number,
						email_address,
						account_enabled,
						date_created,
						last_ip,
						last_login,
						last_session_id,
						balance,
						date_created,
						ref_code,
						api_id,
						usr_ref_code,
						header_data
					 FROM
						".$this->tbl_nme."
					 WHERE
						id = ?
					  LIMIT 1;";

		$values = array(
						$id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
			
		$this->id = $row['id'];
		$this->longitude = $row['longitude'];
		$this->latitude = $row['latitude'];
		$this->password = $row['password'];
		$this->first_name = $row['first_name'];
		$this->last_name = $row['last_name'];
		$this->address_1 = $row['address_1'];
		$this->address_2 = $row['address_2'];
		$this->city = $row['city'];
		$this->state = $row['state'];
		$this->zip = $row['zip'];
		$this->phone_number = $row['phone_number'];
		$this->fax_number = $row['fax_number'];
		$this->email_address = $row['email_address'];
		$this->account_enabled = $row['account_enabled'];
		$this->last_ip = $row['last_ip'];
		$this->last_login = $row['last_login'];
		$this->last_session_id = $row['last_session_id'];
		$this->balance = $row['balance'];
		$this->date_created = $row['date_created'];
		$this->ref_code = $row['ref_code'];
		$this->api_id = $row['api_id'];
		$this->usr_ref_code = $row['usr_ref_code'];
		$this->header_data = unserialize($row['header_data']);

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	
	}
	
	// get vars from database
	public function ref_code_id_srch($ref_code) {
			global $dbh;
		
		$sql_query = "SELECT
						id
					 FROM
						".$this->tbl_nme."
					 WHERE
						ref_code = ?
					  LIMIT 1;";

		$values = array(
						$ref_code
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
			
		$id = $row['id'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	
	return $id;
	}
	
	// update customer password
	public function change_password_check() {
			global $dbh;
			
		$new_pass = $this->password;
			
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						password = ?
					 WHERE
						id = ?";
		$update_vals = array(
							encrypt_password($this->password),
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
		$this->get_db_vars($this->id);
		
		// add account password change email
		$message = new Mail_mime();
		$html = '<p>Your password has been changed to: '.$new_pass.'</p>';
		
		//$message->setTXTBody($text);
		$message->setHTMLBody($html);
		$body = $message->get();
		$extraheaders = array("From"=>SITE_FROM_ADDRESS, "Subject"=>SITE_NAME_VAL." Password Change ".date("m-d-Y"));
		$headers = $message->headers($extraheaders);
		
		$mail = Mail::factory("mail");
		$mail->send($this->email_address, $headers, $body);
	}
	
	// user login check
	public function user_forget_password_check() {
			global $dbh;
		
		$sql_query = "SELECT
					id,
					email_address
				 FROM
					".$this->tbl_nme."
				 WHERE
					email_address = ? AND
					account_enabled = 1
				  LIMIT 1;";

		$values = array(
						$_POST['email']
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		if ($row['id'] > 0) {
		
			$new_pass = rand(11111111,99999999);
		
			$sql_query = "UPDATE
							".$this->tbl_nme."
						 SET
							password = ?
						 WHERE
							id = ?";
			$update_vals = array(
								encrypt_password($new_pass),
								$row['id']
								);
				
			$stmt = $dbh->prepare($sql_query);
			$stmt->execute($update_vals);
				
			// add account password change email
			$html = '<p>Your password has been changed to: '.$new_pass.'</p>';
			
			$email_data["from_address"] = SITE_FROM_ADDRESS;
			$email_data['to_addresses'] = $row['email_address'];
			$email_data["subject"] = SITE_NAME_VAL." Password Change ".date("m-d-Y");
			$email_data['content'] = $html;
			send_email($email_data);
		}

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
		
	return $row['id'];
	}
	
	// user login check
	public function user_login_check() {
		global $dbh;
		
		$sql_query = "SELECT
						id
					 FROM
						".$this->tbl_nme."
					 WHERE
						email_address = ? AND
						password = ? AND
						account_enabled = 1
					  LIMIT 1;";

		$values = array(
						$_POST['email_address'],
						encrypt_password($_POST['password'])
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		if ($row['id'] > 0) {
			$sql_query = "UPDATE
							".$this->tbl_nme."
						 SET
							last_ip = ?,
							last_login = ?,
							last_session_id = ?
						 WHERE
							id = ?";
			$update_vals = array(
								$_SERVER['REMOTE_ADDR'],
								date("Y-m-d"),
								session_id(),
								$row['id']
								);
			$stmt = $dbh->prepare($sql_query);
			$stmt->execute($update_vals);
			$_SESSION['customer_logged_in'] = 1;
			$_SESSION['customer_id'] = $row['id'];
			setcookie("email_address", $_POST['email_address']);
		}

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
	
	// user login session check
	public function user_login_session_check() {
			global $dbh;
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE
						last_session_id = ?
					 AND
						id = ?
					 AND
						last_ip = ?
					  LIMIT 1;";

		$values = array(
						session_id(),
						$_SESSION['customer_id'],
						$_SERVER['REMOTE_ADDR']
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);

		// clear result set
		$result->free();
				
	return $row['rcount'];
	}
	
	// check for existing email
	public function email_check() {
		global $dbh;
		
		$values = array();

		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE email_address = ? ";

		$values[] = $_POST['email_address'];
				 
		if (!empty($_POST['id'])) {
			$sql_query .= "AND id <> ? ";
			$values[] = $_POST['id'];
		}
				 
		$sql_query .= " LIMIT 1;";
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$rowscount = $result->fetchRow(MDB2_FETCHMODE_ASSOC);

		$row_count = $rowscount['rcount'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
		
	return $row_count;
	}
	
	// generates customer affiliate code
	public function generate_affiliate_code() {
			global $dbh;
		
		$cur_affiliate_code = rand(11111111,99999999);
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE affiliate_code = ? 
					  LIMIT 1;";

		$values = array(
						$cur_affiliate_code
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$rowscount = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$row_count = $rowscount['rcount'];
		
		if (empty($row_count)) {
			$aff_code = $cur_affiliate_code;
		} else {
			$this->generate_affiliate_code();
		}

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
		
	return $aff_code;
	}
	
	// generates customer referral code
	public function generate_ref_code() {
		global $dbh;
		
		$cur_ref_code = getUniqueCode(15);
				
		$row_count = $this->ref_code_chk($cur_ref_code);
		
		if (empty($row_count)) {
			$ref_code = $cur_ref_code;
		} else {
			$this->generate_ref_code();
		}
		
	return $ref_code;
	}
	
	// check if ref code exists
	public function ref_code_chk($cur_ref_code){
		global $dbh;
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE ref_code = ? 
					  LIMIT 1;";

		$values = array(
						$cur_ref_code
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$rowscount = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$row_count = $rowscount['rcount'];
		
		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	
	return $row_count;
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