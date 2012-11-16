<?PHP

// This class is used when interacting with advertiser reviews
class advert_reviews_page {
	private $customer_id, $advertiser_id, $advertiser_alt_id;
	public $address;
	
	// draws the review submission form
	public function draw_form() {
		global $adv_rvws_tbl, $dbh, $adv_info_tbl, $location_info_pg, $adv_alt_loc_tbl;
		
		$reviews_form = '<!-- New Reviews Page -->
                    <div align="center">
					<div class="reviews_title">' . $adv_info_tbl->company_name .' Reviews</div>';
					
		if ($adv_info_tbl->hide_address != 1 && ($adv_alt_loc_tbl->latitude > 0 || $adv_info_tbl->latitude > 0)) {
		  
		  $reviews_form .= '<div id="map" style="width: 400px; height: 300px"></div>
							<script src="http://maps.google.com/maps?file=api&v=2&key='.GOOGLE_MAPS_API_KEY.'" type="text/javascript"></script>
							<script type="text/javascript">
							  function initialize() {
								if (GBrowserIsCompatible()) {
								  var map = new GMap2(document.getElementById("map"));
								  map.setCenter(new GLatLng('.$adv_info_tbl->latitude.', '.$adv_info_tbl->longitude.'), 13);
								  var point = new GLatLng('.$adv_info_tbl->latitude.', '.$adv_info_tbl->longitude.');
         						  map.addOverlay(new GMarker(point));
								  map.setUIToDefault();
								}
							  }
							  initialize();
						   </script>';
						   
		}
		
		$reviews_form .= $this->address;
					
		$reviews_form .= '<table border="0" align="center" cellpadding="4" cellspacing="0" align="center">
                      <tr>
                        <td>
					  <script src="'.CONNECTION_TYPE.'js_load.deal?js_doc[]='.urlencode('includes/libs/star-rating/jquery.MetaData.js').'&amp;js_doc[]='.urlencode('includes/libs/star-rating/jquery.rating.js').'" type="text/javascript" language="javascript"></script>
					  <link href="'.CONNECTION_TYPE.'includes/libs/star-rating/jquery.rating.css" type="text/css" rel="stylesheet"/>
					  
					  <table align="center" class="vote_form">
						<tr>
						  <td>
							<table align="center" class="vote_form_area">';
		
		if (isset($_SESSION['customer_logged_in'])) {
			
			// first check to see if the customer did not already review the advertiser
				
			$values = array();
			$values[] = (int)$_GET['loc_id'];
			$values[] = (int)$_SESSION['customer_id'];
			$sql_query = "SELECT
							id
						 FROM
							advertiser_reviews
						 WHERE
							advertiser_id = ?
						 AND
						 	customer_id = ?";
			if(isset($_GET['alt_loc_id'])){
			  $sql_query .= "
							AND
							  advertiser_alt_id = ?";
			  $values[] = (int)$_GET['alt_loc_id'];
			} else {
			  $sql_query .= "
							AND
							  (advertiser_alt_id is null OR advertiser_alt_id = 0)";				
			}
			$sql_query .= "
						 LIMIT 1;";
			$stmt = $dbh->prepare($sql_query);					 
			$result = $stmt->execute($values);
			$reviews = $result->fetchRow(MDB2_FETCHMODE_ASSOC);

			if (count($reviews) == 0) {
				$reviews_form .= '<tr>
				<td>
				  <table width="100%" id="rating_tbl">
					<tr>
					  <th valign="top">Rating</th>
					  <td><input name="star" type="radio" class="star" value="1"/>
						<input name="star" type="radio" class="star" value="2"/>
						<input name="star" type="radio" class="star" value="3"/>
						<input name="star" type="radio" class="star" value="4"/>
						<input name="star" type="radio" class="star" value="5"/></td>
					</tr>
					<tr>
					  <th valign="top">Review</th>
					  <td><textarea id="word_count" name="review_txt" class="review_txt" cols="30" rows="3"></textarea><input id="advert_id" name="advert_id" type="hidden" value="'.(int)$_GET['loc_id'].'" /><input id="advert_alt_id" name="advert_alt_id" type="hidden" value="'.(int)$_GET['alt_loc_id'].'" /></td>
					</tr>
					<tr>
					  <th>Max Characters: </th>
					  <td>300</td>
					</tr>
					<tr>
					  <th>Remaining: </th>
					  <td id="counter">300</td>
					</tr>
					<tr>
					  <td colspan="2" align="center"><input id="rev_sub_btn" class="submit_btn" type="button" name="Submit" value="Submit" /></td>
					</tr>
				  </table>
				</td>
				</tr>';
			} else {
				
				$reviews_form .= '<tr>
					  <td colspan="2" align="center">You appear to have already written a review for this advertiser.</td>
					</tr>';
					
			}
			
		} else {
			
			$reviews_form .= '<tr>
					  <td colspan="2" align="center"><a href="javascript:void(0);" onclick="open_login_frm();">You must be logged in to write a review of this advertiser.</a></td>
					</tr>';
			
		}
		
		$reviews_form .= '
			</table>
		  </td>
		</tr>';
		$reviews_form .= $this->list_reviews();

		$reviews_form .= '</table>
						  </td>
						</tr>
					  </table>
					</div>
                    <!-- /New Reviews Page -->';
	
	return $reviews_form;
	}
	
