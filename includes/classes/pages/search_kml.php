<?PHP

// this document generates Google Maps KML file
class google_kml_gen {
	private $category, $zip, $radius, $view, $city, $state, $advert_alt_loc_id, $advertisers_id, $zip_string;
	public $advert_noncert_id, $list_row = array(), $mapmarkers = array();

	public function __construct() {
		global $geo_data, $cities_tbl, $zip_cds_tbl;
			
		$this->category = $_GET['set_cat'];
		$this->zip = (!empty($_GET['set_zip']) ? $_GET['set_zip'] : '' );
		$this->radius = $_GET['radius'];
		$this->view = $_GET['view'];
		$this->city = $_GET['city'];
		$this->state = $_GET['state'];
		if(!empty($_GET['city'])) {
			$cities_tbl->city_state_search($_GET['city'],$_GET['state']);
			$_GET['city'] = $cities_tbl->id;
		}

		// if city value is set assign city and state values
		if(!empty($_GET['city'])) {
			// set city values
			$cities_tbl->get_db_vars($_GET['city']);
			$this->city = $cities_tbl->city;
			$this->state = $cities_tbl->state;
		}
			
		if(!empty($_GET['city'])) {
			// build zip codes array
			$zip_cds_tbl->city_id = $cities_tbl->id;
			$zip_array = $zip_cds_tbl->get_list();
			$zip_array = $zip_cds_tbl->fetchZipsInRadiusByZip($zip_array[0], $this->radius, 100 );
		} else {
			// get surrounding zips
			$zip_array = $zip_cds_tbl->fetchZipsInRadiusByZip($this->zip, $this->radius, 100 );
		}
		$this->zip_string = implode(', ',$zip_array);
	}
	
	// creates list of advertisers to be displayed within the category listing
	private function get_category_adverts() {
			global $dbh, $cats_tbl, $zip_cds_tbl, $url_nms_tbl, $adv_info_tbl, $cities_tbl, $ste_cty_cat_tbl;		
		

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
							  advertiser_info ci LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id INNER JOIN advertiser_levels al ON al.id = ci.customer_level
						  WHERE ";
			  if($this->view != 'all') {
				$sql_query .= "ac.category_id = ? AND ";
				$sql_values[] = $this->category;
			  }
			  $sql_query .= "ci.zip IN (".$this->zip_string.") 
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
					  
			  $stmt = $dbh->prepare($sql_query);					 
			  $result = $stmt->execute($sql_values);
			  
			  // set session list array
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
			  
			}
						
			// clear existing listing array values
			$this->list_row = array();
			$this->paid_list_row = array();
			
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
			  
			  $selected_advert_arr = array();
			  
