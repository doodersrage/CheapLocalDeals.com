<?PHP

// allows the manual creation of customer assigned gift certificates
class create_certificate_frm {
  
  // load add category page
  function add($message = '') {
	$add_cat = open_table_form('Create New Certificate','create_certificate',SITE_ADMIN_SSL_URL.'?sect=createcertificate&mode=addcheck','post',$message);
	$add_cat .= $this->form();
	$add_cat .= close_table_form();
  return $add_cat;
  }
  
  // draw certificate form
  function form() {
	global $dbh;

	$category_form = table_form_header('* indicates required field');
	
	// build advertisers drop down
	$sql_query = "SELECT
					id,
					state,
					acn
				 FROM
					states
				 ORDER BY
					state
				 ;";
	$rows = $dbh->queryAll($sql_query);
	$advertiser_options .= '<option></option>';
	foreach($rows as $advertiser) {
		$advertiser_options .= '<option value="'.$advertiser['acn'].'" '.($_POST['state_select'] == $advertiser['acn'] ? 'selected="selected"' : '').' >'.$advertiser['state'].'</option>';
	}
	
	$select_ajax = '<script type="text/javascript"> 
					jQuery(function(){
					 jQuery("#state_select").change(function () {
						var selected_state = jQuery("#state_select").val();
						
						 $.ajax({
						   type: "POST",
						   url: "ajax_calls/city_select.deal",
						   data: "state="+selected_state,
						   success: function(msg){
							 jQuery(\'#city_select\').html(msg);
							 jQuery(\'#advertiser_select\').html(\'\');
						   }
						 });
					 });
					}); 
					</script>'.LB;
	
	$category_form .= table_form_field('<span class="required">*State:</span>',$select_ajax.'<select name="state_select" id="state_select">'.$advertiser_options.'</select>');
	
	$category_form .= table_form_field('<span class="required">*City:</span>','<div id="city_select"></div>');

	$category_form .= table_form_field('<span class="required">*Advertiser:</span>','<div id="advertiser_select_bx"></div>');

	// build customers drop down
	$sql_query = "SELECT
					id,
					first_name,
					last_name
				 FROM
					customer_info
				 ORDER BY
				 first_name
				 ;";
	$rows = $dbh->queryAll($sql_query);
	foreach($rows as $customer) {
		$customer_options .= '<option value="'.$customer['id'].'" '.($_POST['customer_select'] == $customer['id'] ? 'selected="selected"' : '').' >'.$customer['first_name'].' '.$customer['last_name'].'</option>';
	}
	$category_form .= table_form_field('<span class="required">*Customer:</span>','<select name="customer_select">'.$customer_options.'</select>');
	
	$category_form .= table_form_field('<span class="required">*Certificate Amount:</span>','<div id="cert_amounts"></div>');
	
	$category_form .= table_form_field('Certificate Requirements:','<textarea name="certificate_requirements" id="certificate_requirements" cols="20" rows="4">'.$_POST['certificate_requirements'].'</textarea>');
		
	$category_form .= table_form_field('Certificate Exclusions:','<textarea name="certificate_excludes" id="certificate_excludes" cols="20" rows="4">'.$_POST['certificate_excludes'].'</textarea>');

	$category_form .= table_span_form_field('<center><input name="certificate_submit" type="hidden" value="1"><input name="submit" type="submit" value="Submit"></center>');
	
  return $category_form;
  }
  
  // check form submission values
  function form_check() {
	global $dbh;
	  
	 // no form requirements
	  
  return $error_message;
  }
  
}

?>