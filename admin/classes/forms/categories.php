<?PHP

class categories_frm {
  var $bc_data = '';
  
  // delete categroies
  function delete_category() {
	global $dbh;
	
	// delete selected primary categories
	foreach($_POST['delete_cat'] as $selected_cats) {
	  $stmt = $dbh->prepare("DELETE FROM categories WHERE id = '".$selected_cats."';");
	  $stmt->execute();
	  $stmt = $dbh->prepare("DELETE FROM url_names WHERE type = 'category' AND parent_id = '".$selected_cats."';");
	  $stmt->execute();
	  // reset script timeout
	  set_time_limit(0);
	  
	  // get newly inserted city id
	  $sql_query = "SELECT
					  id
				   FROM
					  state_city_category
				   WHERE category = ?
				   ;";
		  
	  $values = array(
					  $selected_cats
					  );
		  
	  $stmt = $dbh->prepare($sql_query);					 
	  $result = $stmt->execute($values);
  
	  while($cat_cits = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		// reset script timeout
		set_time_limit(0);
		if(!empty($cat_cits['id'])){
			$stmtdel = $dbh->prepare("DELETE FROM state_city_category WHERE id = ".$cat_cits['id']." ;");
			$stmtdel->execute();
			$stmtdel = $dbh->prepare("DELETE FROM url_names WHERE type = 'citiescate' AND parent_id = ".$cat_cits['id']." ;");
			$stmtdel->execute();
		}
	  }
		  
	  // check for child cats then delete them
	  $this->delete_child_cats($selected_cats);
	}
	
  }
  
  // remove child categories
  function delete_child_cats($cat_id) {
	global $dbh;
	
	// check for child categories
	$sql_query = "SELECT
					id
				 FROM
					categories
				 WHERE
					parent_category_id = '".$cat_id."';";
	
	$rows = $dbh->queryAll($sql_query);
	
	foreach($rows as $found_child) {
	  // reset script timeout
	  set_time_limit(0);
	  $stmt = $dbh->prepare("DELETE FROM categories WHERE id = '".$found_child['id']."';");
	  $stmt->execute();
	  
	  // get newly inserted city id
	  $sql_query = "SELECT
					  id
				   FROM
					  state_city_category
				   WHERE category = ?
				   ;";
		  
	  $values = array(
					  $found_child['id']
					  );
		  
	  $stmtcit = $dbh->prepare($sql_query);					 
	  $resultcit = $stmtcit->execute($values);
  
	  while($cat_cits = $resultcit->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		// reset script timeout
		set_time_limit(0);
		if(!empty($cat_cits['id'])){
			mysql_query("DELETE FROM state_city_category WHERE id = ".$cat_cits['id']." ;");
			mysql_query("DELETE FROM url_names WHERE type = 'citiescate' AND parent_id = ".$cat_cits['id']." ;");
		}
	  }
	  
	  $this->delete_child_cats($found_child['id']);
	}
	
  }

