<?PHP

// class for interacting with customer_coupons table
class cust_cpns_tbl {
	public $id;
	public $code;
	public $value;
	public $expires;
	public $used;
	public $used_by_cust_id;
	public $used_date;
	public $added;
	// table name used throughout queries within page
	private $tbl_nme = "customer_coupons";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->code = NULL;
		$this->value = NULL;
		$this->expires = NULL;
		$this->used = NULL;
		$this->used_by_cust_id = NULL;
		$this->used_date = NULL;
		$this->added = NULL;
	}
	
	// insert new field
	public function insert() {
			global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						code,
						value,
						expires,
						used,
						used_by_cust_id,
						used_date
					 )
					 VALUES
					 (
						?,
						?,
						?,
						?,
						?,
						?
					 );";
				 
		$update_vals = array(
							$this->code,
							$this->value,
							$this->expires,
							$this->used,
							$this->used_by_cust_id,
							$this->used_date,
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing customer_coupons
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						code = ?,
						value = ?,
						expires = ?,
						used = ?,
						used_by_cust_id = ?,
						used_date = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->code,
							$this->value,
							$this->expires,
							$this->used,
							$this->used_by_cust_id,
							$this->used_date,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing customer_coupons
	public function update_coupon_cust() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						used = ?,
						used_by_cust_id = ?,
						used_date = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->used,
							$this->used_by_cust_id,
							$this->used_date,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->code = (isset($_POST['code']) ? $_POST['code'] : '');
		$this->value = (isset($_POST['value']) ? $_POST['value'] : '');
		$this->expires = (isset($_POST['expires']) ? $_POST['expires'] : '');
		$this->used = (isset($_POST['used']) ? $_POST['used'] : '');
		$this->used_by_cust_id = (isset($_POST['used_by_cust_id']) ? $_POST['used_by_cust_id'] : '');
		$this->used_date = (isset($_POST['used_date']) ? $_POST['used_date'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						code,
						value,
						expires,
						used,
						used_by_cust_id,
						used_date,
						added
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
		$this->code = $row['code'];
		$this->value = $row['value'];
		$this->expires = $row['expires'];
		$this->used = $row['used'];
		$this->used_by_cust_id = $row['used_by_cust_id'];
		$this->used_date = $row['used_date'];
		$this->added = $row['added'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function look_up_coupon($coupon_code) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						code,
						value,
						expires,
						used,
						used_by_cust_id,
						used_date,
						added
					 FROM
						".$this->tbl_nme."
					 WHERE
						code = ?
					  LIMIT 1;";

		$values = array(
						$coupon_code
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->code = $row['code'];
		$this->value = $row['value'];
		$this->expires = $row['expires'];
		$this->used = $row['used'];
		$this->used_by_cust_id = $row['used_by_cust_id'];
		$this->used_date = $row['used_date'];
		$this->added = $row['added'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function existing_code_check() {
			global $dbh;
		
		$values = array();
		
		$sql_query = "SELECT
						count(*) as cnt
					 FROM
						".$this->tbl_nme."
					 WHERE ";
		if ($this->id > 0) {
		  $sql_query .= " id <> ?
					   AND ";
		  $values[] = $this->id;
		}
		$sql_query .= "code = ?
					  LIMIT 1;";
		$values[] = $this->code;
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$num_found = $row['cnt'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
		
	return $num_found;
	}
}

?>