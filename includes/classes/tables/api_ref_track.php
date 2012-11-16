<?PHP

// class for interacting with api_ref_track table
class api_ref_track_tbl {
	public $id;
	public $api_id;
	public $sess_id;
	public $customer_id;
	public $ref_page;
	public $page;
	public $cart;
	public $cart_total;
	public $longit;
	public $latt;
	public $date_time;
	// table name used throughout queries within page
	private $tbl_nme = "api_ref_track";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->api_id = NULL;
		$this->sess_id = NULL;
		$this->customer_id = NULL;
		$this->ref_page = NULL;
		$this->page = NULL;
		$this->cart = NULL;
		$this->cart_total = NULL;
		$this->longit = NULL;
		$this->latt = NULL;
		$this->date_time = NULL;
	}
	
	// insert new state
	public function insert() {
			global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						api_id,
						sess_id,
						customer_id,
						ref_page,
						page,
						cart,
						cart_total,
						longit,
						latt
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
						?
					 );";

		$update_vals = array(
							$this->api_id,
							$this->sess_id,
							$this->customer_id,
							$this->ref_page,
							$this->page,
							$this->cart,
							$this->cart_total,
							$this->longit,
							$this->latt
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing state
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						api_id = ?,
						sess_id = ?,
						customer_id = ?,
						ref_page = ?,
						page = ?,
						cart = ?,
						cart_total = ?,
						longit = ?,
						latt = ?,
						date_time = ?,
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->api_id,
							$this->sess_id,
							$this->customer_id,
							$this->ref_page,
							$this->page,
							$this->cart,
							$this->cart_total,
							$this->longit,
							$this->latt,
							$this->date_time,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->api_id = (isset($_POST['api_id']) ? $_POST['api_id'] : '');
		$this->sess_id = (isset($_POST['sess_id']) ? $_POST['sess_id'] : '');
		$this->customer_id = (isset($_POST['customer_id']) ? $_POST['customer_id'] : '');
		$this->ref_page = (isset($_POST['ref_page']) ? $_POST['ref_page'] : '');
		$this->page = (isset($_POST['page']) ? $_POST['page'] : '');
		$this->cart = (isset($_POST['cart']) ? $_POST['cart'] : '');
		$this->cart_total = (isset($_POST['cart_total']) ? $_POST['cart_total'] : '');
		$this->longit = (isset($_POST['longit']) ? $_POST['longit'] : '');
		$this->latt = (isset($_POST['latt']) ? $_POST['latt'] : '');
					
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						api_id,
						sess_id,
						customer_id,
						ref_page,
						page,
						cart,
						cart_total,
						longit,
						latt,
						date_time
					 FROM
						".$this->tbl_nme."
					 WHERE
						id = ?
					  LIMIT 1;";
		
		$update_vals = array(
							$id
							);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($update_vals);
		$row = $result->fetchRow();
				
		$this->id = $row['id'];
		$this->api_id = $row['api_id'];
		$this->sess_id = $row['sess_id'];
		$this->customer_id = $row['customer_id'];
		$this->ref_page = $row['ref_page'];
		$this->page = $row['page'];
		$this->cart = $row['cart'];
		$this->cart_total = $row['cart_total'];
		$this->longit = $row['longit'];
		$this->latt = $row['latt'];
		$this->date_time = $row['date_time'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function get_db_vars_by_sess($sess_id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						api_id,
						sess_id,
						customer_id,
						ref_page,
						page,
						cart,
						cart_total,
						longit,
						latt,
						date_time
					 FROM
						".$this->tbl_nme."
					 WHERE
						sess_id = ?
					 ORDER BY date_time DESC
					 LIMIT 1;";
		
		$update_vals = array(
							$sess_id
							);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($update_vals);
		$row = $result->fetchRow();
				
		$this->id = $row['id'];
		$this->api_id = $row['api_id'];
		$this->sess_id = $row['sess_id'];
		$this->customer_id = $row['customer_id'];
		$this->ref_page = $row['ref_page'];
		$this->page = $row['page'];
		$this->cart = $row['cart'];
		$this->cart_total = $row['cart_total'];
		$this->longit = $row['longit'];
		$this->latt = $row['latt'];
		$this->date_time = $row['date_time'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function get_cnt_by_sess($sess_id) {
		global $dbh;
		
		$sql_query = "SELECT
						count(*) as cnt
					 FROM
						".$this->tbl_nme."
					 WHERE
						sess_id = ?
					 ;";
		
		$update_vals = array(
							$sess_id
							);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($update_vals);
		$row = $result->fetchRow();
				
		$count = $row['cnt'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
		
	return $count;
	}
}

?>
