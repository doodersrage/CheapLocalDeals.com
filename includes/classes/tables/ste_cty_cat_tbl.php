<?PHP

// loads state_city_category 
class ste_cty_cat_tbl {
	public $id;
	public $state;
	public $city;
	public $category;
	public $page_header;
	public $page_footer;
	public $url_name;
	public $url_id;
	public $page_title;
	public $meta_description;
	public $meta_keywords;
	public $updated;
	// table name used throughout queries within page
	private $tbl_nme = "state_city_category";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->state = NULL;
		$this->city = NULL;
		$this->category = NULL;
		$this->page_header = NULL;
		$this->page_footer = NULL;
		$this->url_name = NULL;
		$this->url_id = NULL;
		$this->page_title = NULL;
		$this->meta_description = NULL;
		$this->meta_keywords = NULL;
		$this->updated = NULL;
	}
	
	// insert new category
	public function insert() {
		global $dbh;
				
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						state,
						city,
						category,
						page_header,
						page_footer,
						url_name,
						page_title,
						meta_description,
						meta_keywords
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
					);
					 ";
				 
		$update_vals = array(
								$this->state,
								$this->city,
								$this->category,
								$this->page_header,
								$this->page_footer,
								$this->url_name,
								$this->page_title,
								$this->meta_description,
								$this->meta_keywords
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
		// get newly inserted city cat id
		$sql_query = "SELECT
						id
					 FROM
						".$this->tbl_nme."
					 ORDER BY id DESC
					 LIMIT 1
					 ;";
			
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute();
	
		$new_city_id = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		// insert assigned url name for newly generated url
		$this->id = $new_city_id['id'];
		$this->update_url_name();
		$this->update();
		
	}
	
	// update existing category
	public function update() {
			global $dbh;
		
		$this->update_url_name();

		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						state = ?,
						city = ?,
						category = ?,
						page_header = ?,
						page_footer = ?,
						url_name = ?,
						page_title = ?,
						meta_description = ?,
						meta_keywords = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
								$this->state,
								$this->city,
								$this->category,
								$this->page_header,
								$this->page_footer,
								$this->url_name,
								$this->page_title,
								$this->meta_description,
								$this->meta_keywords,
								$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update seo friendly url name
	public function update_url_name() {
		global $url_nms_tbl;
		
		if ($this->url_name != '' || $this->url_name != 0) {
			$url_nms_tbl->id = $this->url_id;
			$url_nms_tbl->url_name = $this->url_name;
			$url_nms_tbl->parent_id = $this->id;
			$url_nms_tbl->type = 'citiescate';
		
			if ($url_nms_tbl->id == '' || $url_nms_tbl->id == 0) {
				$url_nms_tbl->insert();
				$url_nms_tbl->assign_parent_type_db_vars($this->id,'citiescate');
			} else {
				$url_nms_tbl->update();
			}
			$this->url_name = $url_nms_tbl->id;
		}
	}
	
	// read form post vars to class vars
	public function get_post_vars() {
		
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->state = (isset($_POST['state']) ? $_POST['state'] : '');
		$this->city = (isset($_POST['city']) ? $_POST['city'] : '');
		$this->category = (isset($_POST['category']) ? $_POST['category'] : '');
		$this->page_header = (isset($_POST['page_header']) ? $_POST['page_header'] : '');
		$this->page_footer = (isset($_POST['page_footer']) ? $_POST['page_footer'] : '');
		$this->url_name = (isset($_POST['url_name']) ? $_POST['url_name'] : '');
		$this->url_id = (isset($_POST['url_id']) ? $_POST['url_id'] : '');
		$this->page_title = (isset($_POST['page_title']) ? $_POST['page_title'] : '');
		$this->meta_description = (isset($_POST['meta_description']) ? $_POST['meta_description'] : '');
		$this->meta_keywords = (isset($_POST['meta_keywords']) ? $_POST['meta_keywords'] : '');
		
	}
	
	// read database values and assign to class vars
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						state,
						city,
						category,
						page_header,
						page_footer,
						url_name,
						page_title,
						meta_description,
						meta_keywords,
						updated
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
		  $this->state = $row['state'];
		  $this->city = $row['city'];
		  $this->category = $row['category'];
		  $this->page_header = $row['page_header'];
		  $this->page_footer = $row['page_footer'];
		  $this->url_name = $row['url_name'];
		  $this->page_title = $row['page_title'];
		  $this->meta_description = $row['meta_description'];
		  $this->meta_keywords = $row['meta_keywords'];
		  $this->updated = $row['updated'];
		} else {
		  $this->reset_vars();
		}		
	}
	
	// read database values and assign to class vars
	public function city_search($id,$category) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						state,
						city,
						category,
						page_header,
						page_footer,
						url_name,
						page_title,
						meta_description,
						meta_keywords,
						updated
					 FROM
						".$this->tbl_nme."
					 WHERE
						city = ?
					 AND
					 	category = ?
					  LIMIT 1;";

		$values = array(
						$id,
						$category
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->state = $row['state'];
		$this->city = $row['city'];
		$this->category = $row['category'];
		$this->page_header = $row['page_header'];
		$this->page_footer = $row['page_footer'];
		$this->url_name = $row['url_name'];
		$this->page_title = $row['page_title'];
		$this->meta_description = $row['meta_description'];
		$this->meta_keywords = $row['meta_keywords'];
		$this->updated = $row['updated'];
		
		$row = '';

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
	
	// read database values and assign to class vars
	public function city_category_search($id,$category) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						state,
						city,
						category,
						page_header,
						page_footer,
						url_name,
						page_title,
						meta_description,
						meta_keywords,
						updated
					 FROM
						".$this->tbl_nme."
					 WHERE
						city = ?
					 AND
					 	category = ?
					  LIMIT 1;";

		$values = array(
						$id,
						$category
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->state = $row['state'];
		$this->city = $row['city'];
		$this->category = $row['category'];
		$this->page_header = $row['page_header'];
		$this->page_footer = $row['page_footer'];
		$this->url_name = $row['url_name'];
		$this->page_title = $row['page_title'];
		$this->meta_description = $row['meta_description'];
		$this->meta_keywords = $row['meta_keywords'];
		$this->updated = $row['updated'];
		
		$row = '';

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
	
	// get child category count
	public function child_cat_count($id) {
			global $dbh;
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						categories
					 WHERE
						parent_category_id = ?
					  LIMIT 1;";

		$values = array(
						$id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$count = $row['rcount'];
		
		$row = '';
	

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	return $count;
	}
	
	// create child category id array
	public function get_child_cats($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id
					 FROM
						categories
					 WHERE
						parent_category_id = ?
					 ORDER BY sort_order ASC, category_name ASC
					 ;";

		$values = array(
						$id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
	
		$results = array();
		while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$results[] = $row;
		}
		
		$result->free();
		$row = '';

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	
	return $results;
	}
	
}

?>