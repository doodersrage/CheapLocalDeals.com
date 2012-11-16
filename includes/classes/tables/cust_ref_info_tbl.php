<?PHP

// class for interacting with customer_referel_info table
class cust_ref_info_tbl {
	public $id;
	public $customer_id;
	public $ref_code;
	public $credit_amt;
	public $date_added;
	// table name used throughout queries within page
	private $tbl_nme = "customer_referel_info";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->customer_id = NULL;
		$this->ref_code = NULL;
		$this->credit_amt = NULL;
		$this->date_added = NULL;
	}
	
	// insert new customer_promo_codes
	public function insert() {
		global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						customer_id,
						ref_code,
						credit_amt
					 )
					 VALUES
					 (
						?,
						?,
						?
					 );";
				 
		$update_vals = array(
							$this->customer_id,
							$this->ref_code,
							$this->credit_amt
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing customer_promo_codes
	public function update() {
		global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						ref_code = ?,
						credit_amt = ?,
						customer_id = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->ref_code,
							$this->credit_amt,
							$this->customer_id,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->customer_id = (isset($_POST['customer_id']) ? $_POST['customer_id'] : '');
		$this->ref_code = (isset($_POST['ref_code']) ? $_POST['ref_code'] : '');
		$this->credit_amt = (isset($_POST['credit_amt']) ? $_POST['credit_amt'] : '');
		$this->date_added = (isset($_POST['date_added']) ? $_POST['date_added'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
		global $dbh;
		
		$sql_query = "SELECT
						customer_id,
						ref_code,
						credit_amt,
						date_added,
						customer_id
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
		$this->customer_id = $row['customer_id'];
		$this->ref_code = $row['ref_code'];
		$this->credit_amt = $row['credit_amt'];
		$this->date_added = $row['date_added'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function assign_db_vars_refcode($ref_code) {
		global $dbh;
		
		$sql_query = "SELECT
						customer_id,
						ref_code,
						credit_amt,
						date_added
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
		
		$this->customer_id = $row['customer_id'];
		$this->ref_code = $row['ref_code'];
		$this->credit_amt = $row['credit_amt'];
		$this->date_added = $row['date_added'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// check for existing ref code when inserting a new one
	public function ref_code_chk($ref_code) {
		global $dbh;
		
		$sql_query = "SELECT
						count(*) as cnt
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
		
		if ($row['cnt'] > 0) {
			$found = $row['cnt'];
		} else {
			$found = 0;
		}

		// clear result set
		$result->free();
		
	return $found;
	}
}

?>