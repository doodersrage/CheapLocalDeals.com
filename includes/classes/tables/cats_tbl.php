<?PHP

// class for interacting with zip codes table

class cats_tbl {
	public $id;
	public $category_name;
	public $sort_order;
	public $header_val;
	public $footer;
	public $header_title;
	public $meta_description;
	public $meta_keywords;
	public $zip_id;
	public $url_name;
	public $parent_category_id;
	public $image;
	public $url_id;
	public $map_marker;
	public $views;
	public $disabled;
	// table name used throughout queries within page
	private $tbl_nme = "categories";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->category_name = NULL;
		$this->sort_order = NULL;
		$this->header_val = NULL;
		$this->footer = NULL;
		$this->header_title = NULL;
		$this->meta_description = NULL;
		$this->meta_keywords = NULL;
		$this->zip_id = NULL;
		$this->url_name = NULL;
		$this->parent_category_id = NULL;
		$this->image = NULL;
		$this->url_id = NULL;
		$this->map_marker = NULL;
		$this->views = NULL;
		$this->disabled = NULL;
	}
	
	// insert new category
	public function insert() {
			global $dbh, $dsn;
		
		$this->new_insert_id();
		
		$this->update_url_name();
				
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						category_name,
						sort_order,
						header,
						footer,
						header_title,
						meta_description,
						meta_keywords,
						zip_id,
						url_name,
						parent_category_id,
						image,
						map_marker,
						last_modified,
						disabled
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
						?,
						?
					);
					 ";
				 
		$update_vals = array(
							$this->category_name,
							$this->sort_order,
							$this->header_val,
							$this->footer,
							$this->header_title,
							$this->meta_description,
							$this->meta_keywords,
							$this->zip_id,
							$this->url_name,
							$this->parent_category_id,
							$this->image,
							$this->map_marker,
							$today,
							$this->disabled
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
		// pull newly inserted category ID
		$sql_query = "SELECT
						id
					 FROM
						".$this->tbl_nme."
					 ORDER BY
						id DESC
					 LIMIT 1;";
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute();
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);

		$new_cat_id = $row['id'];
		
		// get total zip codes count
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						cities
					  LIMIT 1;";
		$rowscount = $dbh->queryRow($sql_query);
		
		// set count to var
		$found_cities = $rowscount['rcount'];

		// maximum number or lines to read per run
		$run_limiter = 1000;
		
		// cycle through the next 3000 entries
		for($cur_row = 0; $cur_row <= $found_cities; $cur_row += $run_limiter) {
	
			// this section generates a new category for all cities within the site
			$sql_query = "SELECT
							id,
							state
						 FROM
							cities
						 LIMIT
							?, ?
						 ;";
					 
			$values = array(
							$cur_row,
							$run_limiter
							);

			$stmt1 = $dbh->prepare($sql_query);					 
			$result1 = $stmt1->execute($values);

			while($cur_city = $result1->fetchRow(MDB2_FETCHMODE_ASSOC)) {
						
				 //Set it to no-limit
				set_time_limit(0);
			
				 //pull state id
				$sql_query = "SELECT
								id
							 FROM
								states
							 WHERE
								acn = '".$cur_city['state']."'
							  LIMIT 1;";
				
				$rowsstate = $dbh->queryRow($sql_query);
				
				$sql_query = "INSERT INTO
								state_city_category
							 (
								state,
								city,
								category
							 )
							 VALUES
							 (
							 	?,
								?,
								?
							);
							 ";
						 
				$update_vals = array($rowsstate['id'],
									$cur_city['id'],
									$new_cat_id
									);
													
				$stmt2 = $dbh->prepare($sql_query);					 
				$stmt2->execute($update_vals);
				
			}
			$result->free();
		}
		
	}
	
	// update existing category
	public function update() {
			global $dbh;
		
		$this->update_url_name();

		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						category_name = ?,
						sort_order = ?,
						header = ?,
						footer = ?,
						header_title = ?,
						meta_description = ?,
						meta_keywords = ?,
						zip_id = ?,
						url_name = ?,
						parent_category_id = ?,
						image = ?,
						map_marker = ?,
						last_modified = ?,
						disabled = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->category_name,
							$this->sort_order,
							$this->header_val,
							$this->footer,
							$this->header_title,
							$this->meta_description,
							$this->meta_keywords,
							$this->zip_id,
							$this->url_name,
							$this->parent_category_id,
							$this->image,
							$this->map_marker,
							$today,
							$this->disabled,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update category views
	public function update_category_views() {
			global $dbh;

		$this->views++;

		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						views = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array($this->views,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update seo friendly url name
	public function update_url_name() {
		global $url_nms_tbl, $dbh;
		
		if ($this->url_name != '' || $this->url_name != 0) {
			$url_nms_tbl->id = $this->url_id;
			$url_nms_tbl->url_name = $this->url_name;
			$url_nms_tbl->parent_id = $this->id;
			$url_nms_tbl->type = 'category';
		
			if ($url_nms_tbl->id == '' || $url_nms_tbl->id == 0 || $url_nms_tbl->id == NULL) {
				$url_nms_tbl->insert();
				$url_nms_tbl->assign_parent_type_db_vars($this->id,'category');
			} else {
				$url_nms_tbl->update();
			}
			$this->url_name = $url_nms_tbl->id;
		}
	}
	
	// set insert id
	public function new_insert_id() {
			global $dbh;
			
		$sql_query = "SELECT
						id
					 FROM
						".$this->tbl_nme."
					 ORDER BY
						id DESC
					 LIMIT 1;";
		$rows = $dbh->queryRow($sql_query);
		
		$past_id = $rows['id'];
		$past_id++;
		$this->id = $past_id;
		
	}
	
	// read form post vars to class vars
	public function get_post_vars() {
		
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->category_name = (isset($_POST['category_name']) ? $_POST['category_name'] : '');
		$this->sort_order = (isset($_POST['sort_order']) ? $_POST['sort_order'] : '');
		$this->header_val = (isset($_POST['header']) ? $_POST['header'] : '');
		$this->footer = (isset($_POST['footer']) ? $_POST['footer'] : '');
		$this->header_title = (isset($_POST['header_title']) ? $_POST['header_title'] : '');
		$this->meta_description = (isset($_POST['meta_description']) ? $_POST['meta_description'] : '');
		$this->meta_keywords = (isset($_POST['meta_keywords']) ? $_POST['meta_keywords'] : '');
		$this->zip_id = (isset($_POST['zip_id']) ? $_POST['zip_id'] : '');
		$this->url_name = strtolower(preg_replace("/[^a-zA-Z0-9s]/", "-", (isset($_POST['url_name']) ? $_POST['url_name'] : '')));
		$this->parent_category_id = (isset($_POST['parent_category_id']) ? $_POST['parent_category_id'] : '');
		$this->url_id = (isset($_POST['url_id']) ? $_POST['url_id'] : '');		
		$this->disabled = (isset($_POST['disabled']) ? $_POST['disabled'] : '');		

		// upload new image
		$target_path = CATEGORY_IMAGES_DIRECTORY . md5($_POST['category_name']) . "-" . basename($_FILES['image']['name']); 
		if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
			$this->image = md5($_POST['category_name']) . "-" . basename($_FILES['image']['name']);
		} else {
			$this->image = $_POST['old_image'];
		}

		// upload new image
		$target_path = CATEGORY_IMAGES_DIRECTORY . md5($_POST['category_name']) . "-" . basename( $_FILES['map_marker']['name']); 
		if (move_uploaded_file($_FILES['map_marker']['tmp_name'], $target_path)) {
			$this->map_marker = md5($_POST['category_name']) . "-" . basename( $_FILES['map_marker']['name']);
		} else {
			$this->map_marker = $_POST['old_map_marker'];
		}

		
	}
	
	// read database values and assign to class vars
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						category_name,
						sort_order,
						header,
						footer,
						header_title,
						meta_description,
						meta_keywords,
						zip_id,
						url_name,
						parent_category_id,
						image,
						map_marker,
						views,
						disabled
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
		  $this->category_name = $row['category_name'];
		  $this->sort_order = $row['sort_order'];
		  $this->header_val = $row['header'];
		  $this->footer = $row['footer'];
		  $this->header_title = $row['header_title'];
		  $this->meta_description = $row['meta_description'];
		  $this->meta_keywords = $row['meta_keywords'];
		  $this->zip_id = $row['zip_id'];
		  $this->url_name = $row['url_name'];
		  $this->parent_category_id = $row['parent_category_id'];
		  $this->image = $row['image'];
		  $this->map_marker = $row['map_marker'];
		  $this->views = $row['views'];
		  $this->disabled = $row['disabled'];
		} else {
		  $this->reset_vars();
		}		
	}
	
	// get child category count
	public function child_cat_count($id) {
			global $dbh;
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
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
						".$this->tbl_nme."
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
		
		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	
	return $results;
	}
	
	// get listing of all available categories
	public function get_all_cats() {
			global $dbh;
		
		$sql_query = "SELECT
						*
					 FROM
						".$this->tbl_nme."
					 ORDER BY id
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