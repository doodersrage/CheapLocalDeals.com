<?PHP

// add or modify static pages

// handles and displays pages admin page
class pages_frm {
  var $bc_data = '';
  
  // delete categroies
  function delete_page() {
	global $dbh;
	foreach($_POST['delete_pages'] as $selected_pages) {
	  $stmt = $dbh->prepare("DELETE FROM url_names WHERE type = 'page' AND parent_id = '".$selected_pages."';");
	  $stmt->execute();
	  $stmt = $dbh->prepare("DELETE FROM pages WHERE id = '".$selected_pages."';");
	  $stmt->execute();
	}
  }
  
  // load add page page
  function add($message = '') {
	$add_pages = open_table_form('Add New Page','add_page',SITE_ADMIN_SSL_URL.'?sect=pages&mode=addcheck','post',$message);
	$add_pages .= $this->form();
	$add_pages .= close_table_form();
  return $add_pages;
  }
  
  // load add page page
  function edit($message = '') {
	$add_pages = open_table_form('Edit Page','edit_page',SITE_ADMIN_SSL_URL.'?sect=pages&mode=editcheck','post',$message);
	$add_pages .= $this->form();
	$add_pages .= close_table_form();
  return $add_pages;
  }
  
  // draw page form
  function form() {
	global $pgs_tbl, $url_nms_tbl;

	$pages_form = table_form_header('* indicates required field');
	$pages_form .= table_form_field('<span class="required">*Name:</span>','<input name="name" type="text" size="60" value="'.$pgs_tbl->name.'">');
	$pages_form .= table_form_field('Hide Page Name On Output Page:','<input name="display_name" type="checkbox" value="1" '.($pgs_tbl->display_name == 1 ? 'checked' : '').' />');
	$pages_form .= table_form_field('Do not cache page:','<input name="dont_cache" type="checkbox" value="1" '.($pgs_tbl->dont_cache == 1 ? 'checked' : '').' />');
	
	$oFCKeditor = new FCKeditor('header_content') ;
	$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
	$oFCKeditor->Height = 400;
	$oFCKeditor->Value = $pgs_tbl->header_content;
	
	$pages_form .= table_form_field('Content Header:',$oFCKeditor->Create());
	
	$oFCKeditor = new FCKeditor('footer_content') ;
	$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
	$oFCKeditor->Height = 400;
	$oFCKeditor->Value = $pgs_tbl->footer_content;
	
	$pages_form .= table_form_field('Content Footer:',$oFCKeditor->Create());

	$pages_form .= table_form_header('Header/Meta Data');
	
	// query url data
	$url_nms_tbl->get_db_vars($pgs_tbl->url_name);
	
	$pages_form .= table_form_field('URL Name:','<script language="javascript">
function change_link_name(field_info){

var field_name = "#"+field_info;

jQuery(field_name).val(jQuery(field_name).val().replace(/[^a-zA-Z0-9_-]+/, "-")); 
jQuery(field_name).val(jQuery(field_name).val().replace(" ", "-")); 
}
</script>
<input type="hidden" name="url_id" value="'.$pgs_tbl->url_name.'">
<input name="url_name" id="url_name" type="text" size="60" value="'.$url_nms_tbl->url_name.'" onKeyUp="change_link_name(\'url_name\')" >');
	$pages_form .= table_form_field('Header Title:','<textarea name="header_title" cols="50" rows="6">'.$pgs_tbl->header_title.'</textarea>');
	$pages_form .= table_form_field('Meta Description:','<textarea name="meta_description" cols="50" rows="6">'.$pgs_tbl->meta_description.'</textarea>');
	$pages_form .= table_form_field('Meta Keywords:','<textarea name="meta_keywords" cols="50" rows="6">'.$pgs_tbl->meta_keywords.'</textarea><input name="id" type="hidden" value="'.$pgs_tbl->id.'">');
	$pages_form .= table_span_form_field('<center><input name="submit" type="submit" value="Submit"></center>');
	
  return $pages_form;
  }
  
  // check form submission values
  function form_check() {
	global $dbh, $pgs_tbl;
	
	if ($pgs_tbl->name == '') {
	  $error_message .= '<center>You must atleast assign a page name.</center>'.LB;
	}
	
	if (existing_link_check($pgs_tbl->url_name,$pgs_tbl->id,'page') > 0) {
	  $error_message .= '<center>Same URL Name already exists within database. Please enter another.</center>'.LB;
	}
	
  return $error_message;
  }
  
}

?>