<?PHP

// this document generates API XML file
class api_xml_gen {
	private $category, $zip, $radius, $view, $city, $state, $advert_alt_loc_id, $advertisers_id, $zip_string, $limit;
	public $advert_noncert_id, $list_row = array(), $mapmarkers = array();

	public function __construct() {
		global $geo_data, $cities_tbl, $zip_cds_tbl, $api_load;
			
		$this->radius = DEF_MIN_RADIUS;
		$this->view = 'all';
		$this->city = $_GET['city'];
		$this->state = $_GET['state'];
		
		if(!empty($_GET['limit'])){
			$this->limit = $_GET['limit'];
		} else {
			$this->limit = 10;
		}
		
		if($api_load->status == 1) {
		  $cities_tbl->city_state_search($api_load->city,$api_load->state);
		  $_GET['city'] = $cities_tbl->id;
		}

		// if city value is set assign city and state values
		if(!empty($_GET['city'])) {
			// set city values
			$cities_tbl->get_db_vars($_GET['city']);
			$this->city = $cities_tbl->city;
			$this->state = $cities_tbl->state;
		} else {
		  $cities_tbl->get_db_vars($geo_data->cityid);
		  $this->city = $cities_tbl->city;
		  $this->state = $cities_tbl->state;
		}
			
		// build zip codes array
		$zip_cds_tbl->city_id = $cities_tbl->id;
		$zip_array = $zip_cds_tbl->get_list();
		$zip_array = $zip_cds_tbl->fetchZipsInRadiusByZip($zip_array[0], $this->radius, 100 );
		$this->zip_string = implode(', ',$zip_array);
	}
	
	// creates list of advertisers to be displayed within the category listing
	private function get_category_adverts() {
		global $dbh, $zip_cds_tbl, $url_nms_tbl, $adv_info_tbl, $cities_tbl, $ste_cty_cat_tbl, $api_load;		
			
		  $list_cat_array = array();
		  $sql_values = array();
		  // get primary category list
		  $sql_query = "SELECT
						  ci.id as advertisers_id
					  FROM
						  advertiser_info ci LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id INNER JOIN advertiser_levels al ON al.id = ci.customer_level
					  WHERE ";
		  if($api_load->show_all != 1){
			$sql_query .= "ci.zip IN (".$this->zip_string.") AND ";
		  }
			$sql_query .= " ci.account_enabled = 1 
					  AND ci.approved = 1 
					  AND ci.update_approval = 1
					  ORDER BY RAND() ";
		  if($api_load->show_all != 1){					  
			$sql_query .= " LIMIT ".$this->limit.";";
		  }
		  
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
		  					
		// clear existing listing array values
		$this->list_row = array();
		$this->paid_list_row = array();
				
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
	
	}
	
