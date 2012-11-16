<?PHP

// document creates and modifies advertiser categories

// handles and displays categories admin page
class categories_lst {
  var $bc_data = '';
  
  // display category listing
  function listing($message = '') {
	$cat_view = open_table_listing_form('Category Listing','view_category','','post',$message);
	$cat_view .= $this->listing_content();
	$cat_view .= close_table_form();
  return $cat_view;
  }
  
  function category_bg($pid) {
	global $dbh;
			
	$sql_query = "SELECT
					id,
					category_name,
					parent_category_id
				 FROM
					categories
				 WHERE
					id = '".$pid."';";
	
	$rows = $dbh->queryAll($sql_query);
	
	$this->bc_data[] = ' <a href="'.SITE_ADMIN_SSL_URL.'?sect=categories&mode=view&pid='.$rows[0]['id'].'">'.$rows[0]['category_name'].'</a> ';
	
	if ($rows[0]['parent_category_id'] > 0) {
	$this->category_bg($rows[0]['parent_category_id']);
	}
	
  }
  
  function listing_content() {
	global $dbh, $cats_tbl;
			
	$this->bc_data = '';
	if (isset($_GET['pid'])) {
		$this->category_bg($_GET['pid']);
		krsort($this->bc_data);
		
		$breadcrumbs = implode('::',$this->bc_data);
	}
	
	// table title array							
	$title_array = array(
						'Category Name',
						'Sub Categories',
						'Updated',
						'Delete Category<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_cat\').attr(\'checked\', \'checked\')">Select All</a>',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);

	// draw table header
	$searchbox_head = array('<a href="'.SITE_ADMIN_SSL_URL.'?sect=categories&mode=view">Home</a>'.(!empty($breadcrumbs) ? ' :: ' : '').(isset($breadcrumbs) ? $breadcrumbs : ''));
	
	$cat_listing = draw_table_header($searchbox_head,$table_boxes_cnt,'center');
						
	// print title boxes
	$cat_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					category_name,
					parent_category_id,
					last_modified,
					sort_order
				 FROM
					categories";
	
	if (!empty($cats_tbl->parent_category_id)) {
	$sql_query .= "
			WHERE
			parent_category_id = '".$cats_tbl->parent_category_id."'";
	} elseif (isset($_GET['pid'])) {
	$sql_query .= "
			WHERE
			parent_category_id = '".$_GET['pid']."'";
	} else {
	$sql_query .= "
			WHERE
			parent_category_id = 0 ";
	}
				
	$sql_query .= ";";
		
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	
	while($categories = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	
		$row_array = array(
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=categories&mode=edit&cid='.$categories['id'].'">'.$categories['category_name'].'</a>',
							$this->sub_cat_cnt($categories['id']),
							date('n/j/Y h:i:s A',strtotime($categories['last_modified'])),
							'<input class="delete_cat" name="delete_cat[]" type="checkbox" value="'.$categories['id'].'">',
							);
	
		$cat_listing .= draw_table_contect($row_array,0,'center');
	
	}
	
	$cat_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
	
  return $cat_listing;
  }

  // display category listing
  function category_hit_listing($message = '') {
	$cat_view = open_table_listing_form('Category Views Listing','view_category','','post',$message);
	$cat_view .= $this->category_views_listing_content();
	$cat_view .= close_table_form();
  return $cat_view;
  }

  function category_views_listing_content() {
	global $dbh, $cats_tbl;
	
	// table title array							
	$title_array = array(
						'Category Name',
						'Sub Categories',
						'Updated',
						'Views',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);
						
	// print title boxes
	$cat_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					category_name,
					parent_category_id,
					last_modified,
					sort_order,
					views
				 FROM
					categories ";					
	$sql_query .= "ORDER BY views DESC, category_name ASC;";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	
	while($categories = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	
		$row_array = array(
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=categories&mode=edit&cid='.$categories['id'].'">'.$categories['category_name'].'</a>',
							$this->sub_cat_cnt($categories['id']),
							date('n/j/Y h:i:s A',strtotime($categories['last_modified'])),
							$categories['views'],
							);
	
		$cat_listing .= draw_table_contect($row_array,0,'center');
	
	}
			
  return $cat_listing;
  }
  
  // check for existing question fields
  function field_cnt($catid) {
	global $dbh;

	$sql_query = "SELECT
					count(*) as rcount
				 FROM
					category_fields
				 WHERE
					category_id = '".$catid."';";
	
	$rows = $dbh->queryAll($sql_query);
	
	$field_rslt = '<a href="'.SITE_ADMIN_SSL_URL.'?sect=categories&mode=fieldsview&pid='.$catid.'">'.$rows[0]['rcount'].'</a>';

  return $field_rslt;
  }
  
  // find number of sub-categories
  function sub_cat_cnt($catid) {
	global $dbh;
	
	$sql_query = "SELECT
					count(*) as rcount
				 FROM
					categories
				 WHERE
					parent_category_id = '".$catid."';";
	
	$rows = $dbh->queryAll($sql_query);
	
	if ($rows[0]['rcount'] > 0) {
	$sub_cat_cnt = '<a href="'.SITE_ADMIN_SSL_URL.'?sect=categories&mode=view&pid='.$catid.'">'.$rows[0]['rcount'].'</a>';
	} else {
	$sub_cat_cnt = '0';
	}
	
  return $sub_cat_cnt;
  }
}

?>