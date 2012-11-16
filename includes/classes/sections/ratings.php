<?PHP

// this class gets single advertiser ratings
class adv_ratings {
	public $loc_id;
	public $alt_loc_id;

	public function get_rating($alt = 0) {
		global $dbh, $adv_alt_loc_tbl, $adv_info_tbl;
		
		$values = array();
		$values[] = $this->loc_id;
		$sql_query = "SELECT
						count(*) as cnt
					 FROM
						advertiser_reviews
					 WHERE
						approved = 1
					 AND
						advertiser_id = ?";
		if($alt == 1){
		  $sql_query .= "
		  				AND
						  advertiser_alt_id = ?";
		  $values[] = $this->alt_loc_id;
		} else {
		  $sql_query .= "
		  				AND
						  (advertiser_alt_id is null OR advertiser_alt_id = 0)";
		}
		$sql_query .= "LIMIT 1;";
		
		// create or check memcached settings
		$reviews = db_memc_str($sql_query,$values);
		
		// create review boxes
		$rev_box_arr = array();
									 
		// get reviews total cnt
		$reviews_cnt = $reviews['cnt'];
		
				$values = array();
		$values[] = $this->loc_id;
		$sql_query = "SELECT
						SUM(rating) as ratsm
					 FROM
						advertiser_reviews
					 WHERE
						approved = 1
					 AND
						advertiser_id = ?";
		if($alt == 1){
		  $sql_query .= "
		  				AND
						  advertiser_alt_id = ? ";
		  $values[] = $this->alt_loc_id;
		} else {
		  $sql_query .= "
		  				AND
						  (advertiser_alt_id is null OR advertiser_alt_id = 0) ";
		}
		$sql_query .= "LIMIT 1;";
			
		// create or check memcached settings
		$reviews = db_memc_str($sql_query,$values);
		
		// create review boxes
		$rev_box_arr = array();
									 
		$reviews_sum = $reviews['ratsm'];
		
		if($reviews_cnt > 0) {
		  $review_result = $reviews_sum / $reviews_cnt;
  
		  $review_result = ceil($review_result);
  		  
		  $review_box = '';
		  
		  for($i = 1; $i <= 5; $i++) {
			  $review_box .= '<input name="starave'.$adv_info_tbl->id.($alt == 1 ? 'alt'.$adv_alt_loc_tbl->id : '').'" type="radio" class="star" value="'.$i.'" disabled="disabled"'.($i == $review_result ? ' checked="checked" ' : '').'/>';						
		  }
		  
		  $final_box = '<div class="average_review_list_bx">'.$review_box.'</div><br /><br />';
		} else {
			$final_box = '<p><strong>No Reviews Found</strong></p>';
		}
		
	return $final_box;
	}
	
}

?>