	// added 9-21-2009 retrives alternate locations for listing
	private function get_category_adverts_alt_loc() {
	  global $dbh, $adv_alt_loc_tbl, $zip_cds_tbl, $url_nms_tbl, $adv_info_tbl, $cities_tbl, $ste_cty_cat_tbl, $api_load;		
	
	  // if selected category is a sub category display normal listing		
	  // set category val
			  
		$list_cat_array = array();
		// get primary category list
		$sql_values = array();
		$sql_query = "SELECT
						DISTINCT aal.id AS alt_id
					FROM
						advertiser_alt_locations aal INNER JOIN advertiser_info ci ON aal.advertiser_id = ci.id LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id INNER JOIN advertiser_levels al ON al.id = ci.customer_level
					WHERE ";
		if($api_load->show_all != 1){
			$sql_query .= "	ci.zip IN (".$this->zip_string.") AND ";
		}
		$sql_query .= "	aal.enabled = 1 
						AND ci.account_enabled = 1 
						AND ci.approved = 1 
						AND ci.update_approval = 1
					ORDER BY aal.id, RAND() ";
					
		if($api_load->show_all != 1){
			$sql_query .= " LIMIT ".$this->limit.";";
		}
	 
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
			
		  $list_cat_array = array();
		  // get primary category list
		  $sql_values = array();
		  $sql_query = "SELECT
						  DISTINCT id
					  FROM
						  businesses
					  WHERE
						zip IN (".$this->zip_string.") 
					  ORDER BY id, RAND()
		   			  LIMIT ".$this->limit.";";
			  
		  $stmt = $dbh->prepare($sql_query);					 
		  $result = $stmt->execute($sql_values);
		  
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
	
	public function print_kml() {
		global $api_load;
		
		$this->get_category_adverts();
		$this->get_category_adverts_alt_loc();

		if (count($this->list_row) == 0 && $api_load->show_all != 1) {
		  $this->get_noncert_adverts();
		}

	}

	// added 9-21-2009 assigns listing row advertiser image
	private function assign_list_row_advert_img() {
		global $adv_info_tbl;
				
		// loads advertiser image
		if(!empty($adv_info_tbl->image)) {
			$location_image = CONNECTION_TYPE.'includes/resize_image.deal?image='.urlencode('customers/'.$adv_info_tbl->image).'&amp;new_width='.LISTING_IMAGE_WIDTH.'&amp;new_height='.LISTING_IMAGE_HEIGHT;
		} else {
			$location_image = '';
		}

	return $location_image;
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
		  $values['id'] = $adv_info_tbl->id;
		  $values['durl'] = SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/';
		  $values['item_name'] = $adv_info_tbl->company_name;
		  $values['item_image'] = $this->assign_list_row_advert_img();
		  $values['item_reqs'] = $this->bld_val_reqs();
		  $values['item_address'] = $adv_info_tbl->address_1.', '.($adv_info_tbl->address_2 != '' ? $adv_info_tbl->address_2.', ' : '').$adv_info_tbl->city.', '.$adv_info_tbl->state.', '.$adv_info_tbl->zip;
		  $values['item_desc'] = '<a href="'.SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/">'.$adv_info_tbl->company_name.'</a><br/>
						'.preg_replace('/[^a-zA-Z0-9]/', ' ',$adv_info_tbl->products_services);
		
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
		  $values['id'] = $adv_info_tbl->id;
		  $values['altid'] = $adv_alt_loc_tbl->id;
		  $values['durl'] = SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/'.$adv_alt_loc_tbl->id.'/';
		  $values['item_name'] = $adv_info_tbl->company_name;
		  $values['item_image'] = $this->assign_list_row_advert_img();
		  $values['item_reqs'] = $this->bld_val_reqs();
		  $values['item_address'] = $adv_alt_loc_tbl->address_1.', '.($adv_alt_loc_tbl->address_2 != '' ? $adv_alt_loc_tbl->address_2.', ' : '').$adv_alt_loc_tbl->city.', '.$adv_alt_loc_tbl->state.', '.$adv_alt_loc_tbl->zip;
		  $values['item_desc'] = '<a href="'.SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/'.$adv_alt_loc_tbl->id.'/">'.$adv_info_tbl->company_name.'</a><br/>
						'.preg_replace('/[^a-zA-Z0-9]/', ' ',$adv_alt_loc_tbl->products_services);
		
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
		  $values['id'] = $bus_tbl->id;
		  $values['item_name'] = $bus_tbl->name;
		  $values['item_image'] = '';
		  $values['item_address'] = $bus_tbl->address.', '.$bus_tbl->city.', '.$bus_tbl->state.', '.$bus_tbl->zip;
		  $values['item_desc'] = ($bus_tbl->url != '' ? '<a href="'.$bus_tbl->url.'">' : '').$bus_tbl->name.($bus_tbl->url != '' ? '</a>' : '').'<br/>
						'.preg_replace('/[^a-zA-Z0-9]/', ' ',$bus_tbl->description).'<br/><br/>
						<a href="http://maps.google.com/maps?f=d&source=s_d&saddr=&daddr='.str_replace(" ","+",$bus_tbl->address).',+'.str_replace(" ","+",$bus_tbl->city).',+'.$bus_tbl->state.'+'.$bus_tbl->zip.'&hl=en&geocode=&mra=ls&sll='.$bus_tbl->latitude.','.$bus_tbl->longitude.'&sspn=27.146599,63.28125&ie=UTF8&z=16">Get Directions</a>';
		
		  $this->gen_kml_item($values);
		}
	  
	}
	
