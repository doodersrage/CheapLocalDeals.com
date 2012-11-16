<?PHP

// location info page class and functions
class location_info_pg {
	public $bc, $zip, $category, $loc_id, $alt_loc_id, $cert_id, $default_requirement;

	// build initial variable list
	public function __construct() {
		global $adv_info_tbl, $adv_alt_loc_tbl, $api_load;
			
		// set location values
		$this->loc_id = (int)$_GET['aid'];
		$adv_info_tbl->get_db_vars($this->loc_id);
		if(isset($_GET['altadid'])) {
		  $this->alt_loc_id = (int)$_GET['altadid'];
		  $adv_alt_loc_tbl->get_db_vars($this->alt_loc_id);
		}
		
		// if advertiser is not approved do not show page
		if($adv_info_tbl->approved == 1 && $adv_info_tbl->update_approval == 1 && $adv_info_tbl->account_enabled == 1) {
		// do nothing 
		} else {
			// clear location info
			$adv_info_tbl->reset_vars();
		}
		$this->zip = $adv_info_tbl->zip;
				
	}

	// build coupon value drop down
	function build_value_drop_down() {
			global $cert_amt_tbl, $adv_info_tbl;
				
		$cert_amounts = $cert_amt_tbl->get_certificate_amounts();
		
		$this->default_requirement = '';
		
		// prints selected certificate requirements
		$value_drop_down = '<script type="text/javascript">'.LB; 
		$value_drop_down .= '//<![CDATA[
							var curArray = [];'.LB; 
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

			$value_drop_down .= 'curArray['.$id.'] = "'.str_replace(chr(10)," ",str_replace(array('"',chr(13)),array('\"'," "),$requirement_value)).'";'.LB; 
			if ($i == 1) $this->default_requirement = $requirement_value;
		}
		$value_drop_down .= 'requirementsArray['.$adv_info_tbl->id.'] = curArray;'.LB; 
		$value_drop_down .= '//]]>
							</script>'.LB; 

		// draws level drop down		
		$value_drop_down .= '<select class="cert_sel_dd" size="1" id="cert_'.$adv_info_tbl->id.'" name="certificate_amount_id" style="width:220px;" onchange="update_requirements('.$adv_info_tbl->id.')">'.LB;
		
