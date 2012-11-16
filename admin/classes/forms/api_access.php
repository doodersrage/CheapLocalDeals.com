<?PHP

// manages and inserts advertiser levels

// customer levels management class
class api_access_frm {
  
  // load add retail customers page
  function add($message = '') {
	$add_api_access = open_table_form('Add New API Access','add_api_access',SITE_ADMIN_SSL_URL.'?sect=apiaccess&mode=newcheck','post',$message);
	$add_api_access .= $this->form();
	$add_api_access .= close_table_form();
  return $add_api_access;
  }
  
  // load add retail customers page
  function edit($message = '') {
	$add_api_access = open_table_form('Edit API Access','edit_api_access',SITE_ADMIN_SSL_URL.'?sect=apiaccess&mode=editcheck','post',$message);
	$add_api_access .= $this->form();
	$add_api_access .= close_table_form();
  return $add_api_access;
  }
  
  // draw retail customers form
  function form() {
	global $dbh, $api_acc_tbl;
	
	$api_access_form = table_form_header('* indicates required field');
	$api_access_form .= table_form_field('Enabled:','<input name="enabled" type="checkbox" value="1"'.($api_acc_tbl->enabled == 1 ? ' checked ' : '').'>');
	$api_access_form .= table_form_field('Show All Listings:','<input name="show_all" type="checkbox" value="1"'.($api_acc_tbl->show_all == 1 ? ' checked ' : '').'>');
	$api_access_form .= table_form_field('<span class="required">*APIKey:</span>','<input name="apikey" type="text" size="30" maxlength="160" value="'.$api_acc_tbl->apikey.'">');
	$api_access_form .= table_form_field('Password: <br/> <strong>< needed to gain access to Aff Track system ></strong> ','<input name="ptpassword" type="text" size="30" maxlength="40" value="'.$api_acc_tbl->ptpassword.'">');
	$api_access_form .= table_form_field('Hide Page Header:','<input name="hide_header" type="checkbox" value="1"'.($api_acc_tbl->hide_header == 1 ? ' checked ' : '').'>');
	$api_access_form .= table_form_field('Hide Page Footer:','<input name="hide_footer" type="checkbox" value="1"'.($api_acc_tbl->hide_footer == 1 ? ' checked ' : '').'>');
	$api_access_form .= table_form_field('<span class="required">*Name:</span>','<input name="name" type="text" size="30" maxlength="160" value="'.$api_acc_tbl->name.'">');
	$api_access_form .= table_form_field('Image:','<input name="image" type="file">'.(!empty($api_acc_tbl->image) ? ' <script type="text/javascript"> 
				jQuery(function(){
				 jQuery("#del_image_lnk").click(function () {
					var api_id = jQuery("#id").val();
					var current_old_image = jQuery("#old_image").val();
					
					 $.ajax({
					   type: "POST",
					   url: "ajax_calls/delete_api_image.deal",
					   data: "api_id="+api_id+"&image="+current_old_image,
					   success: function(msg){
						 jQuery("#del_image_lnk").css("display","none");
						 jQuery("#old_image").val("");
						 jQuery("#image_text").html("Image Deleted");
					   }
					 });
				 });
				}); 
				</script>
				<br><strong>Current Image:</strong> <span id="image_text"><a href="'.SITE_URL.'images/api_users/' . $api_acc_tbl->image . '" target="blank">' . $api_acc_tbl->image . '</a></span><input id="old_image" name="old_image" type="hidden" value="'.$api_acc_tbl->image.'"> <a id="del_image_lnk" href="javascript:void;"><font color="red">Delete</font></a>' : ''));
	$api_access_form .= table_form_field('Website:','<input name="website" type="text" size="30" maxlength="160" value="'.$api_acc_tbl->website.'">');
	$api_access_form .= table_form_field('Show Address:','<input name="show_address" type="checkbox" value="1"'.($api_acc_tbl->show_address == 1 ? ' checked ' : '').'>');
	$api_access_form .= table_form_field('Address:','<input name="address" type="text" size="30" maxlength="160" value="'.$api_acc_tbl->address.'">');
	$api_access_form .= table_form_field('Address 2:','<input name="address1" type="text" size="30" maxlength="160" value="'.$api_acc_tbl->address1.'">');
	$api_access_form .= table_form_field('City:','<input name="city" type="text" size="30" maxlength="100" value="'.$api_acc_tbl->city.'">');

	$api_access_form .= table_form_field('State:','<select name="state">'.gen_state_dd($api_acc_tbl->state).'</select>');
	$api_access_form .= table_form_field('Zip:','<input name="zip" type="text" size="10" maxlength="10" value="'.$api_acc_tbl->zip.'">');
	
	$api_access_form .= table_span_form_field('<center><input id="id" name="id" type="hidden" value="'.$api_acc_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
	  
  return $api_access_form;
  }
	  
  // check form submission values
  function form_check() {
	global $api_acc_tbl;
	
	// required fields array
	$required_fields = array(
							 'Name'=> $api_acc_tbl->name,
							 'APIKey'=> $api_acc_tbl->apikey,
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