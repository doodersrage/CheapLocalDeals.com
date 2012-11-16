<?PHP

// display, add, and edit searchable state list

class states_lst {
	
	// display state listing
	function listing($message = '') {
		$cat_view = open_table_listing_form('State Listing','view_category',SITE_ADMIN_SSL_URL.'?sect=states&mode=view','post',$message);
		$cat_view .= $this->listing_content();
		$cat_view .= close_table_form();
		
		return $cat_view;
	}
		
	// list states	
	function listing_content() {
			global $dbh;
			
		// sets record limit per page	
		$page_limiter = ADMIN_PER_PAGE_RESULTS; 
		
		// table title array							
		$title_array = array(
							'State',
							'ACN',
							'Delete State<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_zip\').attr(\'checked\', \'checked\')">Select All</a>',
							);
		
		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// draw table header
		$searchbox_head = array('<a href="'.SITE_ADMIN_SSL_URL.'?sect=states&mode=add">add new state</a>');
		$states_listing = draw_table_header($searchbox_head,$table_boxes_cnt,'center');

		// draw table header
		$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
		$states_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
		
		// print page links
		if (empty($_POST['search_box'])) {
			$sql_query = "SELECT
							count(*) as rcount
						 FROM
							states ";
			if(!empty($_POST['search_box'])) {
				$sql_query .= "
						WHERE state LIKE
						'%".$_POST['search_box']."%' OR acn LIKE '%".$_POST['search_box']."%' ";
			}
			
			$rowscount = $dbh->queryRow($sql_query);
			
			$row_count = $rowscount['rcount'];
			$page_count = (int)$row_count/$page_limiter;
			
			for($i = 0;$i <= $page_count;$i++) {
			$pages_array[] = '<a href="?sect=states&mode=view&page_val='.($i*$page_limiter).'">'.(isset($_GET['page_val']) ? $_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '' : '').($i+1).(isset($_GET['page_val']) ? $_GET['page_val'] == $i*$page_limiter ? '</font>' : '' : '').'</a>';
			}
			
			$pages_links = implode(', ',$pages_array);
			
			$states_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		}
							
		// print title boxes
		$states_listing .= draw_table_header($title_array);
		
		$sql_query = "SELECT
						id,
						state,
						acn
					 FROM
						states";
		
		if(!empty($_POST['search_box'])) {
		$sql_query .= "
				WHERE state =
				'".$_POST['search_box']."' OR acn = '".$_POST['search_box']."' ";
		}
		$sql_query .= "
				ORDER BY
					state ASC ";
		
		if (!empty($_GET['page_val']) && empty($_POST['search_box'])) {
		$sql_query .= "
				LIMIT
				".$_GET['page_val'].",".$page_limiter."  ";
		} elseif (empty($_POST['search_box'])) {
		$sql_query .= "
				LIMIT
				".$page_limiter." ";
		}
					
		$sql_query .= ";";
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute();
		
		while($states = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
			$row_array = array(
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=states&mode=edit&cid='.$states['id'].'">'.$states['state'].'</a>',
								$states['acn'],
								'<input class="delete_zip" name="delete_states[]" type="checkbox" value="'.$states['id'].'">',
								);
		
			$states_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
		// print page links
		$states_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		
		$states_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
		
		return $states_listing;
	}

}

?>