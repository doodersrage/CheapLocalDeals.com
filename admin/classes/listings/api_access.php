<?PHP

// handles and displays error page results page
class api_access_lst {
  
  function delete() {
	global $dbh;
		
	// deleted selected items
	if(isset($_POST['delete_api_access'])) {
	  if(is_array($_POST['delete_api_access'])) {
		foreach($_POST['delete_api_access'] as $id => $del_advert) {
		  $stmt = $dbh->prepare("DELETE FROM api_access WHERE id = '".$del_advert."';");
		  $stmt->execute();
		}
	  }
	}
  }
  
  // display all orders listing
  function listing($message = '') {
	$all_orders_view = open_table_listing_form('API Access Listing','view_page','','post',$message);
	$all_orders_view .= $this->listing_content();
	$all_orders_view .= close_table_form();
  return $all_orders_view;
  }
  
  function listing_content() {
	global $dbh;
	
	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'APIKey',
						'Name',
						'Delete',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);
	
	// draw table header
	$searchbox_head = array('<a href="?sect=apiaccess&mode=new">Create New API Access Settings</a>');
	$api_listing = draw_table_header($searchbox_head,$table_boxes_cnt,'center');
							
	// print title boxes
	$api_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					apikey,
					name
				 FROM
					api_access
				 ORDER BY
					name ASC
			  ;";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	
	while($orders = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	  $row_array = array(
						  '<a href="?sect=apiaccess&mode=edit&uid='.$orders['id'].'">'.$orders['apikey'].'</a>',
						  $orders['name'],
						  '<input name="delete_api_access[]" type="checkbox" value="'.$orders['id'].'" />',
						  );
  
	  $api_listing .= draw_table_contect($row_array,0,'center');
	}
	
	$api_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt);
			
  return $api_listing;
  }
  
}
?>