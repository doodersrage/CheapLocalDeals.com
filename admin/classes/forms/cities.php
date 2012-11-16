<?PHP

// display, add, and edit searchable state list

class cities_frm {
  
  // delete cities
  function delete() {
	global $dbh;
	
	foreach($_POST['delete_cities'] as $selected_zips) {
	  $stmt = $dbh->prepare("DELETE FROM cities WHERE id = '".$selected_zips."';");
	  $stmt->execute();
	  $stmt = $dbh->prepare("DELETE FROM url_names WHERE type = 'city' AND parent_id = '".$selected_zips."';");
	  $stmt->execute();
	  // get newly inserted city id
	  $sql_query = "SELECT
					  id
				   FROM
					  state_city_category
				   WHERE city = ?
				   ;";
		  
	  $values = array(
					  $selected_zips
					  );
		  
	  $stmt = $dbh->prepare($sql_query);					 
	  $result = $stmt->execute($values);
  
	  while($cat_cits = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		$stmt = $dbh->prepare("DELETE FROM state_city_category WHERE id = '".$cat_cits['id']."';");
		$stmt->execute();
		$stmt = $dbh->prepare("DELETE FROM url_names WHERE type = 'citiescate' AND parent_id = '".$cat_cits['id']."';");
		$stmt->execute();
	  }
	}
  }

  // load add state page
  function edit($message = '') {
	$add_cities = open_table_form('Edit City','edit_cities',SITE_ADMIN_SSL_URL.'?sect=cities&mode=editcheck','post',$message);
	$add_cities .= $this->form();
	$add_cities .= close_table_form();
  return $add_cities;
  }
  
  // load add state page
  function add($message = '') {
	$add_cities = open_table_form('Add New City','add_cities',SITE_ADMIN_SSL_URL.'?sect=cities&mode=addcheck','post',$message);
	$add_cities .= $this->form();
	$add_cities .= close_table_form();
  return $add_cities;
  }
  
  
  // draw cities edit form
  function form() {
	global $dbh, $cities_tbl, $url_nms_tbl, $city_type;
	
	$cities_form = table_form_header('<script type="text/javascript" src="js/cities.js"></script>* indicates required field');
	
	// build cities drop down	
	foreach($city_type as $id => $cur_city_type) {
		$cities_dd .= '<option value="'.$id.'" '.($id == $cities_tbl->type ? 'selected="selected" ' : '').'>'.$cur_city_type.'</option>';
	}

	$cities_form .= table_form_field('<span class="required">*State:</span>','<select name="state">'.gen_state_dd((!empty($cities_tbl->state) ? $cities_tbl->state : $_SESSION['state_fltr'])).'</select>');
	$cities_form .= table_form_field('<span class="required">*Type:</span>','<select name="type">'.$cities_dd.'</select>');
	$cities_form .= table_form_field('<span class="required">*City Name:</span>','<input name="city" type="text" size="20" maxlength="160" value="'.$cities_tbl->city.'">');
	
	if($cities_tbl->id != ''){
	  $cities_form .= table_form_field('Associated Postal Codes:','
									   <table>
									   <tr><td>Assigned Zips</td><td>Action</td><td>Available Zips</td></tr>
									   <tr>
									   <td><select name="zip_codes" id="zip_codes" size="6">'.gen_zips_sel($cities_tbl->id).'</select></td>
									   <td><input type="button" name="assign_zip" value="<==" onclick="ass_zip()"><br><input type="button" name="remove_zip" value="==>" onclick="rem_zip()"></td>
									   <td><select name="all_zip_codes" id="all_zip_codes" size="6">'.gen_all_zips_sel($cities_tbl->id).'</select></td>
									   </tr>
									   </table>
						  <input name="new_zip" id="new_zip" type="text" size="5" maxlength="10" value=""/><input type="button" name="add" value="Add" onclick="add_zip()" /><input type="button" name="delete" value="Delete" onclick="del_zip()"/>');
	}
	
	$oFCKeditor = new FCKeditor('page_header') ;
	$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
	$oFCKeditor->Height = 400;
	$oFCKeditor->Value = $cities_tbl->page_header;
	
	$cities_form .= table_form_field('Page Header:',$oFCKeditor->Create());
	
	$oFCKeditor = new FCKeditor('page_footer') ;
	$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
	$oFCKeditor->Height = 400;
	$oFCKeditor->Value = $cities_tbl->page_footer;
	
	$cities_form .= table_form_field('Page Footer:',$oFCKeditor->Create());
	
	$cities_form .= table_form_header('Header/Meta Data');
	
	// query url data
	$url_nms_tbl->get_db_vars($cities_tbl->url_name);

	$cities_form .= table_form_field('URL Name:','<script language="javascript">
function change_url_name(field_info){

var field_name = "#"+field_info;

jQuery(field_name).val(jQuery(field_name).val().replace(/[^a-zA-Z0-9_-]+/, "-")); 
jQuery(field_name).val(jQuery(field_name).val().replace(" ", "-")); 
}
</script>
<input type="hidden" name="url_id" value="'.$cities_tbl->url_name.'">
<input name="url_name" id="url_name" type="text" size="60" value="'.$url_nms_tbl->url_name.'" onKeyUp="change_url_name(\'url_name\')">');
	$cities_form .= table_form_field('Header Title:','<textarea name="page_title" cols="50" rows="6">'.$cities_tbl->page_title.'</textarea>');
	$cities_form .= table_form_field('Meta Description:','<textarea name="meta_description" cols="50" rows="6">'.$cities_tbl->meta_description.'</textarea>');
	$cities_form .= table_form_field('Meta Keywords:','<textarea name="meta_keywords" cols="50" rows="6">'.$cities_tbl->meta_keywords.'</textarea>');
	
	$cities_form .= table_span_form_field('<center><input name="id" id="cityid" type="hidden" value="'.$cities_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
	
  return $cities_form;
  }
	  
  // check form submission values
  function form_check() {
	global $cities_tbl;
	
	// required fields array
	$required_fields = array(
							'state' => $cities_tbl->state,
							'city' => $cities_tbl->city,
							);
		
	// check error values and write error array					
	foreach($required_fields as $field_name => $output) {
	  if (empty($output)) {
		$errors_array[] = $field_name;
	  }
	}
	
	// print errors
	if (!empty($errors_array)) {
	  $error_message = 'You did not supply a value for these fields: ' . implode(', ',$errors_array);
	}
	
	// check for existing search friendly link
	if (existing_link_check($cities_tbl->url_name,$cities_tbl->id,'city') > 0) {
	  $error_message .= '<center>Same URL Name already exists within database. Please enter another.</center>'.LB;
	}
	
  return $error_message;
  }

}

?>