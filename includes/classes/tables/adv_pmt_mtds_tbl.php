<?PHP

// class for interacting with advertiser_payment_methods table
class adv_pmt_mtds_tbl {
	public $id;
	public $method;
	public $image;
	// table name used throughout queries within page
	private $tbl_nme = "advertiser_payment_methods";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->method = NULL;
	}
	
	// insert new advertiser_payment_methods
	public function insert() {
			global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						method,
						image
					 )
					 VALUES
					 (
						?,
						?
					 );";
				 
		$update_vals = array(
							$this->method,
							$this->image
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing advertiser_payment_methods
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						method = ?,
						image = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->method,
							$this->image,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->method = (isset($_POST['method']) ? $_POST['method'] : '');
		
		// upload new image
		$target_path = IMAGES_DIRECTORY.'payment_logos/'. md5($_POST['method']) . "-" . basename( $_FILES['image']['name']); 
		if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
			$this->image = md5($_POST['method']) . "-" . basename( $_FILES['image']['name']);
		} else {
			$this->image = $_POST['old_image'];
		}
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						method,
						image
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
		$this->method = $row['method'];
		$this->image = $row['image'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function get_all() {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						method,
						image
					 FROM
						".$this->tbl_nme."
					 ;";
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute();
	
		$results = array();
		while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$results[] = $row;
		}

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
		
	return $results;
	}

}

?>