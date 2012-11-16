<?PHP

// class for interacting with zip codes table

class adv_lvls_tbl {
	public $id;
	public $level_name;
	public $level_weight;
	public $level_description;
	public $level_duration;
	public $level_upfront_cost;
	public $level_renewal_cost;
	public $upfront_level_link_back;
	public $upfront_bbb_member_price;
	// table name used throughout queries within page
	private $tbl_nme = "advertiser_levels";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->level_name = NULL;
		$this->level_weight = NULL;
		$this->level_description = NULL;
		$this->level_duration = NULL;
		$this->level_upfront_cost = NULL;
		$this->level_renewal_cost = NULL;
		$this->upfront_level_link_back = NULL;
		$this->upfront_bbb_member_price = NULL;
	}
	
	// insert new advertiser level
	public function insert() {
			global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						level_name,
						level_weight,
						level_description,
						level_duration,
						level_upfront_cost,
						level_renewal_cost,
						upfront_level_link_back,
						upfront_bbb_member_price
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
							$this->level_name,
							$this->level_weight,
							$this->level_description,
							$this->level_duration,
							$this->level_upfront_cost,
							$this->level_renewal_cost,
							$this->upfront_level_link_back,
							$this->upfront_bbb_member_price,
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing advertiser level
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						level_name = ?,
						level_weight = ?,
						level_description = ?,
						level_duration = ?,
						level_upfront_cost = ?,
						level_renewal_cost = ?,
						upfront_level_link_back = ?,
						upfront_bbb_member_price = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->level_name,
							$this->level_weight,
							$this->level_description,
							$this->level_duration,
							$this->level_upfront_cost,
							$this->level_renewal_cost,
							$this->upfront_level_link_back,
							$this->upfront_bbb_member_price,
							$this->id
							);

		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->level_name = (isset($_POST['level_name']) ? $_POST['level_name'] : '');
		$this->level_weight = (isset($_POST['level_weight']) ? $_POST['level_weight'] : '');
		$this->level_description = (isset($_POST['level_description']) ? $_POST['level_description'] : '');
		$this->level_duration = (isset($_POST['level_duration']) ? $_POST['level_duration'] : '');
		$this->level_upfront_cost = (isset($_POST['level_upfront_cost']) ? $_POST['level_upfront_cost'] : '');
		$this->level_renewal_cost = (isset($_POST['level_renewal_cost']) ? $_POST['level_renewal_cost'] : '');
		$this->upfront_level_link_back = (isset($_POST['upfront_level_link_back']) ? $_POST['upfront_level_link_back'] : '');
		$this->upfront_bbb_member_price = (isset($_POST['upfront_bbb_member_price']) ? $_POST['upfront_bbb_member_price'] : '');
	
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						level_name,
						level_weight,
						level_description,
						level_duration,
						level_upfront_cost,
						level_renewal_cost,
						upfront_level_link_back,
						upfront_bbb_member_price
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
		$this->level_name = $row['level_name'];
		$this->level_weight = $row['level_weight'];
		$this->level_description = $row['level_description'];
		$this->level_duration = $row['level_duration'];
		$this->level_upfront_cost = $row['level_upfront_cost'];
		$this->level_renewal_cost = $row['level_renewal_cost'];
		$this->upfront_level_link_back = $row['upfront_level_link_back'];
		$this->upfront_bbb_member_price = $row['upfront_bbb_member_price'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
		
	// get vars from database
	public function return_all_levels() {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						level_name,
						level_weight,
						level_description,
						level_duration,
						level_upfront_cost,
						level_renewal_cost,
						upfront_level_link_back,
						upfront_bbb_member_price
					 FROM
						".$this->tbl_nme."
					 ORDER BY level_weight ASC;";
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($stmt);
	
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