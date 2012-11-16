<?PHP

// display, add, and edit searchable state list

class cities_lst {
  
  // display state listing
  function listing($message = '') {
	$cat_view = open_table_listing_form('Cities & Towns Listing','view_city',SITE_ADMIN_SSL_URL.'?sect=cities&mode=view','post',$message,6);
	$cat_view .= $this->listing_content();
	$cat_view .= close_table_form();
  return $cat_view;
  }
	  
  // list cities	
  function listing_content() {
	global $dbh, $city_type;
		
	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'Edit',
						'Delete',
						'Name',
						'State',
						'Type',
						'Categories',
						);
	
	// gets table boxes count
	$table_boxes_cnt = count($title_array);

	// draw table header
	$searchbox_head = array('<a href="'.SITE_ADMIN_SSL_URL.'?sect=cities&mode=add">add new City</a>');
	
	$cities_listing = draw_table_header($searchbox_head,$table_boxes_cnt,'center');

	// draw table header
	$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
	$cities_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');

	// draw table header
	$searchbox_head = array('Filter by State: <select name="state_fltr">'.gen_state_dd($_SESSION['state_fltr']).'</select><input name="submit" type="submit" value="Submit">');
	$cities_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
	
	// print page links
	if (empty($_POST['search_box'])) {
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						cities ";
		$where_arr = array();
		$values = array();
		if(!empty($_SESSION['state_fltr'])) {
		  $where_arr[] = " state = ? ";
		  $values[] = $_SESSION['state_fltr'];
		}
		if(!empty($_POST['search_box'])) {
		  $where_arr[] = " city = ? ";
		  $values[] = $_POST['search_box'];
		}
		if(count($where_arr) > 0) {
			$sql_query .= " WHERE ".implode(' AND ',$where_arr);
		}
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		$rowscount = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$row_count = $rowscount['rcount'];
		$page_count = (int)$row_count/$page_limiter;
		
		for($i = 0;$i <= $page_count;$i++) {
		$pages_array[] = '<a href="?sect=cities&mode=view&page_val='.($i*$page_limiter).'">'.(isset($_GET['page_val']) ? $_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '' : '').($i+1).(isset($_GET['page_val']) ? $_GET['page_val'] == $i*$page_limiter ? '</font>' : '' : '').'</a>';
		}
		
		$pages_links = implode(', ',$pages_array);
		
		$cities_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	}
						
	// print title boxes
	$cities_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					state,
					type,
					city
				 FROM
					cities";
	
	$where_arr = array();
	$values = array();
	if(!empty($_SESSION['state_fltr'])) {
	  $where_arr[] = " state = ? ";
	  $values[] = $_SESSION['state_fltr'];
	}
	if(!empty($_POST['search_box'])) {
	  $where_arr[] = " city = ? ";
	  $values[] = $_POST['search_box'];
	}
	if(count($where_arr) > 0) {
		$sql_query .= " WHERE ".implode(' AND ',$where_arr);
	}
	
	$sql_query .= "
			ORDER BY
				state ASC ";
	
	if (!empty($_GET['page_val']) && empty($_POST['search_box'])) {
	$sql_query .= "
			LIMIT ".$_GET['page_val'].",".$page_limiter."  ";
	} elseif (empty($_POST['search_box'])) {
	$sql_query .= "
			LIMIT
			".$page_limiter." ";
	}
				
	$sql_query .= ";";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute($values);
	
	while($cities = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						state_city_category
					WHERE city = '".$cities['id']."' ;";
		
		$rowscount = $dbh->queryRow($sql_query);
	
		$row_array = array(
							'<a class="edit_image" href="'.SITE_ADMIN_SSL_URL.'?sect=cities&mode=edit&cid='.$cities['id'].'">'.$cities['city'].'</a>',
							'<input class="delete_zip" name="delete_cities[]" type="checkbox" value="'.$cities['id'].'">',
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=cities&mode=edit&cid='.$cities['id'].'">'.$cities['city'].'</a>',
							$cities['state'],
							$city_type[$cities['type']],
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=citiescategories&mode=view&cid='.$cities['id'].'">'.$rowscount['rcount'].'</a>',
							);
	
		$cities_listing .= draw_table_contect($row_array,0,'center');
	
	}
	
	// print page links
	$cities_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	
	$cities_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
	
  return $cities_listing;
  }

}

?>