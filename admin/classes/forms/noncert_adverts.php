<?PHP

class noncert_adverts_frm {
  
  function delete() {
	global $dbh;
	if(is_array($_POST['delete_noncertadvertiser'])) {
	  foreach($_POST['delete_noncertadvertiser'] as $cur_delete_noncertadvertiser) {
		$stmt = $dbh->prepare("DELETE FROM businesses WHERE id = '".$cur_delete_noncertadvertiser."';");
		$stmt->execute();
	  }
	}
  }

// load add retail customers page
  function add($message = '') {
	$add_noncert_adverts = open_table_form('Add New Non-Certificate Advertiser','add_noncert_adverts',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=editnoncertaddcheck','post',$message);
	$add_noncert_adverts .= $this->form();
	$add_noncert_adverts .= close_table_form();
  return $add_noncert_adverts;
  }

  // load add retail customers page
  function edit($message = '') {
	$add_noncert_adverts = open_table_form('Edit Non-Certificate Advertiser','edit_noncert_adverts',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=editnoncerteditcheck','post',$message);
	$add_noncert_adverts .= $this->form();
	$add_noncert_adverts .= close_table_form();
  return $add_noncert_adverts;
  }
  
  // draw retail customers form
  function form() {
	global $dbh, $bus_tbl;
	
	$noncert_adverts_form = table_form_header('* indicates required field');
	
	$noncert_adverts_form .= table_form_field('<span class="required">*Name:</span>','<input name="name" type="text" size="30" maxlength="50" value="'.$bus_tbl->name.'">');
	$noncert_adverts_form .= table_form_field('<span class="required">Address:</span>','<input name="address" type="text" size="50" maxlength="50" value="'.$bus_tbl->address.'">');
	$noncert_adverts_form .= table_form_field('<span class="required">*City:</span>','<input name="city" type="text" size="50" maxlength="100" value="'.$bus_tbl->city.'">');
				
	$noncert_adverts_form .= table_form_field('<span class="required">*State:</span>','<select name="state">'.gen_state_dd($bus_tbl->state).'</select>');
	$noncert_adverts_form .= table_form_field('<span class="required">*Zip:</span>','<input name="zip" type="text" size="15" maxlength="15" value="'.$bus_tbl->zip.'">');
	$noncert_adverts_form .= table_form_field('Phone:','<input name="phone" type="text" size="15" maxlength="15" value="'.$bus_tbl->phone.'">');
	$noncert_adverts_form .= table_form_field('Email:','<input name="email" type="text" size="50" maxlength="160" value="'.$bus_tbl->email.'">');
	$noncert_adverts_form .= table_form_field('URL:','<input name="url" type="text" size="50" maxlength="120" value="'.$bus_tbl->url.'">');
	$noncert_adverts_form .= table_form_field('*Description:','<input name="description" type="text" size="50" maxlength="200" value="'.$bus_tbl->description.'">');

	$noncert_adverts_form .= table_span_form_field('<center><input name="id" type="hidden" value="'.$bus_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
	
  return $noncert_adverts_form;
  }
	  
  // check form submission values
  function form_check() {
	global $bus_tbl;
	
	// required fields array
	$required_fields = array(
							'Name' => $bus_tbl->name,
							'Address' => $bus_tbl->address,
							'City' => $bus_tbl->city,
							'State' => $bus_tbl->state,
							'Zip' => $bus_tbl->zip,
							'Phone' => $bus_tbl->phone,
							);
		
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