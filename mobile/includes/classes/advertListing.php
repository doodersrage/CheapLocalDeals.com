<?PHP

class advertListing {
	public $view;
	public $default_requirement;
	public $sel_restriction_src;
	public $advert_cnt = 0;
	public $state, $city;
	public $list_row = array();
	public $paid_list_row = array();
	public $misc_list_row = array();
	public $zip_array = array();
	// used for listing
	private $advert_alt_loc_id;
	private $advert_noncert_id;
	private $advertisers_id;
	private $alpha_filter;
	private $search_results;
	private $sel_cat;
	private $multicat;
	private $display;
	
	public function __construct($res_limit = 1) {
		global $geo_data, $cities_tbl;
		
		// added to allow limit override
		$this->res_limit = $res_limit;
		
		// set radius data
		if(empty($_SESSION['set_radius'])) {
			$_SESSION['set_radius'] = DEF_MIN_RADIUS;
		}
				
		// if city value is set assign city and state values
		// set city values
		// if city value is set assign city and state values
		if(!empty($_GET['cid'])) {
		  // set city values
		  $cities_tbl->get_db_vars($_GET['cid']);
		  $this->city = $cities_tbl->city;
		  $this->state = $cities_tbl->state;
		} else {
		  $cities_tbl->get_db_vars($geo_data->cityid);
		  $this->city = $cities_tbl->city;
		  $this->state = $cities_tbl->state;
		}
		
		// set class variables
		$this->display = (isset($_GET['display']) ? $_GET['display'] : '');
		$this->zip = (!empty($_SESSION['cur_zip']) ? $_SESSION['cur_zip'] : '' );

		// get zip array for listing
		$this->build_zip_array();
		
		// build listing rows
		$this->get_category_adverts();
		$this->get_category_adverts_alt_loc();
		$this->advert_cnt += count($this->list_row);
		// get listing count
		$this->advert_cnt += count($this->paid_list_row);
		// get listing count
		$this->get_noncert_adverts();
		$this->advert_cnt += count($this->misc_list_row);

		// merg sponsored and regular listing arrays
		// randomize order of listings and add section header
		if(count($this->list_row) > 0) {
		  shuffle($this->list_row);
		}
		if(count($this->paid_list_row) > 0) {
		  shuffle($this->paid_list_row);
		}
		if(count($this->paid_list_row) > 0 || count($this->paid_list_row) > 0) {
		  $this->list_row = array_merge($this->paid_list_row,$this->list_row);
		  array_unshift($this->list_row,'<div class="header_txt">Certificates</div>');
		}
		if(count($this->misc_list_row) > 0) {
		  shuffle($this->misc_list_row);
		  array_unshift($this->misc_list_row,'<div class="header_txt">Advertisers</div>');
		}
		// join array pieces
		$this->list_row = array_merge($this->list_row,$this->misc_list_row);				
	}
	
	// creates list of advertisers to be displayed within the category listing
	private function get_category_adverts() {
		global $dbh, $zip_cds_tbl, $url_nms_tbl, $adv_info_tbl, $cities_tbl, $ste_cty_cat_tbl;		

		$zip_string = implode(', ',$this->zip_array);
			  
			  $list_cat_array = array();
  
  			  // clear listing array values
			  $this->list_row = array();
			  $this->paid_list_row = array();
			  $this->misc_list_row = array();
			  
			  $selected_advert_arr = array();
			  
				  $sql_values = array();
				  $sql_query = "SELECT
								  ci.id as advertisers_id, ac.category_id as category_id
							   FROM
								  advertiser_info ci 
								  LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id 
								  INNER JOIN advertiser_levels al ON al.id = ci.customer_level
							  WHERE ";
				if($_GET['ccid'] == 'dom') {
				  $sql_query .= " ci.pick = 1 AND ";
				} else {
				  $sql_query .= "ac.category_id = ? AND ";
				  $sql_values[] = $_GET['ccid'];
				}
				$sql_query .= " ci.zip IN (".$zip_string.") 
								AND ci.account_enabled = 1 
								AND ci.approved = 1 
								AND ci.update_approval = 1
							  ;";
				$stmt = $dbh->prepare($sql_query);					 
				$result = $stmt->execute($sql_values);

				while($cur_row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				  	  // added to check level selected and if payment info has been entered
					  $adv_info_tbl->get_db_vars($cur_row['advertisers_id']);
					  if ($adv_info_tbl->customer_level != 3) {
					  	if(!empty($adv_info_tbl->payment_method)) {
						  $list_cat_array[$cur_row['advertisers_id']] = $cur_row['advertisers_id'];
						}
				  	  } else {
						  $list_cat_array[$cur_row['advertisers_id']] = $cur_row['advertisers_id'];
					  }
				  }
				  						
			// draw listing array
			foreach($list_cat_array as $cur_row) {
			
				$this->advertisers_id = $cur_row;
				
				// draw row array
				$this->draw_list_row();
				
			}
		
	}
	
