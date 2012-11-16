<?PHP

// add or modify static pages
// handles and displays pages admin page
class balance_coupons_lst {
  var $bc_data = '';
  
  // display pages listing
  function listing($message = '') {
	$page_view = open_table_listing_form('Balance Coupon Listing','view_page','?sect=regcustomer&mode=balancecoupons','post',$message,8);
	$page_view .= $this->listing_content();
	$page_view .= close_table_form();
  return $page_view;
  }
  
  function listing_content() {
	global $dbh, $cust_cpns_tbl, $customer_info_table;

	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'Code',
						'Value',
						'Expires',
						'Used',
						'Used By',
						'Used Date',
						'Added',
						'Delete Page<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_pages\').attr(\'checked\', \'checked\')">Select All</a>'
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);

	// draw table header
	$searchbox_head = array('<a href="'.SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=balancecouponsadd">add new balance coupon</a>');
	$pages_listing = draw_table_header($searchbox_head,$table_boxes_cnt,'center');

	// print title boxes
	$pages_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					code,
					value,
					expires,
					used,
					used_by_cust_id,
					used_date,
					added
				 FROM
					customer_coupons
				 ORDER BY added desc";
	
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
	
	while($pages = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	
		// get customer data if the coupon has been used
		if ($pages['used_by_cust_id'] > 0) {
			
			$customer_info_table->get_db_vars($pages['used_by_cust_id']);
			if ($customer_info_table->id > 0) {
				
				$customer_info = '<a href="?sect=regcustomer&mode=edit&cid='.$customer_info_table->id.'">'.$customer_info_table->first_name.' '.$customer_info_table->last_name.'</a>';
				
			} else {
				$customer_info = '';	
			}
			
		} else {
			$customer_info = '';
		}
	
		$row_array = array(
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=balancecouponsedit&pid='.$pages['id'].'">'.$pages['code'].'</a>',
							$pages['value'],
							date('n/j/Y',strtotime($pages['expires'])),
							($pages['used'] == 1 ? 'Yes' : 'No'),
							$customer_info,
							($pages['used_date'] > 0 ? date('n/j/Y',strtotime($pages['used_date'])) : ''),
							date('n/j/Y h:i:s A',strtotime($pages['added'])),
							'<input class="delete_pages" name="delete_coupons[]" type="checkbox" value="'.$pages['id'].'">'
							);
	
		$pages_listing .= draw_table_contect($row_array,0,'center');
	
	}
	
	if (empty($_POST['search_box'])) {
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						customer_coupons
					 ;";
		
		$rowscount = $dbh->queryRow($sql_query);
		
		$row_count = $rowscount['rcount'];
		$page_count = (int)$row_count/$page_limiter;
		
		for($i = 0;$i <= $page_count;$i++) {
			$pages_array[] = '<a href="?sect=regcustomer&mode=balancecoupons&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
		}
		
		$pages_links = implode(', ',$pages_array);
		
		$pages_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt);
	}
	
	$pages_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
	
  return $pages_listing;
  }
}

?>