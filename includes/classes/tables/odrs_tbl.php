<?PHP

// class for interacting with orders table
class odrs_tbl {
	public $id;
	public $customer_id;
	public $applicable_taxes;
	public $order_total;
	public $payment_method;
	public $credit_card_type;
	public $credit_card_number;
	public $cvv;
	public $expiration_date;
	public $date_added;
	public $payment_approved;
	public $order_notes;
	public $promo_code;
	public $api_id;
	// table name used throughout queries within page
	private $tbl_nme = "orders";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->customer_id = NULL;
		$this->applicable_taxes = NULL;
		$this->order_total = NULL;
		$this->payment_method = NULL;
		$this->credit_card_type = NULL;
		$this->credit_card_number = NULL;
		$this->cvv = NULL;
		$this->expiration_date = NULL;
		$this->date_added = NULL;
		$this->payment_approved = NULL;
		$this->order_notes = NULL;
		$this->promo_code = NULL;
		$this->api_id = NULL;
	}
	
	// insert new orders
	public function insert() {
			global $dbh;
						
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						customer_id,
						applicable_taxes,
						order_total,
						payment_method,
						credit_card_type,
						credit_card_number,
						cvv,
						expiration_date,
						payment_approved,
						order_notes,
						promo_code,
						date_added,
						api_id
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
						?
					 );";
				 
		$update_vals = array(
							$this->customer_id,
							$this->applicable_taxes,
							$this->order_total,
							$this->payment_method,
							$this->credit_card_type,
							$this->credit_card_number,
							$this->cvv,
							$this->expiration_date,
							$this->payment_approved,
							$this->order_notes,
							$this->promo_code,
							$today,
							$this->api_id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing orders
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						customer_id = ?,
						applicable_taxes = ?,
						order_total = ?,
						payment_method = ?,
						credit_card_type = ?,
						credit_card_number = ?,
						cvv = ?,
						expiration_date = ?,
						order_notes = ?,
						promo_code = ?,
						api_id = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->customer_id,
							$this->applicable_taxes,
							$this->order_total,
							$this->payment_method,
							$this->credit_card_type,
							$this->credit_card_number,
							$this->cvv,
							$this->expiration_date,
							$this->order_notes,
							$this->promo_code,
							$this->api_id,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->customer_id = (isset($_POST['customer_id']) ? $_POST['customer_id'] : '');
		$this->applicable_taxes = (isset($_POST['applicable_taxes']) ? $_POST['applicable_taxes'] : '');
		$this->order_total = (isset($_POST['order_total']) ? $_POST['order_total'] : '');
		$this->payment_method = (isset($_POST['payment_method']) ? $_POST['payment_method'] : '');
		$this->credit_card_type = (isset($_POST['credit_card_type']) ? $_POST['credit_card_type'] : '');
		$this->credit_card_number = (isset($_POST['credit_card_number']) ? $_POST['credit_card_number'] : '');
		$this->cvv = (isset($_POST['cvv']) ? $_POST['cvv'] : '');
		$this->expiration_date = (isset($_POST['expiration_date']) ? $_POST['expiration_date'] : '');
		$this->payment_approved = (isset($_POST['payment_approved']) ? $_POST['payment_approved'] : '');
		$this->order_notes = (isset($_POST['order_notes']) ? $_POST['order_notes'] : '');
		$this->promo_code = (isset($_POST['promo_code']) ? $_POST['promo_code'] : '');
		$this->api_id = (isset($_POST['api_id']) ? $_POST['api_id'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						customer_id,
						applicable_taxes,
						order_total,
						payment_method,
						credit_card_type,
						credit_card_number,
						cvv,
						expiration_date,
						date_added,
						payment_approved,
						order_notes,
						promo_code,
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
		$this->customer_id = $row['customer_id'];
		$this->applicable_taxes = $row['applicable_taxes'];
		$this->order_total = $row['order_total'];
		$this->payment_method = $row['payment_method'];
		$this->credit_card_type = $row['credit_card_type'];
		$this->credit_card_number = $row['credit_card_number'];
		$this->cvv = $row['cvv'];
		$this->expiration_date = $row['expiration_date'];
		$this->date_added = $row['date_added'];
		$this->payment_approved = $row['payment_approved'];
		$this->order_notes = $row['order_notes'];
		$this->promo_code = $row['promo_code'];
		$this->api_id = $row['api_id'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function list_orders_customer($customer_id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						customer_id,
						applicable_taxes,
						order_total,
						payment_method,
						credit_card_type,
						credit_card_number,
						cvv,
						expiration_date,
						date_added,
						payment_approved,
						order_notes,
						promo_code,
						api_id
					 FROM
						".$this->tbl_nme."
					 WHERE
						customer_id = ?
					 ;";
	
		$values = array(
						$customer_id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);

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