	// added 9-21-2009 retrives alternate locations for listing
	private function get_category_adverts_alt_loc() {
		global $dbh, $adv_alt_loc_tbl, $zip_cds_tbl, $url_nms_tbl, $adv_info_tbl, $cities_tbl, $ste_cty_cat_tbl;		
		
		$zip_string = implode(', ',$this->zip_array);
				
		$list_cat_array = array();
		$sql_values = array();
		// get primary category list
		$sql_query = "SELECT
						DISTINCT aal.id as alt_id
					FROM
						advertiser_alt_locations aal 
					   INNER JOIN 
						advertiser_info ci ON aal.advertiser_id = ci.id 
					   LEFT JOIN 
						advertiser_categories ac ON ac.advertiser_id = ci.id 
					   INNER JOIN 
						advertiser_levels al ON al.id = ci.customer_level
					WHERE
					";
		if($_GET['ccid'] == 'dom') {
		  $sql_query .= " ci.pick = 1 AND ";
		} else {
		  $sql_query .= "ac.category_id = ? AND ";
		  $sql_values[] = $_GET['ccid'];
		}
		$sql_query .= " 
					aal.zip IN (".$zip_string.") 
					AND aal.enabled = 1 
					AND ci.account_enabled = 1 
					AND ci.approved = 1 
					AND ci.update_approval = 1";
		 $sql_query .= ";";
		 
		  // store found values in memcached
		  $results = db_memc_str($sql_query,$sql_values);
					
		  // set session list array
		  foreach($results as $cur_row) {
			// added to check level selected and if payment info has been entered
			$adv_alt_loc_tbl->get_db_vars($cur_row['alt_id']);
			$adv_info_tbl->get_db_vars($adv_alt_loc_tbl->advertiser_id);
			if ($adv_info_tbl->customer_level != 3) {
			  if(!empty($adv_info_tbl->payment_method)) {
				$list_cat_array[$cur_row['alt_id']] = $cur_row['alt_id'];
			  }
			} else {
				$list_cat_array[$cur_row['alt_id']] = $cur_row['alt_id'];
			}
		}
			  
	  // added to sort ouput by alpha
	  if(isset($_GET['sort'])) {
		  if($_GET['sort'] == 'alpha') {
			  $advert_name_arr = array();
			  // sort by company name
			  foreach($list_cat_array as $cur_advert) {
				$adv_alt_loc_tbl->get_db_vars($cur_advert);
				$adv_info_tbl->get_db_vars($adv_alt_loc_tbl->advertiser_id);
				$advert_name_arr[$cur_advert] = $adv_info_tbl->company_name;
			  }
			  // sort newly created array
			  asort($advert_name_arr);
			  // throw sorted array back into listing array
			  $list_cat_array = array();
			  foreach($advert_name_arr as $id => $value) {
				  $list_cat_array[$id] = $id;
			  }
		  }		
	  }		
	  
	  // build list output
	  foreach($list_cat_array as $cur_row) {
						  
		  $this->advert_alt_loc_id = $cur_row;
		  
		  // draw row array
		  $this->draw_alt_list_row();
		  
	  }		
	}
	
	// added 10-15-2009 retrieves non-cert advertisers
	private function get_noncert_adverts() {
			global $dbh, $zip_cds_tbl, $cities_tbl;		
		
		if ($this->advert_cnt == 0) {
				
			$zip_string = implode(', ',$this->zip_array);
				
			  $list_cat_array = array();
			  // get primary category list
			  $sql_query = "SELECT
							  id
						   FROM
							  businesses
						  WHERE
							zip IN (".$zip_string.") 
						  ORDER BY RAND()
						  LIMIT 0,20 ;";
		  
			  $stmt = $dbh->prepare($sql_query);					 
			  $result = $stmt->execute();
			  
			  // set session list array
			  while($cur_row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				$list_cat_array[$cur_row['id']] = $cur_row['id'];
			  }
			  
			
			// build list output
			foreach($list_cat_array as $cur_row) {
								
				$this->advert_noncert_id = $cur_row;
				
				// draw row array
				$this->draw_noncert_list_row();
				
			}		
		}
	}

	// added 9-21-2009 used for sorting generated listing row into the proper row array value
	private function sort_list_row($cur_list_data,$misc_vert = 0) {
		global $adv_lvls_tbl;
		
		  // check advertiser level and toss data into appropriate array
		  if($adv_lvls_tbl->level_renewal_cost <= 0 && $this->advert_noncert_id == '') {
		  	// assign regulat listing header to first list item
			$this->list_row[] = $cur_list_data;
		  } elseif($this->advert_noncert_id > 0) {
			$this->misc_list_row[] = $cur_list_data;
		  } else {
		  	// assign paid listing header to first list item
			$this->paid_list_row[] = $cur_list_data;
		  }
		  
	}
	
