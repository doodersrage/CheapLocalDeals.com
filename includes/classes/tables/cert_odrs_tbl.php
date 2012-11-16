<?PHP

// class for interacting with certificate_orders table
class cert_odrs_tbl {
	public $id;
	public $order_id;
	public $customer_id;
	public $advertiser_id;
	public $requirements;
	public $certificate_amount_id;
	public $certificate_code;
	public $enabled;
	public $date_added;
	public $cert_id;
	public $session_id;
	public $token;
	public $excludes;
	// table name used throughout queries within page
	private $tbl_nme = "certificate_orders";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->order_id = NULL;
		$this->customer_id = NULL;
		$this->advertiser_id = NULL;
		$this->requirements = NULL;
		$this->certificate_amount_id = NULL;
		$this->certificate_code = NULL;
		$this->enabled = NULL;
		$this->date_added = NULL;
		$this->cert_id = NULL;
		$this->session_id = NULL;
		$this->token = NULL;
		$this->excludes = NULL;
	}
	
	// insert new certificate_order
	public function insert() {
			global $dbh;
		
		// generate email auth code
		$this->generate_cert_id();
						
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						order_id,
						customer_id,
						advertiser_id,
						requirements,
						certificate_amount_id,
						certificate_code,
						enabled,
						cert_id,
						session_id,
						token,
						excludes,
						date_added
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
						?);
					 ";
				 
		$update_vals = array(
							$this->order_id,
							$this->customer_id,
							$this->advertiser_id,
							$this->requirements,
							$this->certificate_amount_id,
							$this->certificate_code,
							$this->enabled,
							$this->cert_id,
							$this->session_id,
							$this->token,
							$this->excludes,
							$today
							);
														
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing zip code
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						order_id = ?,
						customer_id = ?,
						advertiser_id = ?,
						requirements = ?,
						certificate_amount_id = ?,
						certificate_code = ?,
						enabled = ?,
						cert_id = ?,
						session_id = ?,
						token = ?,
						excludes = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->order_id,
							$this->customer_id,
							$this->advertiser_id,
							$this->requirements,
							$this->certificate_amount_id,
							$this->certificate_code,
							$this->enabled,
							$this->cert_id,
							$this->session_id,
							$this->token,
							$this->excludes,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// diable selected certificate
	public function disable() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						enabled = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->enabled,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->order_id = (isset($_POST['order_id']) ? $_POST['order_id'] : '');
		$this->customer_id = (isset($_POST['customer_id']) ? $_POST['customer_id'] : '');
		$this->advertiser_id = (isset($_POST['advertiser_id']) ? $_POST['advertiser_id'] : '');
		$this->requirements = (isset($_POST['requirements']) ? $_POST['requirements'] : '');
		$this->certificate_amount_id = (isset($_POST['certificate_amount_id']) ? $_POST['certificate_amount_id'] : '');
		$this->certificate_code = (isset($_POST['certificate_code']) ? $_POST['certificate_code'] : '');
		$this->enabled = (isset($_POST['enabled']) ? $_POST['enabled'] : '');
		$this->cert_id = (isset($_POST['cert_id']) ? $_POST['cert_id'] : '');
		$this->excludes = (isset($_POST['excludes']) ? $_POST['excludes'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
		global $dbh;
		
		$sql_query = "SELECT
						id,
						order_id,
						customer_id,
						advertiser_id,
						requirements,
						certificate_amount_id,
						certificate_code,
						enabled,
						date_added,
						cert_id,
						session_id,
						token,
						excludes
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
		$this->order_id = $row['order_id'];
		$this->customer_id = $row['customer_id'];
		$this->advertiser_id = $row['advertiser_id'];
		$this->requirements = $row['requirements'];
		$this->certificate_amount_id = $row['certificate_amount_id'];
		$this->certificate_code = $row['certificate_code'];
		$this->enabled = $row['enabled'];
		$this->date_added = $row['date_added'];
		$this->cert_id = $row['cert_id'];
		$this->session_id = $row['session_id'];
		$this->token = $row['token'];
		$this->excludes = $row['excludes'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function assign_db_vars_by_cert_id($id) {
		global $dbh;
		
		$sql_query = "SELECT
						id,
						order_id,
						customer_id,
						advertiser_id,
						requirements,
						certificate_amount_id,
						certificate_code,
						enabled,
						date_added,
						cert_id,
						session_id,
						token,
						excludes
					 FROM
						".$this->tbl_nme."
					 WHERE
						cert_id = ?
					  LIMIT 1;";

		$values = array(
						$id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->order_id = $row['order_id'];
		$this->customer_id = $row['customer_id'];
		$this->advertiser_id = $row['advertiser_id'];
		$this->requirements = $row['requirements'];
		$this->certificate_amount_id = $row['certificate_amount_id'];
		$this->certificate_code = $row['certificate_code'];
		$this->enabled = $row['enabled'];
		$this->date_added = $row['date_added'];
		$this->cert_id = $row['cert_id'];
		$this->session_id = $row['session_id'];
		$this->token = $row['token'];
		$this->excludes = $row['excludes'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function lookup_by_cert_code($id,$adverid) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						order_id,
						customer_id,
						advertiser_id,
						requirements,
						certificate_amount_id,
						certificate_code,
						enabled,
						date_added,
						cert_id
						session_id,
						token,
						excludes
					 FROM
						".$this->tbl_nme."
					 WHERE
						certificate_code = ?
					 AND
						advertiser_id = ?
					  LIMIT 1;";

		$values = array(
						$id,
						$adverid
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->order_id = $row['order_id'];
		$this->customer_id = $row['customer_id'];
		$this->advertiser_id = $row['advertiser_id'];
		$this->requirements = $row['requirements'];
		$this->certificate_amount_id = $row['certificate_amount_id'];
		$this->certificate_code = $row['certificate_code'];
		$this->enabled = $row['enabled'];
		$this->date_added = $row['date_added'];
		$this->cert_id = $row['cert_id'];
		$this->session_id = $row['session_id'];
		$this->token = $row['token'];
		$this->excludes = $row['excludes'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
	
	// generates certificate code
	public function generate_certificate_code() {
			global $dbh;
		
		$cur_certificate_code = rand(11111111,99999999);
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE certificate_code = ? 
					  LIMIT 1;";
	
		$values = array(
						$cur_certificate_code
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$rowscount = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$row_count = $rowscount['rcount'];
		
		if (empty($row_count)) {
			$cert_code = $cur_certificate_code;
		} else {
			$this->generate_affiliate_code();
		}

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
		
	return $cert_code;
	}
	
	// update set email authorization code
	public function generate_cert_id() {
			global $dbh;
		
		$values = array();
		
		$cert_id_code = randgen(15);
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE cert_id = ? 
					  LIMIT 1;";
					 
		$values[] = $cert_id_code;
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);

		$row_count = $row['rcount'];
		
		if ($row_count > 0) {
			$this->generate_cert_id();
		} else {
			$this->cert_id = $cert_id_code;
		}

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
}

?>