  // load add category page
  function add($message = '') {
	
	$add_cat = '<script type="text/javascript">
jQuery(function(){

var document_height = jQuery(document).height();
var document_width = jQuery(document).width();
var box_height = 30;
var new_box_height = (document_height - box_height) / 2;
var new_error_width = (document_width - 325) / 2;
var new_error_height = (document_height - box_height-30) / 2;

// sets login half page
jQuery(\'.loading\').css(\'margin-top\',new_error_height+\'px\');
jQuery(\'.loading\').css(\'margin-left\',new_error_width+\'px\');
	
jQuery("form").submit(function() {

	jQuery(\'.background\').css(\'width\',document_width+\'px\');
	jQuery(\'.background\').css(\'height\',document_height+\'px\');
	jQuery(\'.background\').css(\'display\',\'\');

	jQuery(".loading").animate({ 
	"margin-left" : "50px", 
	"width" : "320px", 
	"heigth" : "30px",
	"opacity" : "100"
	}, 1000, function() {
	return true;
	});  
});

});
</script>
<style>
.background {
position:absolute;
color:#000;
z-index:999;
display:none;
}
.loading {
position:absolute;
top:0;
color:#FFFFFF;
font-weight:700;
width:300px;
font-size:16px;
padding:5px;
text-align:center;
margin:0 auto;
z-index:9999;
display:none;
</style>
<div class="loading" align="center"><img src="images/loading.gif"/></div>';
	$add_cat .= open_table_form('Add new Category','add_category',SITE_ADMIN_SSL_URL.'?sect=categories&mode=addcheck','post',$message);
	$add_cat .= $this->form();
	$add_cat .= close_table_form();
	
  return $add_cat;
  }
  
  // load add category page
  function edit($message = '') {
	$add_cat = open_table_form('Edit Category','edit_category',SITE_ADMIN_SSL_URL.'?sect=categories&mode=editcheck','post',$message);
	$add_cat .= $this->form();
	$add_cat .= close_table_form();
  return $add_cat;
  }
  
  // draw category form
  function form() {
	global $cats_tbl, $url_nms_tbl;

	$category_form = table_form_header('* indicates required field');
	$category_form .= table_form_field('<span class="required">*Name:</span>','<input name="category_name" type="text" size="60" value="'.$cats_tbl->category_name.'">');
	$category_form .= table_form_field('Sort Order:','<input name="sort_order" type="text" size="5" value="'.$cats_tbl->sort_order.'">');
	$category_form .= table_form_field('Disabled:','<input name="disabled" type="checkbox" value="1" '.($cats_tbl->disabled == 1 ? 'checked' : '').'>');
	$category_form .= table_form_field('Image:','<input name="image" type="file">'.(!empty($cats_tbl->image) ? '<br><input name="old_image" type="hidden" value="'.$cats_tbl->image.'">Current Image: '.$cats_tbl->image : ''));
	$category_form .= table_form_field('Google Maps Marker:','<input name="map_marker" type="file">'.(!empty($cats_tbl->map_marker) ? '<br><input name="old_map_marker" type="hidden" value="'.$cats_tbl->map_marker.'">Current Image: '.$cats_tbl->map_marker : ''));
	
	$oFCKeditor = new FCKeditor('header') ;
	$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
	$oFCKeditor->Height = 400;
	$oFCKeditor->Value = $cats_tbl->header_val;
	
	$category_form .= table_form_field('Content Header:',$oFCKeditor->Create());
	
	$oFCKeditor = new FCKeditor('footer') ;
	$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
	$oFCKeditor->Height = 400;
	$oFCKeditor->Value = $cats_tbl->footer;
	
	$category_form .= table_form_field('Content Footer:',$oFCKeditor->Create());
	$category_form .= table_form_field('Parent Category:',$this->parent_category_drop_down($cats_tbl->parent_category_id));
	$category_form .= table_form_header('Header/Meta Data');
	
	// query url data
	$url_nms_tbl->get_db_vars($cats_tbl->url_name);
	
	$category_form .= table_form_field('URL Name:','<script language="javascript">
function change_link_name(field_info){

var field_name = "#"+field_info;

jQuery(field_name).val(jQuery(field_name).val().replace(/[^a-zA-Z0-9_-]+/, "-")); 
jQuery(field_name).val(jQuery(field_name).val().replace(" ", "-")); 
}
</script>
<input type="hidden" name="url_id" value="'.$cats_tbl->url_name.'">
<input name="url_name" id="url_name" type="text" size="60" value="'.$url_nms_tbl->url_name.'" onKeyUp="change_link_name(\'url_name\')" >');
	$category_form .= table_form_field('Header Title:','<textarea name="header_title" cols="50" rows="6">'.$cats_tbl->header_title.'</textarea>');
	$category_form .= table_form_field('Meta Description:','<textarea name="meta_description" cols="50" rows="6">'.$cats_tbl->meta_description.'</textarea>');
	$category_form .= table_form_field('Meta Keywords:','<textarea name="meta_keywords" cols="50" rows="6">'.$cats_tbl->meta_keywords.'</textarea><input name="id" type="hidden" value="'.$cats_tbl->id.'">');
	$category_form .= table_span_form_field('<center><input name="submit" type="submit" value="Submit"></center>');
	
  return $category_form;
  }
  
  // parent category drop down menu
  function parent_category_drop_down($selected_id = '') {
	global $dbh;
	
	$parent_drop_down = '<select name="parent_category_id" id="parent_cat_dd">'.LB;
	$parent_drop_down .= '<option value="0"></option>'.LB;
	
	$sql_query = "SELECT
					id,
					category_name,
					parent_category_id
				 FROM
					categories
				 WHERE
					zip_id is NULL
				 AND
					parent_category_id = 0
				 ;";
	$rows = $dbh->queryAll($sql_query);
	
	foreach ($rows as $categories) {
	  $ind = '--';
	  $parent_drop_down .= '<option value="'.$categories['id'].'" '.($selected_id == $categories['id'] ? 'selected="selected" ' : '').'>'.$categories['category_name'].'</option>'.LB;
	  $parent_drop_down .= $this->parent_dd_child_chk($categories['id'],$ind,$selected_id);
	}
	
	$parent_drop_down .= '</select>'.LB;
	
  return $parent_drop_down;
  }
  
  // check for child categories
  function parent_dd_child_chk($cid,$ind,$selected_id = '') {
	global $dbh;
		
	$sql_query = "SELECT
					id,
					category_name,
					parent_category_id
				 FROM
					categories
				 WHERE
					zip_id is NULL
				 AND
					parent_category_id = '".$cid."'
				 ;";
	$rows = $dbh->queryAll($sql_query);
	
	foreach ($rows as $categories) {
	$parent_drop_down .= '<option value="'.$categories['id'].'" '.($selected_id == $categories['id'] ? 'selected="selected" ' : '').'>'.$ind.' '.$categories['category_name'].'</option>'.LB;
	
	$parent_drop_down .= $this->parent_dd_child_chk($categories['id'],$ind.'--');
	}
	
  return $parent_drop_down;
  }
  
  // check form submission values
  function form_check() {
	global $dbh, $cats_tbl;
	
	if ($cats_tbl->category_name == '') {
	  $error_message .= '<center>You must atleast assign a category name.</center>'.LB;
	}
	
	if (existing_link_check($cats_tbl->url_name,$cats_tbl->id,'category') > 0) {
	  $error_message .= '<center>Same URL Name already exists within database. Please enter another.</center>'.LB;
	}
	
  return $error_message;
  }
  
}

?>