	// added 9-21-2009 used for drawing list row for alternate locations
	private function draw_alt_list_row() {
		global $adv_alt_loc_tbl, $adv_info_tbl, $adv_lvls_tbl, $api_load;
		
		// get alt location info
		$adv_alt_loc_tbl->get_db_vars($this->advert_alt_loc_id);
		
		$this->advertisers_id = $adv_alt_loc_tbl->advertiser_id;
		
		// pull advertisers info
		$adv_info_tbl->get_db_vars($this->advertisers_id);		
		
		// pull advertiser level info
		$adv_lvls_tbl->get_db_vars($adv_info_tbl->customer_level);		
		
		// write category name string for view all option
		$category_name = $this->assign_list_row_cat_nm();
		
		// loads advertiser image
		$location_image = $this->assign_list_row_advert_img();
		
		$page_link = MOB_URL.'altad/'.$adv_info_tbl->id.'/'.$adv_alt_loc_tbl->id;

		$values['page_link'] = $page_link;
		$values['location_image'] = $location_image;
		$values['company_name'] = htmlentities($adv_info_tbl->company_name);
		$values['id'] = $adv_info_tbl->id;
		$values['alt_id'] = $adv_alt_loc_tbl->id;
		  
		$cur_list_data = $this->draw_list_row_elems($values);
		  
	// sort newly generated row
	$this->sort_list_row($cur_list_data);
	}
	
	// used for drawing list row
	private function draw_list_row() {
		global $adv_info_tbl, $adv_lvls_tbl, $api_load;
		
		// pull advertisers info
		$adv_info_tbl->get_db_vars($this->advertisers_id);		
		
		// pull advertiser level info
		$adv_lvls_tbl->get_db_vars($adv_info_tbl->customer_level);		
				
		// loads advertiser image
		$location_image = $this->assign_list_row_advert_img();
		
		$page_link = MOB_URL.'ad/'.$this->advertisers_id;
		
		$values['page_link'] = $page_link;
		$values['location_image'] = $location_image;
		$values['company_name'] = htmlentities($adv_info_tbl->company_name);
		$values['id'] = $adv_info_tbl->id;
		  
		$cur_list_data = $this->draw_list_row_elems($values);
		 
	// sort newly generated row
	$this->sort_list_row($cur_list_data);
	}
	
	// draws row for non-cert offering advertiser
	private function draw_noncert_list_row() {
		global $bus_tbl;
		
		// get alt location info
		$bus_tbl->get_db_vars($this->advert_noncert_id);
		
		// no links are used for non-cert advertisers
		$page_link = $bus_tbl->url;

		$values['page_link'] = MOB_URL.'bus/'.$this->advert_noncert_id;
		$values['location_image'] = '';
		$values['company_name'] = htmlentities($bus_tbl->name);
		$values['id'] = $bus_tbl->id;
		  
		$cur_list_data = $this->draw_list_row_elems($values);
		  
	// sort newly generated row
	$this->sort_list_row($cur_list_data);
	}
	
	// generate list row content
	private function draw_list_row_elems($values) {
		global $man_ill_char, $adv_ratings;
			  
	  // added to set image height and width settings
	  $image_location = CONNECTION_TYPE.'includes/resize_image.deal?image='.urlencode($values['location_image']).'&amp;new_width=55&amp;new_height=55';
		
	  $alt_set = (!empty($values['alt_id']) ? 1 : 0);
	  
	  // set rating id values
	  $adv_ratings->loc_id = $values['id'];
	  $adv_ratings->alt_loc_id = (!empty($values['alt_id']) ? $values['alt_id'] : '');
		
	  $cur_list_data = '<a href="'.$values['page_link'].'" class="advertList"><div class="textBlk">'.$values['company_name'].'</div><div class="imgBlk"><img align="middle" src="'.$image_location.'" alt="' . htmlentities($values['company_name']) . ' image" /></div> </a>'.LB;
	  
	return $cur_list_data;
	}

	// added 9-21-2009 assigns listing row advertiser image
	private function assign_list_row_advert_img() {
		global $adv_info_tbl;
				
		// loads advertiser image
		if(!empty($adv_info_tbl->image)) {
			$location_image = 'customers/'.$adv_info_tbl->image;
		} else {
			$location_image = '';
		}

	return $location_image;
	}
	
	// builds list of available zip codes
	private function build_zip_array() {
	  global $zip_cds_tbl,$cities_tbl,$geo_data;
	
	  if(!empty($geo_data->cityid) || !empty($_GET['cid'])) {
		  // build zip codes array
		  $zip_cds_tbl->city_id = $cities_tbl->id;
		  $this->zip_array = $zip_cds_tbl->get_list();
		  $this->zip = $this->zip_array[0];
		  $this->zip_array = $zip_cds_tbl->fetchZipsInRadiusByZip($this->zip, $_SESSION['set_radius'], 100 );
	  } else {
		  // get surrounding zips
		  $this->zip_array = $zip_cds_tbl->fetchZipsInRadiusByZip($this->zip, $_SESSION['set_radius'], 100 );
	  }
	
	}
	
}

?>