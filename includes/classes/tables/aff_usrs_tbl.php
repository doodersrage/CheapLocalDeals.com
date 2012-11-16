<?PHP

// class for interacting with affiliate_users table
class aff_usrs_tbl {
	public $id;
	public $name;
	public $company;
	public $affiliate_code;
	// table name used throughout queries within page
	private $tbl_nme = "affiliate_users";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->name = NULL;
		$this->company = NULL;
		$this->affiliate_code = NULL;
	}
	
	// insert new affiliate user
	public function insert() {
			global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						name,
						affiliate_code,
						company
					 )
					 VALUES
					 (
						?,
						?,
						?
					 );";
				 
		$update_vals = array(
							$this->name,
							$this->affiliate_code,
							$this->company
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing affiliate user
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						name = ?,
						affiliate_code = ?,
						company = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->name,
							$this->affiliate_code,
							$this->company,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->name = (isset($_POST['name']) ? $_POST['name'] : '');
		$this->affiliate_code = (isset($_POST['affiliate_code']) ? $_POST['affiliate_code'] : '');
		$this->company = (isset($_POST['company']) ? $_POST['company'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						name,
						affiliate_code,
						company
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
		$this->name = $row['name'];
		$this->affiliate_code = $row['affiliate_code'];
		$this->company = $row['company'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database by company and name
	public function assign_parent_name_db_vars($company,$name) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						name,
						affiliate_code,
						company
					 FROM
						".$this->tbl_nme."
					 WHERE
						company = ?
					 AND name = ?
					  LIMIT 1;";

		$values = array(
						$company,
						$name
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->name = $row['name'];
		$this->affiliate_code = $row['affiliate_code'];
		$this->company = $row['company'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
	
	// get vars from database
	public function search($affiliate_code) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						name,
						affiliate_code,
						company
					 FROM
						".$this->tbl_nme."
					 WHERE
						affiliate_code = ?
					  LIMIT 1;";

		$values = array(
						$affiliate_code
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->name = $row['name'];
		$this->affiliate_code = $row['affiliate_code'];
		$this->company = $row['company'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
	
	// get vars from database
	public function affiliate_id_check($affiliate_code) {
			global $dbh;
		
		$sql_query = "SELECT
						id
					 FROM
						".$this->tbl_nme."
					 WHERE
						affiliate_code = ?
					 AND
						id <> ?
					  LIMIT 1;";

		$values = array(
						$affiliate_code,
						$this->id
						);
				
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
	return $row['id'];
	}

}

?>