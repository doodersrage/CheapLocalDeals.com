<?PHP

// class for interacting with orders table
class memb_proc_tbl {
	public $id;
	public $advertiser_id;
	public $advertiser_level;
	public $date;
	public $payment;
	public $payment_method;
	public $cc_type;
	public $payment_approved;
	public $other_info;
	// table name used throughout queries within page
	private $tbl_nme = "membership_process";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->advertiser_id = NULL;
		$this->advertiser_level = NULL;
		$this->date = NULL;
		$this->payment = NULL;
		$this->payment_method = NULL;
		$this->cc_type = NULL;
		$this->payment_approved = NULL;
		$this->other_info = NULL;
	}
	
	// insert new membership_process
	public function insert() {
			global $dbh;
						
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						advertiser_id,
						advertiser_level,
						date,
						payment,
						payment_method,
						cc_type,
						payment_approved,
						other_info
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
						?
					 );";
				 
		$update_vals = array(
							$this->advertiser_id,
							$this->advertiser_level,
							$today,
							$this->payment,
							$this->payment_method,
							$this->cc_type,
							$this->payment_approved,
							$this->other_info,
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing membership_process
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						advertiser_id = ?,
						advertiser_level = ?,
						payment = ?,
						payment_method = ?,
						cc_type = ?,
						payment_approved = ?,
						other_info = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->advertiser_id,
							$this->advertiser_level,
							$this->payment,
							$this->payment_method,
							$this->cc_type,
							$this->payment_approved,
							$this->other_info,
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->advertiser_id = (isset($_POST['advertiser_id']) ? $_POST['advertiser_id'] : '');
		$this->advertiser_level = (isset($_POST['advertiser_level']) ? $_POST['advertiser_level'] : '');
		$this->payment = (isset($_POST['payment']) ? $_POST['payment'] : '');
		$this->payment_method = (isset($_POST['payment_method']) ? $_POST['payment_method'] : '');
		$this->cc_type = (isset($_POST['cc_type']) ? $_POST['cc_type'] : '');
		$this->payment_approved = (isset($_POST['payment_approved']) ? $_POST['payment_approved'] : '');
		$this->other_info = (isset($_POST['other_info']) ? $_POST['other_info'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						advertiser_id,
						advertiser_level,
						date,
						payment,
						payment_method,
						cc_type,
						payment_approved,
						other_info
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
		$this->advertiser_id = $row['advertiser_id'];
		$this->advertiser_level = $row['advertiser_level'];
		$this->date = $row['date'];
		$this->payment = $row['payment'];
		$this->payment_method = $row['payment_method'];
		$this->cc_type = $row['cc_type'];
		$this->payment_approved = $row['payment_approved'];
		$this->other_info = $row['other_info'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
}

?>