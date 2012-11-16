<?PHP

class rand_lst_qry {
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
	private $res_limit = 1;
	
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
		
		// set class variables
		$this->display = (isset($_GET['display']) ? $_GET['display'] : '');
		$this->zip = (!empty($_SESSION['cur_zip']) ? $_SESSION['cur_zip'] : '' );

		// get zip array for listing
		$this->build_zip_array();
		
		// build listing rows
		$this->get_category_adverts();
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
		if(count($this->misc_list_row) > 0) {
		  shuffle($this->misc_list_row);
		}
		// join array pieces
		$this->list_row = array_merge($this->paid_list_row,$this->list_row,$this->misc_list_row);				
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
							  WHERE
								ci.zip IN (".$zip_string.") 
								AND ci.account_enabled = 1 
								AND ci.approved = 1 
								AND ci.update_approval = 1
							  ORDER BY RAND()
							  LIMIT ".$this->res_limit." ;";

				  $stmt = $dbh->prepare($sql_query);					 
				  $result = $stmt->execute();

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
						  LIMIT ".$this->res_limit." ;";
		  
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
			global $cert_amt_tbl, $adv_info_tbl, $adv_alt_loc_tbl;
				
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
		global $adv_info_tbl, $adv_lvls_tbl, $api_load;
		
		// pull advertisers info
		$adv_info_tbl->get_db_vars($this->advertisers_id);		
		
		// pull advertiser level info
		$adv_lvls_tbl->get_db_vars($adv_info_tbl->customer_level);		
				
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
		$values['category_name'] = '';
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
	
	// draws row for non-cert offering advertiser
	private function draw_noncert_list_row() {
		global $bus_tbl;
		
		// get alt location info
		$bus_tbl->get_db_vars($this->advert_noncert_id);
		
		// no links are used for non-cert advertisers
		$page_link = $bus_tbl->url;

		$values['page_link'] = $page_link;
		$values['no_follow'] = 1;
		$values['location_image'] = '';
		$values['company_name'] = htmlentities($bus_tbl->name);
		$values['category_name'] = '';
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
		
	  $cur_list_data = '
						  <table class="advet_box_left"><tr><td align="center" valign="middle" width="190" height="160">
						  <a'.($values['no_follow'] == 1 ? ' rel="nofollow"' : '').' href="'.$values['page_link'].'"><img src="'.$image_location.'" alt="' . htmlentities($values['company_name']) . ' image" /></a>
						  '.$values['category_name'].'
						  </td></tr></table>
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
			  <div class="pop_clear"></div>'.LB;
	  
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
	
	  if(!empty($geo_data->cityid) || !empty($_GET['city'])) {
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