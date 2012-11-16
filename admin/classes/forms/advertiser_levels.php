<?PHP

// manages and inserts advertiser levels

// customer levels management class
class advertiser_levels_frm {
  
  // load add retail customers page
  function add($message = '') {
	$add_advertiser_levels = open_table_form('Add New Customer Level','add_advertiser_levels',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=customerlevelsnewcheck','post',$message);
	$add_advertiser_levels .= $this->form();
	$add_advertiser_levels .= close_table_form();
  return $add_advertiser_levels;
  }
  
  // load add retail customers page
  function edit($message = '') {
	$add_advertiser_levels = open_table_form('Edit Customer Level','edit_advertiser_levels',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=customerlevelseditcheck','post',$message);
	$add_advertiser_levels .= $this->form();
	$add_advertiser_levels .= close_table_form();
  return $add_advertiser_levels;
  }
  
  // draw retail customers form
  function form() {
	global $adv_lvls_tbl;
	
	$advertiser_levels_form = table_form_header('* indicates required field');
	$advertiser_levels_form .= table_form_field('<span class="required">*Level Name:</span>','<input name="level_name" type="text" size="30" maxlength="100" value="'.$adv_lvls_tbl->level_name.'">');
	$advertiser_levels_form .= table_form_field('Level Weight:','<input name="level_weight" type="text" size="2" maxlength="15" value="'.$adv_lvls_tbl->level_weight.'">');
	
	$oFCKeditor = new FCKeditor('level_description') ;
	$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
	$oFCKeditor->Height = 400;
	$oFCKeditor->Value = $adv_lvls_tbl->level_description;
	
	$advertiser_levels_form .= table_form_field('Level Description:',$oFCKeditor->Create());
	
	$advertiser_levels_form .= table_form_field('Level Duration:','<input name="level_duration" type="text" size="3" maxlength="3" value="'.$adv_lvls_tbl->level_duration.'"> - In months');
	$advertiser_levels_form .= table_form_field('Upfront Cost:','<input name="level_upfront_cost" type="text" size="10" maxlength="10" value="'.$adv_lvls_tbl->level_upfront_cost.'">');
	$advertiser_levels_form .= table_form_field('Renewal Cost:','<input name="level_renewal_cost" type="text" size="10" maxlength="10" value="'.$adv_lvls_tbl->level_renewal_cost.'">');
	$advertiser_levels_form .= table_form_field('Upfront Link Back Cost:','<input name="upfront_level_link_back" type="text" size="10" maxlength="10" value="'.$adv_lvls_tbl->upfront_level_link_back.'">');
	$advertiser_levels_form .= table_form_field('Upfront BBB Member Cost:','<input name="upfront_bbb_member_price" type="text" size="10" maxlength="10" value="'.$adv_lvls_tbl->upfront_bbb_member_price.'">');
	$advertiser_levels_form .= table_span_form_field('<center><input name="id" type="hidden" value="'.$adv_lvls_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
	  
  return $advertiser_levels_form;
  }
	  
  // check form submission values
  function form_check() {
	global $adv_lvls_tbl;
	
	// required fields array
	$required_fields = array('Level Name'=> $adv_lvls_tbl->level_name);
		
	// check error values and write error array					
	foreach($required_fields as $field_name => $output) {
	  if (empty($output)) {
		$errors_array[] = $field_name;
	  }
	}
	
	if (!empty($errors_array)) {
	  $error_message = 'You did not supply a value for these fields: ' . implode(', ',$errors_array);
	}
	
  return $error_message;
  }

}

?>