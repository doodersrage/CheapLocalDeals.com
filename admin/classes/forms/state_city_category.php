<?PHP

// document creates and modifies advertiser categories

// handles and displays categories admin page
class state_city_category_frm {
	var $bc_data = '';
	
	// load add category page
	function add($message = '') {
		
		$add_cat = open_table_form('Add new Category','add_category',SITE_ADMIN_SSL_URL.'?sect=citiescategories&cid='.$_GET['cid'].'&mode=addcheck','post',$message);
		$add_cat .= $this->category_form();
		$add_cat .= close_table_form();
		
	return $add_cat;
	}
	
	// load add category page
	function edit($message = '') {
		
		$add_cat = open_table_form('Edit Category','edit_category',SITE_ADMIN_SSL_URL.'?sect=citiescategories&cid='.$_GET['cid'].'&mode=editcheck','post',$message);
		$add_cat .= $this->category_form();
		$add_cat .= close_table_form();
		
	return $add_cat;
	}
	
	// draw category form
	function category_form() {
		global $ste_cty_cat_tbl, $cats_tbl, $url_nms_tbl;
	
		$category_form = table_form_header('* indicates required field');
		$category_form .= table_form_field('<span class="required">*Name:</span>',$cats_tbl->category_name);
		
		$oFCKeditor = new FCKeditor('page_header') ;
		$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
		$oFCKeditor->Height = 400;
		$oFCKeditor->Value = $ste_cty_cat_tbl->page_header;
		
		$category_form .= table_form_field('Content Header:',$oFCKeditor->Create());
		
		$oFCKeditor = new FCKeditor('page_footer') ;
		$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
		$oFCKeditor->Height = 400;
		$oFCKeditor->Value = $ste_cty_cat_tbl->page_footer;
		
		$category_form .= table_form_field('Content Footer:',$oFCKeditor->Create());
		$category_form .= table_form_header('Header/Meta Data');
		
		// query url data
		$url_nms_tbl->get_db_vars($ste_cty_cat_tbl->url_name);
		
		$category_form .= table_form_field('URL Name:','<script language="javascript">
function change_link_name(field_info){

var field_name = "#"+field_info;

jQuery(field_name).val(jQuery(field_name).val().replace(/[^a-zA-Z0-9_-]+/, "-")); 
jQuery(field_name).val(jQuery(field_name).val().replace(" ", "-")); 
}
</script>
<input type="hidden" name="url_id" value="'.$ste_cty_cat_tbl->url_name.'">
<input name="url_name" id="url_name" type="text" size="60" value="'.$url_nms_tbl->url_name.'" onKeyUp="change_link_name(\'url_name\')" >');
		$category_form .= table_form_field('Header Title:','<textarea name="page_title" cols="50" rows="6">'.$ste_cty_cat_tbl->page_title.'</textarea>');
		$category_form .= table_form_field('Meta Description:','<textarea name="meta_description" cols="50" rows="6">'.$ste_cty_cat_tbl->meta_description.'</textarea>');
		$category_form .= table_form_field('Meta Keywords:','<textarea name="meta_keywords" cols="50" rows="6">'.$ste_cty_cat_tbl->meta_keywords.'</textarea><input name="id" type="hidden" value="'.$ste_cty_cat_tbl->id.'"><input name="state" type="hidden" value="'.$ste_cty_cat_tbl->state.'"><input name="city" type="hidden" value="'.$ste_cty_cat_tbl->city.'"><input name="category" type="hidden" value="'.$ste_cty_cat_tbl->category.'">');
		$category_form .= table_span_form_field('<center><input name="submit" type="submit" value="Submit"></center>');
		
	return $category_form;
	}
	
	// check form submission values
	function form_check() {
			global $dbh, $cats_tbl, $ste_cty_cat_tbl;
		
		if (existing_link_check($ste_cty_cat_tbl->url_name,$ste_cty_cat_tbl->id,'citiescate') > 0) {
			$error_message .= '<center>Same URL Name already exists within database. Please enter another.</center>'.LB;
		}
		
	return $error_message;
	}
	
}

?>