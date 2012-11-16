<?PHP

// class for interacting with pages table
class pgs_tbl {
	public $id;
	public $name;
	public $display_name;
	public $header_content;
	public $footer_content;
	public $header_title;
	public $meta_description;
	public $meta_keywords;
	public $dont_cache;
	public $url_name;
	public $url_id;
	// table name used throughout queries within page
	private $tbl_nme = "pages";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->name = NULL;
		$this->display_name = NULL;
		$this->header_content = NULL;
		$this->footer_content = NULL;
		$this->header_title = NULL;
		$this->meta_description = NULL;
		$this->meta_keywords = NULL;
		$this->dont_cache = NULL;
		$this->url_name = NULL;
		$this->url_id = NULL;
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
					 LIMIT 1
					 ;";
		$rows = $dbh->queryRow($sql_query);
		
		$past_id = $rows['id'];
		$past_id++;
		$this->id = $past_id;
		
	}
	
	// insert new page
	public function insert() {
			global $dbh;
		
		$this->new_insert_id();
		
		$this->update_url_name();
						
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						id,
						name,
						display_name,
						header_content,
						footer_content,
						header_title,
						meta_description,
						meta_keywords,
						dont_cache,
						url_name,
						updated
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
							$this->id,
							$this->name,
							$this->display_name,
							$this->header_content,
							$this->footer_content,
							$this->header_title,
							$this->meta_description,
							$this->meta_keywords,
							$this->dont_cache,
							$this->url_name,
							$today
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing page
	public function update() {
			global $dbh;
		
		$this->update_url_name();
		
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						name = ?,
						display_name = ?,
						header_content = ?,
						footer_content = ?,
						header_title = ?,
						meta_description = ?,
						meta_keywords = ?,
						dont_cache = ?,
						url_name = ?,
						updated = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->name,
							$this->display_name,
							$this->header_content,
							$this->footer_content,
							$this->header_title,
							$this->meta_description,
							$this->meta_keywords,
							$this->dont_cache,
							$this->url_name,
							$today,
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
			$url_nms_tbl->type = 'page';
		
			if ($url_nms_tbl->id == '' || $url_nms_tbl->id == 0) {
				$url_nms_tbl->insert();
				$url_nms_tbl->assign_parent_type_db_vars($this->id,'page');
			} else {
				$url_nms_tbl->update();
			}
			$this->url_name = $url_nms_tbl->id;
		}
	}

	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->name = (isset($_POST['name']) ? $_POST['name'] : '');
		$this->display_name = (isset($_POST['display_name']) ? $_POST['display_name'] : '');
		$this->header_content = (isset($_POST['header_content']) ? $_POST['header_content'] : '');
		$this->footer_content = (isset($_POST['footer_content']) ? $_POST['footer_content'] : '');
		$this->header_title = (isset($_POST['header_title']) ? $_POST['header_title'] : '');
		$this->meta_description = (isset($_POST['meta_description']) ? $_POST['meta_description'] : '');
		$this->meta_keywords = (isset($_POST['meta_keywords']) ? $_POST['meta_keywords'] : '');
		$this->dont_cache = (isset($_POST['dont_cache']) ? $_POST['dont_cache'] : '');
		$this->url_name = (isset($_POST['url_name']) ? $_POST['url_name'] : '');
		$this->url_id = (isset($_POST['url_id']) ? $_POST['url_id'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						name,
						display_name,
						header_content,
						footer_content,
						header_title,
						meta_description,
						meta_keywords,
						dont_cache,
						url_name
					 FROM
						".$this->tbl_nme."
					 WHERE
						id = ?
					  LIMIT 1;";

		$values = array(
						$id
						);
		
		$row = db_memc_str($sql_query,$values);
		
		if(!empty($row)){
		  $this->id = $row['id'];
		  $this->name = $row['name'];
		  $this->display_name = $row['display_name'];
		  $this->header_content = $row['header_content'];
		  $this->footer_content = $row['footer_content'];
		  $this->header_title = $row['header_title'];
		  $this->meta_description = $row['meta_description'];
		  $this->meta_keywords = $row['meta_keywords'];
		  $this->dont_cache = $row['dont_cache'];
		  $this->url_name = $row['url_name'];
		} else {
		  $this->reset_vars();
		}		
	}
}

?>