			  foreach($child_array as $cur_id) {
				  $sql_values = array();
				  $sql_values[] = $cur_id['id'];
				  $sql_query = "SELECT
								  DISTINCT ci.id as advertisers_id, ac.category_id as category_id
							   FROM
								  advertiser_info ci LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id INNER JOIN advertiser_levels al ON al.id = ci.customer_level
							  WHERE
							  ac.category_id = ? 
							  AND ci.zip IN (".$this->zip_string.") 
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
					   
				  $stmt = $dbh->prepare($sql_query);					 
				  $result = $stmt->execute($sql_values);
	  
				  // pull category info
				  $cats_tbl->get_db_vars($this->category);
			  
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

		// check for parent or subcategory
		$cats_tbl->get_db_vars($this->category);

		// if selected category is a sub category display normal listing
		if (($cats_tbl->child_cat_count($cats_tbl->id) == 0) || $this->view == 'all') {
		
			// set category val
			$cat_val = ($this->category > 0 ? $this->category : $this->view);

			if (!empty($cat_val)) {
				
			  $list_cat_array = array();
			  // get primary category list
			  $sql_values = array();
			  $sql_query = "SELECT
							  DISTINCT aal.id as alt_id
						   FROM
							  advertiser_alt_locations aal INNER JOIN advertiser_info ci ON aal.advertiser_id = ci.id LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id INNER JOIN advertiser_levels al ON al.id = ci.customer_level
						  WHERE ";
			  if($this->view != 'all') {
				$sql_query .= "ac.category_id = ? AND ";
				$sql_values[] = $this->category;
			  }
			  $sql_query .= "ci.zip IN (".$this->zip_string.") 
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
		   
			  $stmt = $dbh->prepare($sql_query);					 
			  $result = $stmt->execute($sql_values);
		  
			  // set session list array
			  while($cur_row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
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
								  advertiser_alt_locations aal INNER JOIN advertiser_info ci ON aal.advertiser_id = ci.id LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id INNER JOIN advertiser_levels al ON al.id = ci.customer_level
							  WHERE
							  ac.category_id = ? 
							  AND ci.zip IN (".$this->zip_string.") 
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
					   
				  $stmt = $dbh->prepare($sql_query);					 
				  $result = $stmt->execute($sql_values);
	  
				  // pull category info
				  $cats_tbl->get_db_vars($this->category);
			  
				  while($cur_row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
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

		// check for parent or subcategory
		$cats_tbl->get_db_vars($this->category);
	
		// set category val
		$cat_val = ($this->category > 0 ? $this->category : $this->view);

		if (!empty($cat_val)) {
			
		  $list_cat_array = array();
		  // get primary category list
		  $sql_values = array();
		  $sql_query = "SELECT
						  DISTINCT id
					   FROM
						  businesses
					  WHERE
					  zip IN (".$this->zip_string.") ";
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
				$search_query[] = "(name LIKE ? OR description LIKE ?)";
			}
			$sql_query .= " AND ".implode(" AND ",$search_query);
		   }
		   $sql_query .= " ORDER BY id LIMIT 60;";
			  
		  $stmt = $dbh->prepare($sql_query);					 
		  $result = $stmt->execute($sql_values);
		  
		  // set session list array
		  while($cur_row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
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
	
	public function print_kml() {
		
		$this->get_category_adverts();
		$this->get_category_adverts_alt_loc();

		if (count($this->list_row) == 0) {
		  $this->get_noncert_adverts();
		}

	}

	private function draw_list_row() {
		global $adv_alt_loc_tbl, $adv_info_tbl;
		
		// pull advertiser info
		$adv_info_tbl->get_db_vars($this->advertisers_id);
		$location_image = 'customers/'.urlencode($adv_info_tbl->image);
	
		// generate kml item
		if ($adv_info_tbl->hide_address != 1 && $adv_info_tbl->latitude > 0) {
		  // kml values
		  $values = array();
		  $values['item_name'] = htmlentities(preg_replace('/[^a-zA-Z0-9]/', ' ', $adv_info_tbl->company_name));
		  $values['item_address'] = $adv_info_tbl->address_1.', '.($adv_info_tbl->address_2 != '' ? $adv_info_tbl->address_2.', ' : '').$adv_info_tbl->city.', '.$adv_info_tbl->state.', '.$adv_info_tbl->zip;
		  $values['item_styleurl'] = '#'.$this->assign_map_markers();
		  $values['item_coordinates'] = $adv_info_tbl->longitude.','.$adv_info_tbl->latitude;
		  $values['item_desc'] = '<a href="'.SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/"><img border="0" src="'.SITE_URL.'includes/resize_image.deal?image='.$location_image.'&new_width=55&new_height=35" alt="' . $adv_info_tbl->company_name . '" /></a><br/>
						'.$adv_info_tbl->address_1.'<br/>
						  '.($adv_info_tbl->address_2 != '' ? $adv_info_tbl->address_2.'<br/>' : '').'
						  '.$adv_info_tbl->city.', '.$adv_info_tbl->state.' '.$adv_info_tbl->zip.'<br/>
						  '.$adv_info_tbl->phone_number.'<br/><br/>
						'.preg_replace('/[^a-zA-Z0-9]/', ' ',$adv_info_tbl->products_services).'<br/><br/>
						<a href="http://maps.google.com/maps?f=d&source=s_d&saddr=&daddr='.str_replace(" ","+",$adv_info_tbl->address_1).',+'.str_replace(" ","+",$adv_info_tbl->city).',+'.$adv_info_tbl->state.'+'.$adv_info_tbl->zip.'&hl=en&geocode=&mra=ls&sll='.$adv_info_tbl->latitude.','.$adv_info_tbl->longitude.'&sspn=27.146599,63.28125&ie=UTF8&z=16">Get Directions</a>';
		
		  $this->gen_kml_item($values);
		}
		
	}
	
	private function draw_alt_list_row() {
		global $adv_alt_loc_tbl, $adv_info_tbl;
		
		// pull advertiser info
		$adv_alt_loc_tbl->get_db_vars($this->advert_alt_loc_id);
		$adv_info_tbl->get_db_vars($$adv_alt_loc_tbl->advertiser_id);
		$location_image = 'customers/'.urlencode($adv_info_tbl->image);
	
		// generate kml item
		if ($adv_info_tbl->hide_address != 1 && $adv_alt_loc_tbl->latitude > 0) {
		  // kml values
		  $values = array();
		  $values['item_name'] = htmlentities(preg_replace('/[^a-zA-Z0-9]/', ' ', $adv_info_tbl->company_name));
		  $values['item_address'] = $adv_alt_loc_tbl->address_1.', '.($adv_alt_loc_tbl->address_2 != '' ? $adv_alt_loc_tbl->address_2.', ' : '').$adv_alt_loc_tbl->city.', '.$adv_alt_loc_tbl->state.', '.$adv_alt_loc_tbl->zip;
		  $values['item_styleurl'] = '#'.$this->assign_map_markers();
		  $values['item_coordinates'] = $adv_alt_loc_tbl->longitude.','.$adv_alt_loc_tbl->latitude;
		  $values['item_desc'] = '<a href="'.SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/'.$adv_alt_loc_tbl->id.'/"><img border="0" src="'.SITE_URL.'includes/resize_image.deal?image='.$location_image.'&new_width=55&new_height=35" alt="' . $adv_info_tbl->company_name . '" /></a><br/>
						'.$adv_alt_loc_tbl->address_1.'<br/>
						  '.($adv_alt_loc_tbl->address_2 != '' ? $adv_alt_loc_tbl->address_2.'<br/>' : '').'
						  '.$adv_alt_loc_tbl->city.', '.$adv_alt_loc_tbl->state.' '.$adv_alt_loc_tbl->zip.'<br/>
						  '.$adv_alt_loc_tbl->phone_number.'<br/><br/>
						'.preg_replace('/[^a-zA-Z0-9]/', ' ',$adv_alt_loc_tbl->products_services).'<br/><br/>
						<a href="http://maps.google.com/maps?f=d&source=s_d&saddr=&daddr='.str_replace(" ","+",$adv_alt_loc_tbl->address_1).',+'.str_replace(" ","+",$adv_alt_loc_tbl->city).',+'.$adv_alt_loc_tbl->state.'+'.$adv_alt_loc_tbl->zip.'&hl=en&geocode=&mra=ls&sll='.$adv_alt_loc_tbl->latitude.','.$adv_alt_loc_tbl->longitude.'&sspn=27.146599,63.28125&ie=UTF8&z=16">Get Directions</a>';
		
		  $this->gen_kml_item($values);
		}
		
	}
	
	private function draw_noncert_list_row() {
		global $bus_tbl;
		
		// pull advertiser info
		$bus_tbl->get_db_vars($this->advert_noncert_id);
		$location_image = '';
  
		// generate kml item
		if ($bus_tbl->latitude > 0) {
		  // kml values
		  $values = array();
		  $values['item_name'] = htmlentities(preg_replace('/[^a-zA-Z0-9]/', ' ', $bus_tbl->name));
		  $values['item_address'] = $bus_tbl->address.', '.$bus_tbl->city.', '.$bus_tbl->state.', '.$bus_tbl->zip;
		  $values['item_styleurl'] = '#'.$this->assign_map_markers();
		  $values['item_coordinates'] = $bus_tbl->longitude.','.$bus_tbl->latitude;
		  $values['item_desc'] = '<a href="'.$bus_tbl->url.'"><img border="0" src="'.SITE_URL.'includes/resize_image.deal?image='.$location_image.'&new_width=55&new_height=35" alt="' . $bus_tbl->name . '" /></a><br/>
						'.$bus_tbl->address.'<br/>
						  '.$bus_tbl->city.', '.$bus_tbl->state.' '.$bus_tbl->zip.'<br/>
						  '.$bus_tbl->phone.'<br/><br/>
						'.preg_replace('/[^a-zA-Z0-9]/', ' ',$bus_tbl->description).'<br/><br/>
						<a href="http://maps.google.com/maps?f=d&source=s_d&saddr=&daddr='.str_replace(" ","+",$bus_tbl->address).',+'.str_replace(" ","+",$bus_tbl->city).',+'.$bus_tbl->state.'+'.$bus_tbl->zip.'&hl=en&geocode=&mra=ls&sll='.$bus_tbl->latitude.','.$bus_tbl->longitude.'&sspn=27.146599,63.28125&ie=UTF8&z=16">Get Directions</a>';
		
		  $this->gen_kml_item($values);
		}
	  
	}
	
	private function gen_kml_item($values) {
  
	  $this->list_row[] = '	<Placemark>
			<name>'.$values['item_name'].'</name>
			<address>'.$values['item_address'].'</address>
			<description>
			  <![CDATA[
				'.$values['item_desc'].'
			  ]]>
			</description>
			<styleUrl>'.$values['item_styleurl'].'</styleUrl>
			<Point>
			  <coordinates>'.$values['item_coordinates'].',0</coordinates>
			</Point>
		  </Placemark>'.LB;
  
	}
	
	private function assign_map_markers() {
		global $dbh, $cats_tbl, $adv_info_tbl, $adv_alt_loc_tbl;
		
		if ($this->category != '') {
		
		  $cats_tbl->get_db_vars($this->category);
		
		  if($cats_tbl->map_marker != '') {
			  if($adv_alt_loc_tbl->id != '') {
				$marker = 'pntr_alt_loc_'.$adv_alt_loc_tbl->id;
				$new_mapmark = '<Style id="'.$marker.'">
					  <IconStyle>
						<Icon>
						  <href>'.SITE_URL.'images/category/'.$cats_tbl->map_marker.'</href>
						</Icon>
					  </IconStyle>
					</Style>'.LB;
			  } else {
				$marker = 'pntr_cat_id_'.$adv_info_tbl->id;
				$new_mapmark = '<Style id="'.$marker.'">
					  <IconStyle>
						<Icon>
						  <href>'.SITE_URL.'images/category/'.$cats_tbl->map_marker.'</href>
						</Icon>
					  </IconStyle>
					</Style>'.LB;
			  }
		  } else {
			  if($adv_alt_loc_tbl->id != '') {
				$marker = 'pntr_alt_loc_'.$adv_alt_loc_tbl->id;
				$new_mapmark = '<Style id="'.$marker.'">
					  <IconStyle>
						<Icon>
						  <href>http://www.google.com/intl/en_us/mapfiles/ms/icons/blue-dot.png</href>
						</Icon>
					  </IconStyle>
					</Style>'.LB;
			  } else {
				$marker = 'pntr_cat_id_'.$adv_info_tbl->id;
				$new_mapmark = '<Style id="'.$marker.'">
					  <IconStyle>
						<Icon>
						  <href>http://www.google.com/intl/en_us/mapfiles/ms/icons/blue-dot.png</href>
						</Icon>
					  </IconStyle>
					</Style>'.LB;
			  }
		  }
		
		} else {
			$sql_query = "SELECT
							category_id
						 FROM
							advertiser_categories
						 WHERE
							advertiser_id = ?
						LIMIT 1;";
			
			$values2 = array(
							$adv_info_tbl->id,
							);
	
			$stmt2 = $dbh->prepare($sql_query);					 
			$result2 = $stmt2->execute($values2);
			
			$rows_check = $result2->fetchRow(MDB2_FETCHMODE_ASSOC);
			if($rows_check['category_id'] != '') {
				$cats_tbl->get_db_vars($rows_check['category_id']);
				
				if($cats_tbl->map_marker != '') {
					if($adv_alt_loc_tbl->id != '') {
					  $marker = 'pntr_alt_loc_'.$adv_alt_loc_tbl->id;
					  $new_mapmark = '<Style id="'.$marker.'">
							<IconStyle>
							  <Icon>
								<href>'.SITE_URL.'images/category/'.$cats_tbl->map_marker.'</href>
							  </Icon>
							</IconStyle>
						  </Style>'.LB;
					} else {
					  $marker = 'pntr_cat_id_'.$adv_info_tbl->id;
					  $new_mapmark = '<Style id="'.$marker.'">
							<IconStyle>
							  <Icon>
								<href>'.SITE_URL.'images/category/'.$cats_tbl->map_marker.'</href>
							  </Icon>
							</IconStyle>
						  </Style>'.LB;
					}
				} else {
					if($adv_alt_loc_tbl->id != '') {
					  $marker = 'pntr_alt_loc_'.$adv_alt_loc_tbl->id;
					  $new_mapmark = '<Style id="'.$marker.'">
							<IconStyle>
							  <Icon>
								<href>http://www.google.com/intl/en_us/mapfiles/ms/icons/blue-dot.png</href>
							  </Icon>
							</IconStyle>
						  </Style>'.LB;
					} else {
					  $marker = 'pntr_cat_id_'.$adv_info_tbl->id;
					  $new_mapmark = '<Style id="'.$marker.'">
							<IconStyle>
							  <Icon>
								<href>http://www.google.com/intl/en_us/mapfiles/ms/icons/blue-dot.png</href>
							  </Icon>
							</IconStyle>
						  </Style>'.LB;
					}
				}
			} else {
			  if($adv_alt_loc_tbl->id != '') {
				$marker = 'pntr_alt_loc_'.$adv_alt_loc_tbl->id;
				$new_mapmark = '<Style id="'.$marker.'">
					  <IconStyle>
						<Icon>
						  <href>http://www.google.com/intl/en_us/mapfiles/ms/icons/blue-dot.png</href>
						</Icon>
					  </IconStyle>
					</Style>'.LB;
			  } else {
				$marker = 'pntr_cat_id_'.$adv_info_tbl->id;
				$new_mapmark = '<Style id="'.$marker.'">
					  <IconStyle>
						<Icon>
						  <href>http://www.google.com/intl/en_us/mapfiles/ms/icons/blue-dot.png</href>
						</Icon>
					  </IconStyle>
					</Style>'.LB;
			}
		}
		}
		
		$this->mapmarkers[] = $new_mapmark;
	
	return $marker;
	}

}

?>