	// build coupon value drop down
	private function bld_val_reqs() {
		global $cert_amt_tbl, $adv_info_tbl, $adv_alt_loc_tbl, $cats_tbl;
				
		$cert_amounts = $cert_amt_tbl->get_certificate_amounts();
			
		$req_val_fin .= '';
		foreach($cert_amounts as $cur_cert_amt) {
			if (!empty($adv_info_tbl->certificate_levels[$cur_cert_amt['id']])) {
				if ($adv_info_tbl->certificate_levels[$cur_cert_amt['id']] == 1) {
					if(is_numeric($cur_cert_amt['cost'])) {
						if($cur_cert_amt['cost'] > 0) {
						  $cost_str = $cur_cert_amt['cost'];
						} else {
						  $cost_str = 'FREE!';
						}
					} else {
						$cost_str = $adv_info_tbl->certificate_requirements[$cur_cert_amt['id']]['blank_cost'];
					}
					if(is_numeric($cur_cert_amt['discount_amount'])) {
						$disc_amt = $cur_cert_amt['discount_amount'];
					} else {
						$disc_amt = $adv_info_tbl->certificate_requirements[$cur_cert_amt['id']]['blank_val'];
					}
					$cost_str = ($cur_cert_amt['cost'] > 0 ? 'You Pay $'.$cur_cert_amt['cost'] : 'FREE!');
					$req_val_fin .= '<value>$'.round($disc_amt,2).' Gift Certificate ('.$cost_str.')</value>'.LB;
					$req_val_fin .= '<valuedec>
										<discamt>'.round($disc_amt,2).'</discamt>
										<cost>'.$cur_cert_amt['cost'].'</cost>
										<requirement>
										<![CDATA[ 
										  '.set_cert_agreement_str($adv_info_tbl->certificate_requirements[$cur_cert_amt['id']]['type'],$adv_info_tbl->certificate_requirements[$cur_cert_amt['id']]['value']).'
										]]>
										</requirement>
									</valuedec>'.LB;
				}
			}
		}
				
		// prints selected certificate requirements
		if(is_array($adv_info_tbl->certificate_requirements)) {
			$i = 0;
			foreach($adv_info_tbl->certificate_requirements as $id => $value) {
								
				// set cert req string
				$req_val = htmlentities(set_cert_agreement_str($value['type'],$value['value']));
				
				if (!empty($value['excludes'])) {
					$req_val .= '<br/>Excludes: '.htmlentities($value['excludes']);
				}
				
				$cert_amt_tbl->get_db_vars($value['type']);
				
				$req_val_fin .= '<requirement>'.str_replace(chr(10)," ",str_replace(array('"',chr(13)),array('\"'," "),htmlspecialchars($req_val))).'</requirement>'.LB; 
			}
		}
					
	return $req_val_fin;
	}
	
	private function gen_kml_item($values) {
  
	  $this->list_row[] = "<Placemark>".LB.
			"<id>".htmlentities($values['id'])."</id>".LB.
			"<altid>".htmlentities($values['altid'])."</altid>".LB.
			"<durl>".htmlentities($values['durl'])."</durl>".LB.
			"<name>".htmlentities($values['item_name'])."</name>".LB.
			"<logo>".htmlentities($values['item_image'])."</logo>".LB.
			"<address>".htmlentities($values['item_address'])."</address>".LB.
			"<description>".LB.
			" <![CDATA[ ".LB.
				preg_replace('/\>\s+\</', '> <', $values['item_desc']).LB.
			" ]]>".LB.
			"</description>".LB.
			$values['item_reqs'].
		  "</Placemark>".LB;
  
	}
	
}

?>