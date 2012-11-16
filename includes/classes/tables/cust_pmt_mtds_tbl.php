<?PHP

// class for interacting with customer_payment_methods table
class cust_pmt_mtds_tbl {
	public $id;
	public $customer_id;
	public $credit_card_type;
	public $cc_number;
	public $cvv;
	public $cc_exp;
	public $check_routing_num;
	public $check_account_num;
	public $bank_name;
	public $bank_state;
	public $drivers_license_num;
	public $drivers_license_state;
	// table name used throughout queries within page
	private $tbl_nme = "customer_payment_methods";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->customer_id = NULL;
		$this->credit_card_type = NULL;
		$this->cc_number = NULL;
		$this->cvv = NULL;
		$this->cc_exp = NULL;
		$this->check_routing_num = NULL;
		$this->check_account_num = NULL;
		$this->bank_name = NULL;
		$this->bank_state = NULL;
		$this->drivers_license_num = NULL;
		$this->drivers_license_state = NULL;
	}
	
	// insert new field
	public function insert() {
			global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						customer_id,
						credit_card_type,
						cc_number,
						cvv,
						cc_exp,
						check_routing_num,
						check_account_num,
						bank_name,
						bank_state,
						drivers_license_num,
						drivers_license_state
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
						?
					 );";
				 
		$update_vals = array(
							$this->customer_id,
							$this->credit_card_type,
							$this->cc_number,
							$this->cvv,
							$this->cc_exp,
							$this->check_routing_num,
							$this->check_account_num,
							$this->bank_name,
							$this->bank_state,
							$this->drivers_license_num,
							$this->drivers_license_state
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing customer_payment_methods
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						customer_id = ?,
						credit_card_type = ?,
						cc_number = ?,
						cvv = ?,
						cc_exp = ?,
						check_routing_num = ?,
						check_account_num = ?,
						bank_name = ?,
						bank_state = ?,
						drivers_license_num = ?,
						drivers_license_state = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->customer_id,
							$this->credit_card_type,
							$this->cc_number,
							$this->cvv,
							$this->cc_exp,
							$this->check_routing_num,
							$this->check_account_num,
							$this->bank_name,
							$this->bank_state,
							$this->drivers_license_num,
							$this->drivers_license_state,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->customer_id = (isset($_POST['customer_id']) ? $_POST['customer_id'] : '');
		$this->credit_card_type = (isset($_POST['credit_card_type']) ? $_POST['credit_card_type'] : '');
		$this->cc_number = (isset($_POST['cc_number']) ? $_POST['cc_number'] : '');
		$this->cvv = (isset($_POST['cvv']) ? $_POST['cvv'] : '');
		$this->cc_exp = (isset($_POST['cc_exp']) ? $_POST['cc_exp'] : '');
		$this->check_routing_num = (isset($_POST['check_routing_num']) ? $_POST['check_routing_num'] : '');
		$this->check_account_num = (isset($_POST['check_account_num']) ? $_POST['check_account_num'] : '');
		$this->bank_name = (isset($_POST['bank_name']) ? $_POST['bank_name'] : '');
		$this->bank_state = (isset($_POST['bank_state']) ? $_POST['bank_state'] : '');
		$this->drivers_license_num = (isset($_POST['drivers_license_num']) ? $_POST['drivers_license_num'] : '');
		$this->drivers_license_state = (isset($_POST['drivers_license_state']) ? $_POST['drivers_license_state'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						customer_id,
						credit_card_type,
						cc_number,
						cvv,
						cc_exp,
						check_routing_num,
						check_account_num,
						bank_name,
						bank_state,
						drivers_license_num,
						drivers_license_state
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
		$this->credit_card_type = $row['credit_card_type'];
		$this->cc_number = $row['cc_number'];
		$this->cvv = $row['cvv'];
		$this->cc_exp = $row['cc_exp'];
		$this->check_routing_num = $row['check_routing_num'];
		$this->check_account_num = $row['check_account_num'];
		$this->bank_name = $row['bank_name'];
		$this->bank_state = $row['bank_state'];
		$this->drivers_license_num = $row['drivers_license_num'];
		$this->drivers_license_state = $row['drivers_license_state'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
}

?>