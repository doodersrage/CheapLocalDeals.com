<?PHP

class listing_qry {
	public $category;
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
	
	public function __construct() {
		global $geo_data, $cities_tbl, $category_results_pg;
		
		// if city value is set assign city and state values
		if(!empty($_GET['city'])) {
		  // set city values
		  $this->city = $category_results_pg->city;
		  $this->state = $category_results_pg->state;
		}

		// set class variables
		$this->category = $category_results_pg->category;
		$this->view = $category_results_pg->view;
		$this->display = (isset($_GET['display']) ? $_GET['display'] : '');
		$this->alpha_filter = $category_results_pg->alpha_filter;
		$this->search_results = $category_results_pg->search_results;
		$this->zip = $category_results_pg->zip;

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
		  array_unshift($this->list_row,$this->reg_list_head());
		}
		if(count($this->paid_list_row) > 0) {
		  shuffle($this->paid_list_row);
		  array_unshift($this->paid_list_row,$this->sponsored_list_head());
		}
		if(count($this->misc_list_row) > 0) {
		  shuffle($this->misc_list_row);
		  array_unshift($this->misc_list_row,$this->misc_list_head());
		}
		// join array pieces
		$this->list_row = array_merge($this->paid_list_row,$this->list_row,$this->misc_list_row);				
	}
	
	// creates list of advertisers to be displayed within the category listing
	private function get_category_adverts() {
		global $dbh, $cats_tbl, $zip_cds_tbl, $url_nms_tbl, $adv_info_tbl, $cities_tbl, $ste_cty_cat_tbl;		

		$zip_string = implode(', ',$this->zip_array);

		// check for parent or subcategory
		$cats_tbl->get_db_vars($this->category);

		// if selected category is a sub category display normal listing
		if (($cats_tbl->child_cat_count($cats_tbl->id) == 0) || $this->view == 'all') {
		
			// set category val
			$cat_val = ($this->category > 0 ? $this->category : $this->view);

			if (!empty($cat_val)) {
				
			  $list_cat_array = array();
			  $sql_values = array();
			  // get primary category list
			  $sql_query = "SELECT
							  DISTINCT ci.id as advertisers_id
						   FROM
							  advertiser_info ci 
							  LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id 
							  INNER JOIN advertiser_levels al ON al.id = ci.customer_level
						  WHERE
						  ";
			  if($this->view != 'all') {
				$sql_query .= "ac.category_id = ? AND ";
				$sql_values[] = $this->category;
			  }
			  $sql_query .= "ci.zip IN (".$zip_string.") 
							AND ci.account_enabled = 1 
							AND ci.approved = 1 
							AND ci.update_approval = 1";
			  if($this->display == 'dom') {
				$sql_query .= " AND ci.pick = 1 ";
			  }
			  if(!empty($this->alpha_filter)) {
				$sql_query .= " AND ci.company_name LIKE ? ";
				$sql_values[] = $this->alpha_filter.'%';
			  }
						  
			  if (!empty($this->search_results)) {
				$search_terms = explode(" ",$this->search_results);
				$search_query = array();
				foreach ($search_terms as $cur_term) {
					$sql_values[] = '%'.$cur_term.'%';
					$sql_values[] = '%'.$cur_term.'%';
					$sql_values[] = '%'.$cur_term.'%';
					$search_query[] = "(ci.company_name LIKE ? OR ci.products_services LIKE ? OR ci.customer_description LIKE ?)";
				}
				$sql_query .= " AND ".implode(" AND ",$search_query);
			   }
			   $sql_query .= " ORDER BY ci.id ;";
			  
			  // store found values in memcached
			  $results = db_memc_str($sql_query,$sql_values);
			  
			  // added to check for single returns due to memcached system
			  if(count($results) == 1) $results[0] = $results;
			  
			  // set session list array
			  foreach($results as $cur_row) {
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
			  
			}
						
			// clear existing listing array values
			$this->list_row = array();
			$this->paid_list_row = array();
			$this->misc_list_row = array();
			
			if ($this->view != 'all') {
				// pull category info
				$cats_tbl->get_db_vars($this->category);
			}
			
			// added to sort ouput by alpha
			if(isset($_GET['sort'])) {
				if($_GET['sort'] == 'alpha') {
					$advert_name_arr = array();
					// sort by company name
					foreach($list_cat_array as $cur_advert) {
						$adv_info_tbl->get_db_vars($cur_advert);
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
								
				$this->advertisers_id = $cur_row;
				
				// draw row array
				$this->draw_list_row();
				
			}
		} else {
			if (!empty($this->category)) {
			  
			  $list_cat_array = array();
			  
			  // if selected category is a parent category display all categories below
			  $child_array = $cats_tbl->get_child_cats($this->category);
  
  			  // clear listing array values
			  $this->list_row = array();
			  $this->paid_list_row = array();
			  $this->misc_list_row = array();
			  
			  $selected_advert_arr = array();
			  
			  foreach($child_array as $cur_id) {
				  $sql_values = array();
				  $sql_values[] = $cur_id['id'];
				  $sql_query = "SELECT
								  DISTINCT ci.id as advertisers_id, ac.category_id as category_id
							   FROM
								  advertiser_info ci 
								  LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id 
								  INNER JOIN advertiser_levels al ON al.id = ci.customer_level
							  WHERE
							  ac.category_id = ? 
							  AND ci.zip IN (".$zip_string.") 
							  AND ci.account_enabled = 1 
							  AND ci.approved = 1 
							  AND ci.update_approval = 1";
				if(!empty($this->alpha_filter)) {
				  $sql_query .= " AND ci.company_name LIKE ? ";
				  $sql_values[] = $this->alpha_filter.'%';
				}
							  
				  if (!empty($this->search_results)) {
					  $search_terms = explode(" ",$this->search_results);
					  $search_query = array();
					  foreach ($search_terms as $cur_term) {
						$sql_values[] = '%'.$cur_term.'%';
						$sql_values[] = '%'.$cur_term.'%';
						$sql_values[] = '%'.$cur_term.'%';
						$search_query[] = "(ci.company_name LIKE ? OR ci.products_services LIKE ? OR ci.customer_description LIKE ?)";
					  }
					  $sql_query .= " AND ".implode(" AND ",$search_query);
				   }
				   $sql_query .= " ORDER BY ci.id ;";
	  
				  // pull category info
				  $cats_tbl->get_db_vars($this->category);
	
				  // store found values in memcached
				  $results = db_memc_str($sql_query,$sql_values);
				  
				  // added to check for single returns due to memcached system
				  if(count($results) == 1) $results[0] = $results;
							
				  // set session list array
				  foreach($results as $cur_row) {
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
				  
			  }
			}
			
			// added to sort ouput by alpha
			if(isset($_GET['sort'])) {
				if($_GET['sort'] == 'alpha') {
					$advert_name_arr = array();
					// sort by company name
					foreach($list_cat_array as $cur_advert) {
						$adv_info_tbl->get_db_vars($cur_advert);
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
			
			// draw listing array
			foreach($list_cat_array as $cur_row) {
			
				$this->advertisers_id = $cur_row;
				
				// draw row array
				$this->draw_list_row();
				
			}
				
		}
		
	}
	
	// added 9-21-2009 retrives alternate locations for listing
	private function get_category_adverts_alt_loc() {
		global $dbh, $adv_alt_loc_tbl, $cats_tbl, $zip_cds_tbl, $url_nms_tbl, $adv_info_tbl, $cities_tbl, $ste_cty_cat_tbl;		
		
		$zip_string = implode(', ',$this->zip_array);

		// check for parent or subcategory
		$cats_tbl->get_db_vars($this->category);

		// if selected category is a sub category display normal listing
		if (($cats_tbl->child_cat_count($cats_tbl->id) == 0) || $this->view == 'all') {
		
			// set category val
			$cat_val = ($this->category > 0 ? $this->category : $this->view);

			if (!empty($cat_val)) {
				
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
			  if($this->view != 'all') {
				$sql_query .= "ac.category_id = ? AND ";
				$sql_values[] = $this->category;
			  }
			  $sql_query .= " 
						  aal.zip IN (".$zip_string.") 
						  AND aal.enabled = 1 
						  AND ci.account_enabled = 1 
						  AND ci.approved = 1 
						  AND ci.update_approval = 1";
			  if($this->display == 'dom') {
				$sql_query .= " AND ci.pick = 1 ";
			  }
			  if(!empty($this->alpha_filter)) {
				$sql_query .= " AND ci.company_name LIKE ? ";
				$sql_values[] = $this->alpha_filter.'%';
			  }
						  
			  if (!empty($this->search_results)) {
				$search_terms = explode(" ",$this->search_results);
				$search_query = array();
				foreach ($search_terms as $cur_term) {
				  $sql_values[] = '%'.$cur_term.'%';
				  $sql_values[] = '%'.$cur_term.'%';
				  $sql_values[] = '%'.$cur_term.'%';
				  $search_query[] = "(ci.company_name LIKE ? OR ci.products_services LIKE ? OR ci.customer_description LIKE ?)";
				}
				$sql_query .= " AND ".implode(" AND ",$search_query);
			   }
			   $sql_query .= " ORDER BY aal.id ;";
					  
				// store found values in memcached
				$results = db_memc_str($sql_query,$sql_values);
				
				// added to check for single returns due to memcached system
				if(count($results) == 1) $results[0] = $results;
						  
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
			  
			}
						
			if ($this->view != 'all') {
				// pull category info
				$cats_tbl->get_db_vars($this->category);
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
		} else {
			
			if (!empty($this->category)) {
			  
			  $list_cat_array = array();
			  
			  // if selected category is a parent category display all categories below
			  $child_array = $cats_tbl->get_child_cats($this->category);
			  
			  $selected_advert_arr = array();
			  
			  foreach($child_array as $cur_id) {
				  $sql_values = array();
				  $sql_values[] = $cur_id['id'];
				  $sql_query = "SELECT
								  DISTINCT aal.id as alt_id
							   FROM
								  advertiser_alt_locations aal 
								  INNER JOIN advertiser_info ci ON aal.advertiser_id = ci.id 
								  LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id 
								  INNER JOIN advertiser_levels al ON al.id = ci.customer_level
							  WHERE
							  ac.category_id = ? 
							  AND aal.zip IN (".$zip_string.") 
							  AND aal.enabled = 1 
							  AND ci.account_enabled = 1 
							  AND ci.approved = 1 
							  AND ci.update_approval = 1";
			  if(!empty($this->alpha_filter)) {
				$sql_query .= " AND ci.company_name LIKE ? ";
				$sql_values[] = $this->alpha_filter.'%';
			  }
							  
				  if (!empty($this->search_results)) {
					  $search_terms = explode(" ",$this->search_results);
					  $search_query = array();
					  foreach ($search_terms as $cur_term) {
						$sql_values[] = '%'.$cur_term.'%';
						$sql_values[] = '%'.$cur_term.'%';
						$sql_values[] = '%'.$cur_term.'%';
						$search_query[] = "(ci.company_name LIKE ? OR ci.products_services LIKE ? OR ci.customer_description LIKE ?)";
					  }
					  $sql_query .= " AND ".implode(" AND ",$search_query);
				   }
				   $sql_query .= " ORDER BY aal.id ;";
	  
				  // pull category info
				  $cats_tbl->get_db_vars($this->category);
						   
				  // store found values in memcached
				  $results = db_memc_str($sql_query,$sql_values);
				  
				  // added to check for single returns due to memcached system
				  if(count($results) == 1) $results[0] = $results;
							
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
			
			// draw listing array
			foreach($list_cat_array as $cur_row) {
			
				$this->advert_alt_loc_id = $cur_row;
				
				// draw row array
				$this->draw_alt_list_row();
				
			}
				
		}
		
	}
	
	// added 10-15-2009 retrieves non-cert advertisers
	private function get_noncert_adverts() {
			global $dbh, $cats_tbl, $zip_cds_tbl, $cities_tbl;		
		
		if ($this->advert_cnt == 0) {
				
			$zip_string = implode(', ',$this->zip_array);
	
			// check for parent or subcategory
			$cats_tbl->get_db_vars($this->category);
		
			// set category val
			$cat_val = ($this->category > 0 ? $this->category : $this->view);
	
			if (!empty($cat_val)) {
			  $sql_values = array();
			  $list_cat_array = array();
			  // get primary category list
			  $sql_query = "SELECT
							  DISTINCT id
						   FROM
							  businesses
						  WHERE
						  zip IN (".$zip_string.") ";
			  if(!empty($this->alpha_filter)) {
				$sql_query .= " AND name LIKE ? ";
				$sql_values[] = $this->alpha_filter.'%';
			  }
						  
			  if (!empty($this->search_results)) {
				$search_terms = explode(" ",$this->search_results);
				$search_query = array();
				foreach ($search_terms as $cur_term) {
					$sql_values[] = '%'.$cur_term.'%';
					$sql_values[] = '%'.$cur_term.'%';
					$search_query[] = "(name LIKE ? OR description LIKE ?) ";
				}
				$sql_query .= " AND ".implode(" AND ",$search_query);
			   }
			 
			  $sql_query .= " ORDER BY id LIMIT 20;";
		  
			  // store found values in memcached
			  $results = db_memc_str($sql_query,$sql_values);
			  
			  // added to check for single returns due to memcached system
			  if(count($results) == 1) $results[0] = $results;
						
			  // set session list array
			  foreach($results as $cur_row) {
				$list_cat_array[$cur_row['id']] = $cur_row['id'];
			  }
			  
			}
						
			if ($this->view != 'all') {
				// pull category info
				$cats_tbl->get_db_vars($this->category);
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
	
	// list header for regular advertisers
	private function reg_list_head() {
			
		$row_foot = '<div class="regular_list_head"><div class="rlh_left_corner"></div><div class="rlh_right_corner"></div><div class="header_txt">Regular Listing</div></div>'.LB;
			
	return $row_foot;
	}
	
	// list header for sponsored advertisers
	private function sponsored_list_head() {
			
		$row_foot = '<div class="regular_list_head"><div class="rlh_left_corner"></div><div class="rlh_right_corner"></div><div class="header_txt">Premium Listings</div></div>'.LB;
			
	return $row_foot;
	}
	
	// list header for sponsored advertisers
	private function misc_list_head() {
			
		$row_foot = '<div class="regular_list_head"><div class="rlh_left_corner"></div><div class="rlh_right_corner"></div><div class="header_txt">Extended Results Deals Not Yet Available</div></div>'.LB;
			
	return $row_foot;
	}
	
	// build quantity drop down
	private function build_quantity_drop_down() {
		
		$quantity_drop_down = '<select class="cert_sel_dd" size="1" name="quantity">'.LB;
		
			for($i = 1;$i <= ADD_TO_CART_QUANTITY_DROP_DOWN; $i++) {
				$quantity_drop_down .= '<option>'.$i.'</option>'.LB;
			}
			
		$quantity_drop_down .= '</select>'.LB;
			
	return $quantity_drop_down;
	}
	
	// build coupon value drop down
	private function build_value_drop_down() {
			global $cert_amt_tbl, $adv_info_tbl, $adv_alt_loc_tbl, $cats_tbl;
				
		$cert_amounts = $cert_amt_tbl->get_certificate_amounts();
		
		$this->default_requirement = '';
		
		$value_drop_down = '';
		
		// prints selected certificate requirements
		if(is_array($adv_info_tbl->certificate_requirements)) {
			$this->sel_restriction_src .= 'var curArray = [];'.LB; 
			$i = 0;
			foreach($adv_info_tbl->certificate_requirements as $id => $value) {
				$i++;
				
				$requirement_value = '';
				
				// set cert req string
				$requirement_value = htmlentities(set_cert_agreement_str($value['type'],$value['value']));
				
				if (!empty($value['excludes'])) {
					$requirement_value .= '<br/>Excludes: '.htmlentities($value['excludes']);
				}
				
				// added to allow free certificate download
				$cert_amt_tbl->get_db_vars($id);
				if ($cert_amt_tbl->cost == 0 && is_numeric($cert_amt_tbl->cost)) {
					$requirement_value .= '<br/><a class="free_crt_lnk" onclick="form_submit(\'cert_frm_'.$adv_info_tbl->id.'\')" href="javascript: void(0)">This Certificate Free-Click Here</a>';
				}
				
				$this->sel_restriction_src .= 'curArray['.$id.'] = "'.str_replace(chr(10)," ",str_replace(array('"',chr(13)),array('\"'," "),$requirement_value)).'";'.LB; 
				if ($i == 1) $this->default_requirement = $requirement_value;
			}
			$this->sel_restriction_src .= 'requirementsArray['.$adv_info_tbl->id.$this->sel_cat.$adv_alt_loc_tbl->id.'] = curArray;'.LB; 
		}

		// draws level drop down		
		$value_drop_down .= '<select class="cert_sel_dd" size="1" id="cert_'.$adv_info_tbl->id.$this->sel_cat.$adv_alt_loc_tbl->id.'" name="certificate_amount_id" style="width:220px;" onchange="update_requirements('.$adv_info_tbl->id.$this->sel_cat.$adv_alt_loc_tbl->id.')">'.LB;
		
		foreach($cert_amounts as $cur_cert_amt) {
			if (!empty($adv_info_tbl->certificate_levels[$cur_cert_amt['id']])) {
				if ($adv_info_tbl->certificate_levels[$cur_cert_amt['id']] == 1) {
				  if(is_numeric($cur_cert_amt['cost'])) {
					  if($cur_cert_amt['cost'] > 0) {
						$cost_str = 'You Pay $'.$cur_cert_amt['cost'];
					  } else {
						$cost_str = 'FREE!';
					  }
				  } else {
					  $cost_str = 'You Pay $'.$adv_info_tbl->certificate_requirements[$cur_cert_amt['id']]['blank_cost'];
				  }
				  if(is_numeric($cur_cert_amt['discount_amount'])) {
					  $disc_amt = round($cur_cert_amt['discount_amount'],2);
				  } else {
					  $disc_amt = round($adv_info_tbl->certificate_requirements[$cur_cert_amt['id']]['blank_val'],2);
				  }
				  if ($adv_info_tbl->certificate_levels[$cur_cert_amt['id']] == 1) $value_drop_down .= '<option value="'.$cur_cert_amt['id'].'">$'.$disc_amt.' Gift Certificate ('.$cost_str.')</option>'.LB;
				}
			}
		}
		
		$value_drop_down .= '</select>'.LB; 
		
	return $value_drop_down;
	}
	
	// used for drawing list row
	private function draw_list_row() {
		global $cats_tbl, $adv_info_tbl, $adv_lvls_tbl, $api_load;
		
		// pull advertisers info
		$adv_info_tbl->get_db_vars($this->advertisers_id);		
		
		// pull advertiser level info
		$adv_lvls_tbl->get_db_vars($adv_info_tbl->customer_level);		
		
		// write category name string for view all option
		$category_name = $this->assign_list_row_cat_nm();
		
		// loads advertiser image
		$location_image = $this->assign_list_row_advert_img();
		
		$page_link = SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/';

		// added 12/11/2009 to write urls for api system
		if ($api_load->status == 1) {
		  $page_link = SITE_URL.'?page=advertiser&adid='.$adv_info_tbl->id;
		}
		
		$values['page_link'] = $page_link;
		$values['no_follow'] = 0;
		$values['location_image'] = $location_image;
		$values['company_name'] = htmlentities($adv_info_tbl->company_name);
		$values['category_name'] = htmlentities($category_name);
		$values['hide_address'] = $adv_info_tbl->hide_address;
		$values['address_1'] = $adv_info_tbl->address_1;
		$values['address_2'] = $adv_info_tbl->address_2;
		$values['city'] = $adv_info_tbl->city;
		$values['state'] = $adv_info_tbl->state;
		$values['zip'] = $adv_info_tbl->zip;
		$values['phone_number'] = $adv_info_tbl->phone_number;
		$values['products_services'] = htmlentities($adv_info_tbl->products_services);
		$values['certificate_requirements'] = $adv_info_tbl->certificate_requirements;
		$values['id'] = $adv_info_tbl->id;
		  
		$cur_list_data = $this->draw_list_row_elems($values);
		 
	// sort newly generated row
	$this->sort_list_row($cur_list_data);
	}
	
	// added 9-21-2009 used for drawing list row for alternate locations
	private function draw_alt_list_row() {
		global $adv_alt_loc_tbl, $cats_tbl, $adv_info_tbl, $adv_lvls_tbl, $api_load;
		
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
		
		$page_link = SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/alt-'.$adv_alt_loc_tbl->id.'/';

		// added 12/11/2009 to write urls for api system
		if ($api_load->status == 1) {
		  $page_link = SITE_URL.'?page=advertiser&adid='.$adv_info_tbl->id.'&altadid='.$adv_alt_loc_tbl->id;
		}

		$values['page_link'] = $page_link;
		$values['no_follow'] = 0;
		$values['location_image'] = $location_image;
		$values['company_name'] = htmlentities($adv_info_tbl->company_name);
		$values['category_name'] = htmlentities($category_name);
		$values['hide_address'] = $adv_alt_loc_tbl->hide_address;
		$values['address_1'] = $adv_alt_loc_tbl->address_1;
		$values['address_2'] = $adv_alt_loc_tbl->address_2;
		$values['city'] = $adv_alt_loc_tbl->city;
		$values['state'] = $adv_alt_loc_tbl->state;
		$values['zip'] = $adv_alt_loc_tbl->zip;
		$values['phone_number'] = $adv_alt_loc_tbl->phone_number;
		$values['products_services'] = htmlentities($adv_alt_loc_tbl->products_services);
		$values['certificate_requirements'] = $adv_info_tbl->certificate_requirements;
		$values['id'] = $adv_info_tbl->id;
		$values['alt_id'] = $adv_alt_loc_tbl->id;
		  
		$cur_list_data = $this->draw_list_row_elems($values);
		  
	// sort newly generated row
	$this->sort_list_row($cur_list_data);
	}
	
	// draws row for non-cert offering advertiser
	private function draw_noncert_list_row() {
		global $cats_tbl, $bus_tbl;
		
		// get alt location info
		$bus_tbl->get_db_vars($this->advert_noncert_id);
		
		// write category name string for view all option
		$category_name = $this->assign_list_row_cat_nm();
		
		// no links are used for non-cert advertisers
		$page_link = $bus_tbl->url;

		$values['page_link'] = $page_link;
		$values['no_follow'] = 1;
		$values['location_image'] = '';
		$values['company_name'] = htmlentities($bus_tbl->name);
		$values['category_name'] = htmlentities($category_name);
		$values['hide_address'] = 0;
		$values['address_1'] = $bus_tbl->address;
		$values['address_2'] = '';
		$values['city'] = $bus_tbl->city;
		$values['state'] = $bus_tbl->state;
		$values['zip'] = $bus_tbl->zip;
		$values['phone_number'] = $bus_tbl->phone;
		$values['products_services'] = htmlentities($bus_tbl->description);
		$values['certificate_requirements'] = '';
		$values['id'] = $bus_tbl->id;
		  
		$cur_list_data = $this->draw_list_row_elems($values);
		  
	// sort newly generated row
	$this->sort_list_row($cur_list_data);
	}
	
	// generate list row content
	private function draw_list_row_elems($values) {
	  global $man_ill_char, $adv_ratings;
		
	  // clean description
	  $products_services = $values['products_services'];
	  
	  // added to set image height and width settings
	  $image_location = CONNECTION_TYPE.'includes/resize_image.deal?image='.urlencode($values['location_image']).'&amp;new_width='.LISTING_IMAGE_WIDTH.'&amp;new_height='.LISTING_IMAGE_HEIGHT;
		
	  $alt_set = (!empty($values['alt_id']) ? 1 : 0);
	
	  // set rating id values
	  $adv_ratings->loc_id = $values['id'];
	  $adv_ratings->alt_loc_id = (!empty($values['alt_id']) ? $values['alt_id'] : '');
			  
	  $cur_list_data = '<div class="listItem">
						  <div class="advet_box_left">
						  <a'.($values['no_follow'] == 1 ? ' rel="nofollow"' : '').' href="'.$values['page_link'].'"><img src="'.$image_location.'" alt="' . htmlentities($values['company_name']) . ' image" class="advertBoxImg" /></a>
						  '.$values['category_name'].'
						  </div>
						  <div class="advert_box_mid">
						  <p><a'.($values['no_follow'] == 1 ? ' rel="nofollow"' : '').' href="'.$values['page_link'].'"><strong>'.$values['company_name'].'</strong></a><br/>
							  '.($values['hide_address'] != 1 ? $values['address_1'].'<br/>
							  '.($values['address_2'] != '' ? $values['address_2'].'<br/>' : '').'
							  '.$values['city'].', '.$values['state'].' '.$values['zip'] : '').'<br/>
							  '.$values['phone_number'].'</p>'
							. $adv_ratings->get_rating()
							.'
							<p><b>Products &amp; Services:</b><br/>';
						
					  $limit = 150;
					  if (strlen($products_services) > $limit) {
						  $cur_list_data .= substr($products_services, 0, strrpos(substr($products_services, 0, $limit), ' ')) . '...';
					  } else {
						  $cur_list_data .= $products_services;
					  }
					  
						  $cert_req_arr = $values['certificate_requirements'];
					  
						  $cur_list_data .= ' <a'.($values['no_follow'] == 1 ? ' rel="nofollow"' : '').' href="'.$values['page_link'].'"> (info)</a></p>
						  </div>
	  <div class="advert_box_right">'.
	  (!empty($cert_req_arr) ? draw_cert_form($this->build_value_drop_down(),$values['id'],$this->default_requirement,$this->build_quantity_drop_down(),'review_'.(!empty($values['id']) ? $values['id'] : '').$this->sel_cat.(!empty($values['alt_id']) ? $values['alt_id'] : '')) : '<strong>Claim your Listing!</strong><br />
Is This your business? Contact a Sales Representative and find out how you can promote your businesses deals on CheapLocalDeals.com or
<a href="'.SITE_SSL_URL.'advertiser-signup/">Click here to get started today</a>.').'</div>
			  <div class="pop_clear"></div></div>'.LB;
	  
	return $cur_list_data;
	}

	// added 9-21-2009 assigns listing row advertiser image
	private function assign_list_row_advert_img() {
		global $adv_info_tbl;
				
		// loads advertiser image
		if(!empty($adv_info_tbl->image)) {
			$location_image = 'customers/'.$adv_info_tbl->image;
		} elseif(!empty($cats_tbl->image)) {
			$location_image = 'category/'.$cats_tbl->image;
		} else {
			$location_image = '';
		}

	return $location_image;
	}

	// added 9-21-2009 writes list row category name string
	private function assign_list_row_cat_nm() {
		global $cats_tbl;
		
		// write category name string for view all option
		if($this->multicat == 1) {
			$cats_tbl->get_db_vars($this->sel_cat);
			$category_name[] = $cats_tbl->category_name;
			// if category has sub category add to name string
			if($cats_tbl->parent_category_id > 0) {
				$cats_tbl->get_db_vars($cats_tbl->parent_category_id);
				$category_name[] = $cats_tbl->category_name;
			}
			krsort($category_name);
			$category_name = implode(' - ',$category_name);
			
			$category_name = '<span id="listing_category_name">Category: '.$category_name.'</span>';
		} else {
			$category_name = '';
		}
		
	return $category_name;
	}
	
	// builds list of available zip codes
	private function build_zip_array() {
	  global $zip_cds_tbl,$cities_tbl;
	
	  if(!empty($_GET['city'])) {
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