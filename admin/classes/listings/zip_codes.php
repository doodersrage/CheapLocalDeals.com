<?PHP

// display, add, and edit searchable zip code list

class zip_codes_lst {
	
	// display zip code listing
	function listing($message = '') {
		$cat_view = open_table_listing_form('Zip Code Listing','view_category',SITE_ADMIN_SSL_URL.'?sect=zipcodes&mode=view','post',$message);
		$cat_view .= $this->listing_content();
		$cat_view .= close_table_form();
		
		return $cat_view;
	}
		
	// list zip codes	
	function listing_content() {
			global $dbh;
			
		// sets record limit per page	
		$page_limiter = 300; 
		
		// table title array							
		$title_array = array(
							'Zip Code',
							'Updated',
							'Delete Zip Code<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_zip\').attr(\'checked\', \'checked\')">Select All</a>',
							);
		
		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// draw table header
		$searchbox_head = array('<a href="'.SITE_ADMIN_SSL_URL.'?sect=zipcodes&mode=add">add new zip code</a>');
		$zip_codes_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');

		// draw table header
		$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
		$zip_codes_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
		
		// print page links
		if (empty($_POST['search_box'])) {
			$sql_query = "SELECT
							count(*) as rcount
						 FROM
							zip_codes;";
			if(!empty($_POST['search_box'])) {
				$sql_query .= "
						WHERE zip LIKE
						'%".$_POST['search_box']."%' OR city LIKE '%".$_POST['search_box']."%' OR state LIKE '%".$_POST['search_box']."%' ";
			}
			
			$rowscount = $dbh->queryRow($sql_query);
			
			$row_count = $rowscount['rcount'];
			$page_count = (int)$row_count/$page_limiter;
			
			for($i = 0;$i <= $page_count;$i++) {
			$pages_array[] = '<a href="?sect=zipcodes&mode=view&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
			}
			
			$pages_links = implode(', ',$pages_array);
			
			$zip_codes_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		}
							
		// print title boxes
		$zip_codes_listing .= draw_table_header($title_array);
		
		$sql_query = "SELECT
						id,
						zip,
						updated
					 FROM
						zip_codes";
		
		if(!empty($_POST['search_box'])) {
		$sql_query .= "
				WHERE zip =
				'".$_POST['search_box']."' OR city = '".$_POST['search_box']."' OR state = '".$_POST['search_box']."' ";
		}
		$sql_query .= "
				ORDER BY
					zip ASC ";
		
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
		$result = $stmt->execute();
		
		while($zip_codes = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
			$row_array = array(
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=zipcodes&mode=edit&cid='.$zip_codes['id'].'">'.$zip_codes['zip'].'</a>',
								$zip_codes['modified'],
								'<input class="delete_zip" name="delete_zip[]" type="checkbox" value="'.$zip_codes['id'].'">',
								);
		
			$zip_codes_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
		// print page links
		$zip_codes_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		
		$zip_codes_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
		
		return $zip_codes_listing;
	}
	
	// display zip code views listing
	function views_listing($message = '') {
		$cat_view = open_table_listing_form('Zip Code Views Listing','view_category',SITE_ADMIN_SSL_URL.'?sect=zipcodes&mode=viewhits','post',$message);
		$cat_view .= $this->views_listing_content();
		$cat_view .= close_table_form();
		
		return $cat_view;
	}
		
	// list zip codes	
	function views_listing_content() {
			global $dbh;
			
		// sets record limit per page	
		$page_limiter = 300; 
		
		// table title array							
		$title_array = array(
							'Zip Code',
							'Updated',
							'Views',
							);
		
		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// draw table header
		$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
		$zip_codes_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
							
		// print title boxes
		$zip_codes_listing .= draw_table_header($title_array);
		
		$sql_query = "SELECT
						id,
						zip,
						updated,
						views
					 FROM
						zip_codes
					 ";
		
		if(!empty($_POST['search_box'])) {
			$sql_query .= "
					WHERE zip =
					'".$_POST['search_box']."' OR city = '".$_POST['search_box']."' OR state = '".$_POST['search_box']."' ";
		}

		$sql_query .= " ORDER BY views DESC, zip ASC ";
		
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
		$result = $stmt->execute();
		
		while($zip_codes = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
			$row_array = array(
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=zipcodes&mode=edit&cid='.$zip_codes['id'].'">'.$zip_codes['zip'].'</a>',
								$zip_codes['modified'],
								$zip_codes['views'],
								);
		
			$zip_codes_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
		if (empty($_POST['search_box'])) {
			$sql_query = "SELECT
							count(*) as rcount
						 FROM
							zip_codes;";
			
			$rowscount = $dbh->queryRow($sql_query);
			
			$row_count = $rowscount['rcount'];
			$page_count = (int)$row_count/$page_limiter;
			
			for($i = 0;$i <= $page_count;$i++) {
				$pages_array[] = '<a href="?sect=zipcodes&mode=viewhits&page_val='.($i*$page_limiter).'">'.($i).'</a>';
			}
			
			$pages_links = implode(', ',$pages_array);
			
			$zip_codes_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		}
				
		return $zip_codes_listing;
	}

}

?>