		foreach($cert_amounts as $cur_cert_amt) {
			if(!empty($adv_info_tbl->certificate_levels[$cur_cert_amt['id']])) {
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
		
		$value_drop_down .= '</select>'.LB; 
		
	return $value_drop_down;
	}

	// build quantity drop down
	public function build_quantity_drop_down() {
		
		$quantity_drop_down = '<select class="cert_sel_dd" size="1" name="quantity">'.LB;
		
			for($i = 1;$i <= ADD_TO_CART_QUANTITY_DROP_DOWN; $i++) {
				$quantity_drop_down .= '<option>'.$i.'</option>'.LB;
			}
			
		$quantity_drop_down .= '</select>'.LB;
			
	return $quantity_drop_down;
	}

	// build certificate area
	public function build_certificate_form() {
		global $adv_info_tbl, $adv_info_tbl;

		  $cert_req_arr = $adv_info_tbl->certificate_requirements;
	
		  if (!empty($cert_req_arr)) {
			  
			  $location_info_form = draw_mob_cert_form($this->build_value_drop_down(),$adv_info_tbl->id,$this->default_requirement,$this->build_quantity_drop_down(),'review_'.$adv_info_tbl->id);
			  
			}
	return $location_info_form;
	}
	
	public function payment_methods_display() {
		global $adv_info_tbl, $adv_pmt_mtds_tbl;

		if (count($adv_info_tbl->payment_options) > 0) {
		$payment_meth = '<p><b>Payment Methods:</b></p>
		';
		// build payment method options
		$payment_methods = $adv_pmt_mtds_tbl->get_all();
			$payment_method_sel = '';
			$payment_method_sel_op = '<table class="payment_methods">';
			foreach($payment_methods as $id => $value) {
				if (!empty($adv_info_tbl->payment_options[$value['id']])) {
					if ($adv_info_tbl->payment_options[$value['id']] == 1) {
						if(!empty($value['image'])){
						  // added to set image height and width settings
						  $image_location = CONNECTION_TYPE.'includes/resize_image.deal?image='.urlencode('payment_logos/'.$value['image']).'&amp;new_width=60&amp;new_height=60';
						  $payment_method_sel[] = '<td><img src="'.$image_location.'" alt="' . htmlentities($value['method']) . ' payment method image" /></td>';
						} else {
						  $payment_method_sel[] = '<td>'.$value['method'].'</td>';
						}
						if (count($payment_method_sel) == 3) {
							$payment_method_sel_op .= '<tr>'.implode('',$payment_method_sel).'</tr>';
							$payment_method_sel = '';
						}
					}
				}
			}
			if ($payment_method_sel != '') {
				$payment_method_sel_op .= '<tr>'.implode('',$payment_method_sel).'</tr>';
				$payment_method_sel = '';
			}
			$payment_method_sel_op .= '<tr><td>&nbsp;</td></tr>';
			$payment_method_sel_op .= '</table>';
			$payment_meth .= $payment_method_sel_op.'';
		}

	return $payment_meth;
	}
	
	// get advertisers hours of operation
	public function print_hours_of_operation() {
		global $adv_info_tbl;

		// if HOP values are set load values into form
		$hours_operation = $adv_info_tbl->hours_operation;

		if ($hours_operation['selected']['type'] != 'nohours' && $hours_operation['selected']['type'] != '') {
		 
	    $hour_opp = '<center><b>Hours of Operation:</b></center><br/>';

		switch($hours_operation['selected']['type']) {
		case '24hr':
		  $hour_opp .= 'Open 24 hours';
		break;
		case 'select':
		  $days_array = unserialize(DAYS_ARRAY);
		  $hours_of_operation = '<table class="hours_operation_tbl" align="center">';
		  reset($days_array);
		  foreach($days_array as $day_value) {
			  $hours_of_operation .= '<tr><th>'.$day_value.'</th>';
			  $hours_of_operation .= '<td valign="top">';
			  if ($hours_operation['selected'][$day_value.'open'] != 'CLOSED' && $hours_operation['selected'][$day_value.'close'] != 'CLOSED') {
				$hours_of_operation .= $hours_operation['selected'][$day_value.'open'].'<br/>';
				$hours_of_operation .= 'to<br/>';
				$hours_of_operation .= $hours_operation['selected'][$day_value.'close'];
			  } else {
				$hours_of_operation .= $hours_operation['selected'][$day_value.'open'];
			  }
			  $hours_of_operation .= '<br/>';
			  $hours_of_operation .= '</td></tr>';
		  }
		  $hours_of_operation .= '</table>';	
		  $hour_opp .= $hours_of_operation;		
		break;
		}
		} 

	return $hour_opp;
	}
	
	// get advertisers image
	public function assign_image() {
		global $adv_info_tbl;

	  if($this->alt_loc_id != '') {
		  $website = $adv_alt_loc_tbl->website;
	  } else {
		  $website = $adv_info_tbl->website;
	  }
	
	  if(!empty($adv_info_tbl->image)) {
		  $advert_image = '<img border="0" src="';
		  $location_image = 'customers/'.$adv_info_tbl->image;
		  $advert_image .= OVERRIDE_SITE_URL.'includes/resize_image.deal?image='.urlencode($location_image).'&amp;new_width=150&amp;new_height=150';
		  $advert_image .= '" alt="'. htmlentities($adv_info_tbl->company_name) .'" />';
	  } else {
		  $advert_image = '<img border="0" src="';
		  $advert_image .= OVERRIDE_SITE_URL.'includes/resize_image.deal?image=&amp;new_width=150&amp;new_height=150';
		  $advert_image .= '" alt="'. htmlentities($adv_info_tbl->company_name) .'" />';
	  }
	  
	  $img_arr = array();
	  if(!empty($website)) {
		$img_arr[] = '<a href="'.(strstr($website,'http') == 0 ? 'http://'.$website : $website ).'" target="_blank" rel="nofollow">';
	  }
	  $img_arr[] = $advert_image;
	  if(!empty($website)) {
		$img_arr[] = '</a>';
	  }
	  
	  $advert_image = implode('',$img_arr);
	  	
	return $advert_image;
	}
	
	// get advertisers address
	public function print_address() {
	  global $adv_info_tbl, $adv_alt_loc_tbl;
		
	  $address_opp = '';
	  if($this->alt_loc_id != '') {
		$address_opp = '<span class="location_info">';
		if ($adv_alt_loc_tbl->hide_address != 1) {
			$address_opp .= '<br/>'.$adv_alt_loc_tbl->address_1.'<br/>'
			.($adv_alt_loc_tbl->address_2 != '' ? $adv_alt_loc_tbl->address_2.'<br/>' : '')
			.$adv_alt_loc_tbl->city.', '.$adv_alt_loc_tbl->state.' '.$adv_alt_loc_tbl->zip.'<br/>
			<p><a target="_blank" href="http://maps.google.com/maps?f=d&amp;source=s_d&amp;saddr=&amp;daddr='.str_replace(" ","+",$adv_alt_loc_tbl->address_1).',+'.str_replace(" ","+",$adv_alt_loc_tbl->city).',+'.$adv_alt_loc_tbl->state.'+'.$adv_alt_loc_tbl->zip.'&amp;hl=en&amp;geocode=&amp;mra=ls&amp;sll='.$adv_alt_loc_tbl->latitude.','.$adv_alt_loc_tbl->longitude.'&amp;sspn=27.146599,63.28125&amp;ie=UTF8&amp;z=16" style="text-decoration: underline; font-weight: bold;">Get Directions</a></p>';
		}
		$address_opp .= ($adv_alt_loc_tbl->email_address != '' ? '<br/>Email: <a href="mailto:'.$adv_alt_loc_tbl->email_address.'">'.$adv_alt_loc_tbl->email_address.'</a>' : '');
		$address_opp .= '<p>Phone: <a href="tel:'.$adv_alt_loc_tbl->phone_number.'">'.$adv_alt_loc_tbl->phone_number.'</a></p></span>';
	  } else {
		$address_opp = '<span class="location_info">';
		if ($adv_info_tbl->hide_address != 1) {
			$address_opp .= '<br/>'.$adv_info_tbl->address_1.'<br/>'
			.($adv_info_tbl->address_2 != '' ? $adv_info_tbl->address_2.'<br/>' : '')
			.$adv_info_tbl->city.', '.$adv_info_tbl->state.' '.$adv_info_tbl->zip.'<br/>
			<p><a target="_blank" href="http://maps.google.com/maps?f=d&amp;source=s_d&amp;saddr=&amp;daddr='.str_replace(" ","+",$adv_info_tbl->address_1).',+'.str_replace(" ","+",$adv_info_tbl->city).',+'.$adv_info_tbl->state.'+'.$adv_info_tbl->zip.'&amp;hl=en&amp;geocode=&amp;mra=ls&amp;sll='.$adv_info_tbl->latitude.','.$adv_info_tbl->longitude.'&amp;sspn=27.146599,63.28125&amp;ie=UTF8&amp;z=16" style="text-decoration: underline; font-weight: bold;">Get Directions</a></p>'; 
		}
		$address_opp .= ($adv_info_tbl->email_address != '' ? '<p>Email: <a href="mailto:'.$adv_info_tbl->email_address.'">'.$adv_info_tbl->email_address.'</a></p>' : '');
		$address_opp .= '<p>Phone: <a href="tel:'.$adv_info_tbl->phone_number.'">'.$adv_info_tbl->phone_number.'</a></p></span>';
	  }

	return $address_opp;
	}
	
	// get advertisers description content
	public function print_description() {
	  global $adv_info_tbl, $adv_alt_loc_tbl, $man_ill_char;

	  if($adv_alt_loc_tbl->customer_description != '') {
		  $descripton = $adv_alt_loc_tbl->customer_description;
	  } else {
		  $descripton = $adv_info_tbl->customer_description;
	  }
	  
//	  // clean description
//	  $man_ill_char->text_to_cln = $descripton;
//	  $man_ill_char->clean_text();
//	  $descripton = $man_ill_char->text_to_cln;

	  $desc_op = '';
	  $cust_description = explode("\n",htmlentities($descripton));
	  foreach($cust_description as $cust_desc_ln) {
		  $cust_desc_ln = trim($cust_desc_ln);
		  if(!empty($cust_desc_ln)) $desc_op .= '<p>'.$cust_desc_ln.'</p>';
	  }

	return $desc_op;
	}
	
	// get products and services content 
	public function print_products_services() {
	  global $adv_info_tbl, $adv_alt_loc_tbl, $man_ill_char;
	  
	  $products_services = '';
	  if($this->alt_loc_id != '') {
		  $products_services = $adv_alt_loc_tbl->products_services;
	  } else {
		  $products_services = $adv_info_tbl->products_services;
	  }
	  
//	  // clean description
//	  $man_ill_char->text_to_cln = $products_services;
//	  $man_ill_char->clean_text();
//	  $products_services = $man_ill_char->text_to_cln;
	  
	  if (!empty($products_services)) {
		$products_services = '<p><b>Products &amp; Services:</b><br/>
		<span class="location_info">'.htmlentities($products_services).'</span></p>'; 
	  }
	
	return $products_services;
	}
	
	// prints the locations website link
	public function print_website_lnk() {
	  global $adv_info_tbl, $adv_alt_loc_tbl;

	  if($this->alt_loc_id != '') {
		  $website = $adv_alt_loc_tbl->website;
	  } else {
		  $website = $adv_info_tbl->website;
	  }
	  
	  $website_lnk = '';
	  if(!empty($adv_info_tbl->website)) {
		  $website_lnk = '<a href="'.(strstr($website,'http') == 0 ? 'http://'.$website : $website ).'" target="_blank" rel="nofollow"><u><b>'.htmlentities($adv_info_tbl->company_name).'</b></u></a>'; 
	  } else {
		  $website_lnk = '<b>'.htmlentities($adv_info_tbl->company_name).'</b>'; 
	  }

	return $website_lnk;
	}
	
	public function get_api_user_info() {
		global $api_load;
		
		$address_opp = '<div class="smallPad textCent boxCent average_review_bx">';
				  
		if(!empty($api_load->image)) {
			$address_opp .= '<br /><div align="center"><img border="0" src="';
			$location_image = 'api_users/'.$api_load->image;
			$address_opp .= OVERRIDE_SITE_URL.'includes/resize_image.deal?image='.urlencode($location_image).'&amp;new_width='.ADVERTISER_PAGE_IMAGE_WIDTH.'&amp;new_height='.ADVERTISER_PAGE_IMAGE_HEIGHT;
			$address_opp .= '" alt="'. $api_load->name .'" /></div>';
		}
  
		if ($api_load->show_address == 1) {
			$address_opp .= '<br/>'.$api_load->address.'<br/>'
			.($api_load->address1 != '' ? $api_load->address1.'<br/>' : '')
			.$api_load->city.', '.$api_load->state.' '.$api_load->zip;
		}
		
		$address_opp .= '</div>';
	return $address_opp;
	}

}

?>