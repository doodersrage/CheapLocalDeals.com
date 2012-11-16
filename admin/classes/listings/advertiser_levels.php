<?PHP

// manages and inserts advertiser levels

// customer levels management class
class advertiser_levels_lst {

  // display advertiser_levels listing
  function listing($message = '') {
	$advertiser_levels_view = open_table_listing_form('Advertiser Levels Listing','view_advertiser_levels',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=customerlevels','post',$message);
	$advertiser_levels_view .= $this->listing_content();
	$advertiser_levels_view .= close_table_form();
  return $advertiser_levels_view;
  }

  // list advertiser_levels	
  function listing_content() {
	global $dbh;	

	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'Name',
						'Weight',
						'Duration',
						'Upfront Cost',
						'Delete Customer Level',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);

	// draw table header
	$searchbox_head = array('<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=customerlevelsnew">Add New Customer Level</a>');
	$advertiser_levels_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
						
	// print title boxes
	$advertiser_levels_listing .= draw_table_header($title_array);

	// draw table header		
	$sql_query = "SELECT
					id,
					level_name,
					level_weight,
					level_duration,
					level_upfront_cost
				 FROM
					advertiser_levels
				 ORDER BY level_weight ";
	
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
	
	while($advertiser_levels_advertiser_levels = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	
		$row_array = array(
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=customerlevelsedit&cid='.$advertiser_levels_advertiser_levels['id'].'">'.$advertiser_levels_advertiser_levels['level_name'].'</a>',
							$advertiser_levels_advertiser_levels['level_weight'],
							$advertiser_levels_advertiser_levels['level_duration'],
							$advertiser_levels_advertiser_levels['level_upfront_cost'],
							'<input name="delete_advertiser_levels[]" type="checkbox" value="'.$advertiser_levels_advertiser_levels['id'].'">',
							);
	
		$advertiser_levels_listing .= draw_table_contect($row_array,0,'center');
	
	}
	
	if (empty($_POST['search_box'])) {
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						advertiser_levels
					 ;";
		
		$rowscount = $dbh->queryRow($sql_query);
		
		$row_count = $rowscount['rcount'];
		$page_count = (int)$row_count/$page_limiter;
		
		for($i = 0;$i <= $page_count;$i++) {
			$pages_array[] = '<a href="?sect=retcustomer&mode=customerlevels&cid='.$_GET['lid'].'&page_val='.($i*$page_limiter).'">'.($i+1).'</a>';
		}
		
		$pages_links = implode(', ',$pages_array);
		
		$advertiser_levels_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	}
	
	$advertiser_levels_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
	
  return $advertiser_levels_listing;
  }

}
?>