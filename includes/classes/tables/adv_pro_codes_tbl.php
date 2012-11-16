<?PHP

// class for interacting with advert_promo_codes table
class adv_pro_codes_tbl {
	public $id;
	public $promo_code;
	public $allowed_access;
	public $updated;
	// table name used throughout queries within page
	private $tbl_nme = "advert_promo_codes";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->promo_code = NULL;
		$this->allowed_access = NULL;
		$this->updated = NULL;
	}
	
	// insert new advert_promo_codes
	public function insert() {
		global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						promo_code,
						percentage
					 )
					 VALUES
					 (
						?,
						?
					 );";
				 
		$update_vals = array(
							$this->promo_code,
							$this->percentage
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing advert_promo_codes
	public function update() {
		global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						promo_code = ?,
						percentage = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->promo_code,
							$this->percentage,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->promo_code = (isset($_POST['promo_code']) ? $_POST['promo_code'] : '');
		$this->percentage = $_POST['percentage'];
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
		global $dbh;
		
		$sql_query = "SELECT
						id,
						promo_code,
						percentage,
						updated
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
		$this->promo_code = $row['promo_code'];
		$this->percentage = $row['percentage'];
		$this->updated = $row['updated'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function assign_db_vars_procode($promo_code) {
		global $dbh;
		
		$sql_query = "SELECT
						id,
						promo_code,
						percentage,
						updated
					 FROM
						".$this->tbl_nme."
					 WHERE
						promo_code = ?
					  LIMIT 1;";

		$values = array(
						$promo_code
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->promo_code = $row['promo_code'];
		$this->percentage = $row['percentage'];
		$this->updated = $row['updated'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// check for existing promo code when inserting a new one
	public function promo_code_chk($promo_code) {
		global $dbh;
		
		$sql_query = "SELECT
						count(*) as cnt
					 FROM
						".$this->tbl_nme."
					 WHERE
						promo_code = ?
					  LIMIT 1;";

		$values = array(
						$promo_code
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