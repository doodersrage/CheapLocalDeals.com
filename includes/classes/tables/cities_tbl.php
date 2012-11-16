<?PHP

// class for interacting with cities table
class cities_tbl {
	public $id;
	public $state;
	public $city;
	public $type;
	public $page_header;
	public $page_footer;
	public $url_name;
	public $url_id;
	public $page_title;
	public $meta_description;
	public $meta_keywords;
	public $updated;
	// table name used throughout queries within page
	private $tbl_nme = "cities";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->state = NULL;
		$this->city = NULL;
		$this->type = NULL;
		$this->page_header = NULL;
		$this->page_footer = NULL;
		$this->url_name = NULL;
		$this->url_id = NULL;
		$this->page_title = NULL;
		$this->meta_description = NULL;
		$this->meta_keywords = NULL;
		$this->updated = NULL;
	}
	
	// insert new state
	public function insert() {
			global $dbh, $cats_tbl, $ste_cty_cat_tbl, $stes_tbl;
						
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						state,
						city,
						type,
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
					 );";
				 
		$update_vals = array(
							$this->state,
							$this->city,
							$this->type,
							$this->page_header,
							$this->page_footer,
							$this->url_name,
							$this->page_title,
							$this->meta_description,
							$this->meta_keywords,
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);

		// get newly inserted city id
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
		
		// pull listing of all categories
		$all_cats = $cats_tbl->get_all_cats();
		
		foreach($all_cats as $cur_cat){
		  // look up state id
		  $stes_tbl->lookup_by_acn($this->state);
		  $url_string = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $this->city.'-'.$this->state.'-cheap-local-deals'));
		  $ste_cty_cat_tbl->reset_vars();
		  $ste_cty_cat_tbl->state = $stes_tbl->id;
		  $ste_cty_cat_tbl->city = $this->id;
		  $ste_cty_cat_tbl->category = $cur_cat['id'];
		  $ste_cty_cat_tbl->url_name = $url_string;
		  $ste_cty_cat_tbl->insert();
		}
		
	}
	
	// update existing state
	public function update() {
			global $dbh;
		
		$this->update_url_name();
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						state = ?,
						city = ?,
						type = ?,
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
							$this->type,
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
			$url_nms_tbl->type = 'city';
		
			if ($url_nms_tbl->id == '' || $url_nms_tbl->id == 0) {
				$url_nms_tbl->insert();
				$url_nms_tbl->assign_parent_type_db_vars($this->id,'city');
			} else {
				$url_nms_tbl->update();
			}
			$this->url_name = $url_nms_tbl->id;
		}
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->state = (isset($_POST['state']) ? $_POST['state'] : '');
		$this->city = (isset($_POST['city']) ? $_POST['city'] : '');
		$this->type = (isset($_POST['type']) ? $_POST['type'] : '');
		$this->page_header = (isset($_POST['page_header']) ? $_POST['page_header'] : '');
		$this->page_footer = (isset($_POST['page_footer']) ? $_POST['page_footer'] : '');
		$this->url_name = (isset($_POST['url_name']) ? $_POST['url_name'] : '');
		$this->url_id = (isset($_POST['url_id']) ? $_POST['url_id'] : '');
		$this->page_title = (isset($_POST['page_title']) ? $_POST['page_title'] : '');
		$this->meta_description = (isset($_POST['meta_description']) ? $_POST['meta_description'] : '');
		$this->meta_keywords = (isset($_POST['meta_keywords']) ? $_POST['meta_keywords'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						state,
						city,
						type,
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
		  $this->type = $row['type'];
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
		
	// get vars from database
	public function city_state_search($city,$state) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						state,
						city,
						type,
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
					 	state = ?
					  LIMIT 1;";

		$values = array(
						$city,
						$state
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->state = $row['state'];
		$this->city = $row['city'];
		$this->type = $row['type'];
		$this->page_header = $row['page_header'];
		$this->page_footer = $row['page_footer'];
		$this->url_name = $row['url_name'];
		$this->page_title = $row['page_title'];
		$this->meta_description = $row['meta_description'];
		$this->meta_keywords = $row['meta_keywords'];
		$this->updated = $row['updated'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
}

?>
