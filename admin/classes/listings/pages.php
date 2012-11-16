<?PHP

// add or modify static pages

// handles and displays pages admin page
class pages_lst {
  var $bc_data = '';
	  
  // display pages listing
  function listing($message = '') {
	$page_view = open_table_listing_form('Pages Listing','view_page','','post',$message);
	$page_view .= $this->listing_content();
	$page_view .= close_table_form();
  return $page_view;
  }
  
  function listing_content() {
	global $dbh, $pgs_tbl;
	
	// table title array							
	$title_array = array(
						'Name',
						'Updated',
						'Delete Page<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_pages\').attr(\'checked\', \'checked\')">Select All</a>'
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);

	// print title boxes
	$pages_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					name,
					updated
				 FROM
					pages
				;";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	
	while($pages = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	
		$row_array = array(
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=pages&mode=edit&pid='.$pages['id'].'">'.$pages['name'].'</a>',
							date('n/j/Y h:i:s A',strtotime($pages['updated'])),
							'<input class="delete_pages" name="delete_pages[]" type="checkbox" value="'.$pages['id'].'">'
							);
	
		$pages_listing .= draw_table_contect($row_array,0,'center');
	
	}
	
	$pages_listing .= table_listing_span_form_field('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>');
	
  return $pages_listing;
  }
}

?>