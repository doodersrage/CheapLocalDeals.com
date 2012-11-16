<?PHP

// class for interacting with advertiser_reviews table
class adv_rvws_tbl {
	public $id;
	public $customer_id;
	public $advertiser_id;
	public $advertiser_alt_id;
	public $rating;
	public $review;
	public $approved;
	public $added;
	// table name used throughout queries within page
	private $tbl_nme = "advertiser_reviews";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->customer_id = NULL;
		$this->advertiser_id = NULL;
		$this->advertiser_alt_id = NULL;
		$this->rating = NULL;
		$this->review = NULL;
		$this->approved = NULL;
		$this->added = NULL;
	}
	
	// insert new advertiser_reviews
	public function insert() {
		global $dbh;
		
		// apply swear filter
		$this->swear_filter();
		
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						customer_id,
						advertiser_id,
						advertiser_alt_id,
						rating,
						review,
						approved
					 )
					 VALUES
					 (
						?,
						?,
						?,
						?,
						?,
						?
					 );";
				 
		$update_vals = array(
							  $this->customer_id,
							  $this->advertiser_id,
							  $this->advertiser_alt_id,
							  $this->rating,
							  $this->review,
							  $this->approved
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing advertiser_reviews
	public function update() {
		global $dbh;
		
		// apply swear filter
		$this->swear_filter();
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						customer_id = ?,
						advertiser_id = ?,
						advertiser_alt_id = ?,
						rating = ?,
						review = ?,
						approved = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							  $this->customer_id,
							  $this->advertiser_id,
							  $this->advertiser_alt_id,
							  $this->rating,
							  $this->review,
							  $this->approved
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// approve existing advertiser_reviews
	public function approve() {
		global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						approved = ?
					 WHERE
						id = ?
					 AND
						advertiser_id = ?
					 ;";
				 
		$update_vals = array(
							  $this->approved,
							  $this->advertiser_id,
							  $this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->customer_id = (isset($_POST['customer_id']) ? $_POST['customer_id'] : '');
		$this->advertiser_id = (isset($_POST['advertiser_id']) ? $_POST['advertiser_id'] : '');
		$this->advertiser_alt_id = (isset($_POST['advertiser_alt_id']) ? $_POST['advertiser_alt_id'] : '');
		$this->rating = (isset($_POST['rating']) ? serialize($_POST['rating']) : '');
		$this->review = (isset($_POST['review']) ? $_POST['review'] : '');
		$this->approved = (isset($_POST['approved']) ? $_POST['approved'] : '');
			
	}
		
	// get vars from database
	public function get_db_vars($id) {
		global $dbh;
		
		$sql_query = "SELECT
						id,
						customer_id,
						advertiser_id,
						advertiser_alt_id,
						rating,
						review,
						approved,
						added
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
		  $this->customer_id = $row['customer_id'];
		  $this->advertiser_id = $row['advertiser_id'];
		  $this->advertiser_alt_id = $row['advertiser_alt_id'];
		  $this->rating = $row['rating'];
		  $this->review = $row['review'];
		  $this->approved = $row['approved'];
		  $this->added = $row['added'];
		} else {
		  $this->reset_vars();
		}
	}
	
	// internal function used for clearing swearwords from review post
	private function swear_filter() {
		
		// list of swear words to be filtered from review text
		$swear_words = array(
							 'shit',
							 'fuck',
							 'fucking',
							 'cunt',
							 'kunt',
							 'asshole',
							 'ass',
							 'dick',
							 'penis',
							 'asshat',
							 'dickhead',
							 'douce',
							 'anus',
							 'anal'
							 );
		
		// break up review text into a single word array
		$review_text = explode(' ',$this->review);
		
		$new_rev_arr = array();
		// check each word in review array for curse words
		foreach($review_text as $cur_word) {
			// review word for swear word match
			foreach($swear_words as $cur_swear) {
				// we have found a curse word
				if(strtolower($cur_word) == strtolower($cur_swear)) {
					$word_lgth = strlen($cur_word);
					$cur_word = str_repeat('*',$word_lgth);
				}
			}
			
			$new_rev_arr[] = $cur_word;
		}
		
		// rebuild review text
		$this->review = implode(' ',$new_rev_arr);
	}
		
}

?>