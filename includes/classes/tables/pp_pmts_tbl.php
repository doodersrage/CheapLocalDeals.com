<?PHP

// class for interacting with paypal_payments table
class pp_pmts_tbl {
	public $id;
	public $session_id;
	public $token;
	public $amount;
	public $date;
	public $approved;
	public $api_id;
	// table name used throughout queries within page
	private $tbl_nme = "paypal_payments";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->session_id = NULL;
		$this->token = NULL;
		$this->amount = NULL;
		$this->date = NULL;
		$this->approved = NULL;
		$this->api_id = NULL;
	}
	
	// insert new orders
	public function insert() {
			global $dbh;
						
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						session_id,
						token,
						amount,
						date,
						api_id
					 )
					 VALUES
					 (
						?,
						?,
						?,
						?,
						?
					 );";
				 
		$update_vals = array(
							$this->session_id,
							$this->token,
							$this->amount,
							$today,
							$this->api_id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing orders
	public function update() {
			global $dbh;
		
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						session_id = ?,
						token = ?,
						amount = ?,
						date = ?,
						approved = ?,
						api_id = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->session_id,
							$this->token,
							$this->amount,
							$today,
							$this->approved,
							$this->api_id,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						session_id,
						token,
						amount,
						date,
						approved,
						api_id
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
		$this->session_id = $row['session_id'];
		$this->token = $row['token'];
		$this->amount = $row['amount'];
		$this->date = $row['date'];
		$this->api_id = $row['api_id'];
		$this->approved = $row['approved'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function assign_db_vars_session_id($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						session_id,
						token,
						amount,
						date,
						approved,
						api_id
					 FROM
						".$this->tbl_nme."
					 WHERE
						session_id = ?
					  LIMIT 1;";

		$values = array(
						$id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->session_id = $row['session_id'];
		$this->token = $row['token'];
		$this->amount = $row['amount'];
		$this->date = $row['date'];
		$this->approved = $row['approved'];
		$this->api_id = $row['api_id'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function assign_db_vars_token_an_session_id($id,$token) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						session_id,
						token,
						amount,
						date,
						approved,
						api_id
					 FROM
						".$this->tbl_nme."
					 WHERE
						session_id = ?
					 AND
						token = ?
					  LIMIT 1;";

		$values = array(
						$id,
						$token
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->session_id = $row['session_id'];
		$this->token = $row['token'];
		$this->amount = $row['amount'];
		$this->date = $row['date'];
		$this->approved = $row['approved'];
		$this->api_id = $row['api_id'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function assign_db_vars_token($token) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						session_id,
						token,
						amount,
						date,
						approved,
						api_id
					 FROM
						".$this->tbl_nme."
					 WHERE
						token = ?
					  LIMIT 1;";

		$values = array(
						$token
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->session_id = $row['session_id'];
		$this->token = $row['token'];
		$this->amount = $row['amount'];
		$this->date = $row['date'];
		$this->approved = $row['approved'];
		$this->api_id = $row['api_id'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
}

?>