	// draws a list of reviews for the current advertiser
	private function list_reviews() {
		global $adv_rvws_tbl, $dbh, $adv_info_tbl;
		
		if(isset($_POST['page'])) {
			$page = $_POST['page']-1;
			$page = page*ADVERT_REVIEWS_PAGE_LIMIT;
		} else {
			$page = 0;
		}
		
		$values = array();
		$values[] = (int)$_GET['loc_id'];
		$sql_query = "SELECT
						id
					 FROM
						advertiser_reviews
					 WHERE
						approved = 1
					 AND
						advertiser_id = ?";
		if(isset($_GET['alt_loc_id'])){
		  $sql_query .= "
		  				AND
						  advertiser_alt_id = ?";
		  $values[] = (int)$_GET['alt_loc_id'];
		} else {
		  $sql_query .= "
		  				AND
						  (advertiser_alt_id is null OR advertiser_alt_id = 0)";
		}
		$sql_query .= "
					ORDER BY added DESC
					 LIMIT ".$page.",".ADVERT_REVIEWS_PAGE_LIMIT." ;";
	
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		// create review boxes
		$rev_box_arr = array();
		while ($reviews = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$adv_rvws_tbl->get_db_vars($reviews['id']);
			$rev_arr = array();
			$rev_arr['id'] = $reviews['id'];
			$rev_arr['added'] = $adv_rvws_tbl->added;
			$rev_arr['rating'] = $adv_rvws_tbl->rating;
			$rev_arr['review'] = $adv_rvws_tbl->review;
			
			$rev_box_arr[] = $this->reviews_box($rev_arr);
		}
		
		$rev_box_final = '';
		if (count($rev_box_arr) > 0) {
		// create review box area
		$rev_box_final = '<tr>
							<td colspan="2" align="center">'.
							$this->prnt_page_lnks()
							.'</td>
						  <tr>';
		  
		$rev_box_final .= $this->reviews_mid();

		$rev_box_final .= '<tr>
							<td colspan="2">
							  <table border="0" cellspacing="0" cellpadding="4">
								<tr>
								  <td><table border="0" cellspacing="0" cellpadding="4">';
				  
		  $rev_box_final .= implode($this->reviews_mid(),$rev_box_arr);		
				  
		  $rev_box_final .= '</table></td>
						  </tr>
						</table>
					  </td>
					</tr>';
			
		$rev_box_final .= $this->reviews_mid();
		
		$rev_box_final .= '<tr>
							  <td colspan="2" align="center">'.
							  $this->prnt_page_lnks()
							  .'</td>
							<tr>';
		  
		}
	
	return $rev_box_final;
	}
	
	private function reviews_box($rev_arr) {
		
		$review_box = '<tr>
          <td colspan="2" align="center">
		  <table border="0" cellspacing="0" cellpadding="4" class="review_box">
                    <tr>
                      <th>Added</th>
                      <td>'.date('n/j/Y h:i:s A',strtotime($rev_arr['added'])).'</td>
                    </tr>
                    <tr>
                      <th>Rating</th>
                      <td>';
					  
		for($i = 1; $i <= 5; $i++) {
			$review_box .= '<input name="star'.$rev_arr['id'].'" type="radio" class="star" value="'.$i.'" disabled="disabled"'.($i == $rev_arr['rating'] ? ' checked="checked" ' : '').'/>';						
		}
						
		$review_box .= '</td>
					  </tr>
					  <tr>
						<th>Review</th>
						<td>'.$rev_arr['review'].'</td>
					  </tr>
					</table>
				  </td>
				</tr>';
		
	return $review_box;
	}
	
	private function reviews_mid() {
	
		$mid_cnt = '<tr>
                <td><hr class="review_break"></td>
              </tr>';
	
	return $mid_cnt;
	}

	private function prnt_page_lnks() {
		global $dbh;
		
		$values = array();
		$values[] = (int)$_GET['loc_id'];
		$sql_query = "SELECT
						count(*) as cnt
					 FROM
						advertiser_reviews
					 WHERE
						approved = 1
					 AND
						advertiser_id = ?";
		if(isset($_GET['alt_loc_id'])){
		  $sql_query .= "
		  				AND
						  advertiser_alt_id = ?";
		  $values[] = (int)$_GET['alt_loc_id'];
		} else {
		  $sql_query .= "
		  				AND
						  (advertiser_alt_id is null OR advertiser_alt_id = 0)";
		}
		$sql_query .= ";";
	
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		$review_cnt = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		$review_tot = $review_cnt['cnt'];
	
		if(isset($_GET['alt_loc_id'])){
		  $page_link = SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/alt-'.$adv_alt_loc_tbl->id.'/reviews/';
		} else {
		  $page_link = SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/reviews/';				
		}
	
		$pages_arr = array();
		$page_val = 1;
		for($i = 1;$i <= $review_tot;$i += ADVERT_REVIEWS_PAGE_LIMIT){
			$pages_arr[] = '<a href="'.$page_link.$page_val.'/">'.$page_val.'</a>';
			$page_val++;
		}
		
		$page_links = implode(', ',$pages_arr);
	
	return $page_links;
	}

}

?>