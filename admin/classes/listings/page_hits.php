<?PHP

// handles and displays error page results page
class page_hits_lst {
  
  // display all orders listing
  function listing($message = '') {
	$all_orders_view = open_table_listing_form('Page Hits Listing','view_page','','post',$message);
	$all_orders_view .= $this->listing_content();
	$all_orders_view .= close_table_form();
  return $all_orders_view;
  }
  
  function listing_content() {
	global $dbh, $customer_info_table;
	
	// set current page session var
	$_SESSION['previous_page'] = curPageURL();

	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'Link Name',
						'Count'							);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);
	
	$filter_title_array = array(
						'Start Date: <script>DateInput(\'start_date\', true, \'YYYY-MM-DD\',\''.(!empty($_POST['start_date']) ? $_POST['start_date'] : date("Y-m-d")).'\')</script>'.'End Date: <script>DateInput(\'end_date\', true, \'YYYY-MM-DD\',\''.(!empty($_POST['end_date']) ? $_POST['end_date'] : date("Y-m-d")).'\')</script>'.'<input type="submit" name="submit" value="Filter Results">'
						);
	$orders_listing .= draw_table_contect($filter_title_array,$table_boxes_cnt,'center');

	$sql_query = "SELECT
					link, count(link) as cnt
				 FROM
					page_hits ";
	$sql_query .= "GROUP BY
					link
				;";
	
	$rows = $dbh->queryAll($sql_query);
	
	$row_count = count($rows);
	$page_count = (int)$row_count/$page_limiter;
	
	for($i = 0;$i <= $page_count;$i++) {
		$pages_array[] = '<a href="?sect=page_hits&mode=view&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
	}
	
	$pages_links = implode(', ',$pages_array);
			
	if (empty($_POST['start_date'])) $orders_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
						
	// print title boxes
	$orders_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					link, count(link) as cnt
				 FROM
					page_hits ";
	if (!empty($_POST['start_date'])) {
	$sql_query .= "WHERE
					added >= '".$_POST['start_date']."'
				 AND
					added <= '".$_POST['end_date']."' ";
	}
	$sql_query .= "GROUP BY
					link
				 ORDER BY
					cnt DESC,link ASC";
						
	if (!empty($_GET['page_val']) && empty($_POST['start_date'])) {
	$sql_query .= "
			LIMIT ".$_GET['page_val'].",".$page_limiter."  ";
	} elseif(empty($_POST['start_date'])) {
	$sql_query .= "
			LIMIT
			".$page_limiter." ";
	}
	$sql_query .= ";";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	
	while($orders = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	
		$row_array = array(
							'<a href="'.$orders['link'].'">'.$orders['link'].'</a>',
							$orders['cnt'],
							);
	
		$orders_listing .= draw_table_contect($row_array,0,'center');
	}
	
	if (empty($_POST['start_date'])) $orders_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
			
  return $orders_listing;
  }
  
}
?>