<?PHP

// class for interacting with cirtificate amount table
class cert_amt_tbl {
	public $id;
	public $discount_amount;
	public $cost;
	public $min_spend_amts;
	public $crtamt_sort;
	// table name used throughout queries within page
	private $tbl_nme = "certificate_amount";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->discount_amount = NULL;
		$this->cost = NULL;
		$this->min_spend_amts = NULL;
		$this->crtamt_sort = NULL;
	}
	
	// insert new certificate amount
	public function insert() {
			global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						discount_amount,
						cost,
						min_spend_amts,
						crtamt_sort
					 )
					 VALUES
					 (
						?,
						?,
						?,
						?
					 );";
				 
		$update_vals = array(
							$this->discount_amount,
							$this->cost,
							$this->min_spend_amts,
							$this->crtamt_sort
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing cirtificate amount
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						discount_amount = ?,
						cost = ?,
						min_spend_amts = ?,
						crtamt_sort = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->discount_amount,
							$this->cost,
							$this->min_spend_amts,
							$this->crtamt_sort,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->discount_amount = (isset($_POST['discount_amount']) ? $_POST['discount_amount'] : '');
		$this->cost = (isset($_POST['cost']) ? $_POST['cost'] : '');
		$this->min_spend_amts = (isset($_POST['min_spend_amts']) ? $_POST['min_spend_amts'] : '');
		$this->crtamt_sort = (isset($_POST['crtamt_sort']) ? $_POST['crtamt_sort'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						discount_amount,
						cost,
						min_spend_amts,
						crtamt_sort
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
		$this->discount_amount = $row['discount_amount'];
		$this->cost = $row['cost'];
		$this->min_spend_amts = $row['min_spend_amts'];
		$this->crtamt_sort = $row['crtamt_sort'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function get_certificate_amounts() {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						discount_amount,
						cost,
						min_spend_amts
					 FROM
						".$this->tbl_nme."
					 ORDER BY 
					 	crtamt_sort ASC
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