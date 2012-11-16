<?PHP

// class for interacting with order_items table
class odr_itms_tbl {
	public $id;
	public $order_id;
	public $item_id;
	public $certificate_value_id;
	public $item_type;
	public $item_value;
	public $item_quantity;
	public $item_start_date;
	public $item_expiration_date;
	// table name used throughout queries within page
	private $tbl_nme = "order_items";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->order_id = NULL;
		$this->item_id = NULL;
		$this->certificate_value_id = NULL;
		$this->item_type = NULL;
		$this->item_value = NULL;
		$this->item_quantity = NULL;
		$this->item_start_date = NULL;
		$this->item_expiration_date = NULL;
	}
	
	// insert new order_items amount
	public function insert() {
			global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						order_id,
						item_id,
						certificate_value_id,
						item_type,
						item_value,
						item_quantity,
						item_start_date,
						item_expiration_date
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
							$this->order_id,
							$this->item_id,
							$this->certificate_value_id,
							$this->item_type,
							$this->item_value,
							$this->item_quantity,
							$this->item_start_date,
							$this->item_expiration_date
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing order_items
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						order_id = ?,
						item_id = ?,
						certificate_value_id = ?,
						item_type = ?,
						item_value = ?,
						item_quantity =?,
						item_start_date = ?,
						item_expiration_date = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->order_id,
							$this->item_id,
							$this->certificate_value_id,
							$this->item_type,
							$this->item_value,
							$this->item_quantity,
							$this->item_start_date,
							$this->item_expiration_date,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->order_id = (isset($_POST['order_id']) ? $_POST['order_id'] : '');
		$this->item_id = (isset($_POST['item_id']) ? $_POST['item_id'] : '');
		$this->certificate_value_id = (isset($_POST['certificate_value_id']) ? $_POST['certificate_value_id'] : '');
		$this->item_type = (isset($_POST['item_type']) ? $_POST['item_type'] : '');
		$this->item_value = (isset($_POST['item_value']) ? $_POST['item_value'] : '');
		$this->item_quantity = (isset($_POST['item_quantity']) ? $_POST['item_quantity'] : '');
		$this->item_start_date = (isset($_POST['item_start_date']) ? $_POST['item_start_date'] : '');
		$this->item_expiration_date = (isset($_POST['item_expiration_date']) ? $_POST['item_expiration_date'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						order_id,
						item_id,
						certificate_value_id,
						item_type,
						item_value,
						item_quantity,
						item_start_date,
						item_expiration_date
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
		$this->item_id = $row['item_id'];
		$this->certificate_value_id = $row['certificate_value_id'];
		$this->item_type = $row['item_type'];
		$this->item_value = $row['item_value'];
		$this->item_quantity = $row['item_quantity'];
		$this->item_start_date = $row['item_start_date'];
		$this->item_expiration_date = $row['item_expiration_date'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
}

?>