<?PHP

// display, add, and edit searchable state list

class states_frm {
	
	// delete states
	function delete() {
		global $dbh;
		foreach($_POST['delete_states'] as $selected_zips) {
			$stmt = $dbh->prepare("DELETE FROM states WHERE id = '".$selected_zips."';");
			$stmt->execute();
			$stmt = $dbh->prepare("DELETE FROM url_names WHERE type = 'state' AND parent_id = '".$selected_zips."';");
			$stmt->execute();
		}
	}

	// load add state page
	function edit($message = '') {
		
		$add_states = open_table_form('Edit State','edit_states',SITE_ADMIN_SSL_URL.'?sect=states&mode=editcheck','post',$message);
		$add_states .= $this->form();
		$add_states .= close_table_form();
		
		return $add_states;
	}
	
	// load add state page
	function add($message = '') {
		
		$add_states = open_table_form('Add New State','add_states',SITE_ADMIN_SSL_URL.'?sect=states&mode=addcheck','post',$message);
		$add_states .= $this->form();
		$add_states .= close_table_form();
		
		return $add_states;
	}
	
	
	// draw states edit form
	function form() {
		global $dbh, $stes_tbl, $url_nms_tbl;
		
		$states_form = table_form_header('* indicates required field');
		$states_form = table_form_field('<span class="required">*State:</span>','<input name="state" type="text" size="20" maxlength="130" value="'.$stes_tbl->state.'">');
		$states_form .= table_form_field('<span class="required">*ACN:</span>','<input name="acn" type="text" size="2" maxlength="2" value="'.$stes_tbl->acn.'">');
		
		$oFCKeditor = new FCKeditor('page_header') ;
		$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
		$oFCKeditor->Height = 400;
		$oFCKeditor->Value = $stes_tbl->page_header;
		
		$states_form .= table_form_field('Page Header:',$oFCKeditor->Create());
		
		$oFCKeditor = new FCKeditor('page_footer') ;
		$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
		$oFCKeditor->Height = 400;
		$oFCKeditor->Value = $stes_tbl->page_footer;
		
		$states_form .= table_form_field('Page Footer:',$oFCKeditor->Create());
		
		$states_form .= table_form_header('Header/Meta Data');
		
		// query url data
		$url_nms_tbl->get_db_vars($stes_tbl->url_name);

		$states_form .= table_form_field('URL Name:','<script language="javascript">
function change_url_name(field_info){

var field_name = "#"+field_info;

jQuery(field_name).val(jQuery(field_name).val().replace(/[^a-zA-Z0-9_-]+/, "-")); 
jQuery(field_name).val(jQuery(field_name).val().replace(" ", "-")); 
}
</script>
<input type="hidden" name="url_id" value="'.$stes_tbl->url_name.'">
<input name="url_name" id="url_name" type="text" size="60" value="'.$url_nms_tbl->url_name.'" onKeyUp="change_url_name(\'url_name\')">');
		$states_form .= table_form_field('Header Title:','<textarea name="page_title" cols="50" rows="6">'.$stes_tbl->page_title.'</textarea>');
		$states_form .= table_form_field('Meta Description:','<textarea name="meta_description" cols="50" rows="6">'.$stes_tbl->meta_description.'</textarea>');
		$states_form .= table_form_field('Meta Keywords:','<textarea name="meta_keywords" cols="50" rows="6">'.$stes_tbl->meta_keywords.'</textarea>');
		
		$states_form .= table_span_form_field('<center><input name="id" type="hidden" value="'.$stes_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
		
		return $states_form;
	}
		
	// check form submission values
	function form_check() {
			global $stes_tbl;
		
		// required fields array
		$required_fields = array(
								'state' => $stes_tbl->state,
								'acn' => $stes_tbl->acn,
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
		if (existing_link_check($stes_tbl->url_name,$stes_tbl->id,'state') > 0) {
			$error_message .= '<center>Same URL Name already exists within database. Please enter another.</center>'.LB;
		}
		
		return $error_message;
	}

}

?>