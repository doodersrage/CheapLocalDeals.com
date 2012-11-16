<?PHP

// class for interacting with url names table
class url_nms_tbl {
	public $id;
	public $type;
	public $parent_id;
	public $url_name;
	// table name used throughout queries within page
	private $tbl_nme = "url_names";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->type = NULL;
		$this->parent_id = NULL;
		$this->url_name = NULL;
	}
	
	// insert new url name
	public function insert() {
			global $dbh;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						type,
						url_name,
						parent_id
					 )
					 VALUES
					 (
						?,
						?,
						?
					 );";
				 
		$update_vals = array(
							$this->type,
							$this->url_name,
							$this->parent_id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing url name
	public function update() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						type = ?,
						url_name = ?,
						parent_id = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->type,
							$this->url_name,
							$this->parent_id,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->type = (isset($_POST['type']) ? $_POST['type'] : '');
		$this->url_name = (isset($_POST['url_name']) ? $_POST['url_name'] : '');
		$this->parent_id = (isset($_POST['parent_id']) ? $_POST['parent_id'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						type,
						url_name,
						parent_id
					 FROM
						".$this->tbl_nme."
					 WHERE
						id = ?
					  LIMIT 1;";

		$update_vals = array(
							$id
							);
				
		$row = db_memc_str($sql_query,$update_vals);
		
		if(!empty($row)){
		  $this->id = $row['id'];
		  $this->type = $row['type'];
		  $this->url_name = $row['url_name'];
		  $this->parent_id = $row['parent_id'];
		} else {
		  $this->reset_vars();
		}		
	}
		
	// get vars from database by parent_id and type
	public function assign_parent_type_db_vars($parent_id,$type) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						type,
						url_name,
						parent_id
					 FROM
						".$this->tbl_nme."
					 WHERE
						parent_id = ?
					 AND type = ?
					  LIMIT 1;";

		$values = array(
						$parent_id,
						$type
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->type = $row['type'];
		$this->url_name = $row['url_name'];
		$this->parent_id = $row['parent_id'];
		
		$result->free();
		
		// reset DB conn
		db_check_conn();
		$row = '';
	}
	
	// get vars from database
	public function url_name_search($url_name) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						type,
						url_name,
						parent_id
					 FROM
						".$this->tbl_nme."
					 WHERE
						url_name = ?
					  LIMIT 1;";

		$values = array(
						$url_name
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->type = $row['type'];
		$this->url_name = $row['url_name'];
		$this->parent_id = $row['parent_id'];
		
		$result->free();
		
		// reset DB conn
		db_check_conn();
		$row = '';
	}

}

?>