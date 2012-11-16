<?PHP

class orders_lst {
  
  // display recent orders listing
  function recent_orders_listing($message = '') {
	$recent_orders_view = open_table_listing_form('Recent Orders Listing','view_page','','post',$message);
	$recent_orders_view .= $this->recent_orders_listing_content();
	$recent_orders_view .= close_table_form();
  return $recent_orders_view;
  }
  
  function recent_orders_listing_content() {
	global $dbh, $customer_info_table, $pp_pmts_tbl, $odrs_tbl;
		
	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'Order Date/Time',
						'Email Address',
						'Order Total',
						'Payment Type',
						'Delete Order',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);
						
	// print title boxes
	$orders_listing = draw_table_header($title_array);
	
	$sql_query = "SELECT 
					DISTINCT date_added, 
					order_id, 
					token
				 FROM 
				 	certificate_orders
				 WHERE
					DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= date_added 
				 ORDER BY
					date_added DESC";
						
	if (!empty($_GET['page_val'])) {
	$sql_query .= "
			LIMIT ".$_GET['page_val'].",".$page_limiter."  ";
	} else {
	$sql_query .= "
			LIMIT
			".$page_limiter." ";
	}
	$sql_query .= ";";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	
	while($orders = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
		if($orders['order_id'] > 0) {
		  $odrs_tbl->get_db_vars($orders['order_id']);
	  
		  // pull customer data
		  $customer_info_table->get_db_vars($odrs_tbl->customer_id);
	  
			$row_array = array(
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=orders&mode=view_order&oid='.$odrs_tbl->id.'">'.date('m/d/Y g:iA',strtotime($odrs_tbl->date_added)).'</a>',
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=edit&cid='.$odrs_tbl->customer_id.'">'.$customer_info_table->email_address.'</a>',
								$odrs_tbl->order_total,
								$odrs_tbl->credit_card_type,
								'<input name="delete_orders[]" type="checkbox" value="'.$odrs_tbl->id.'">',
								);
		} else {
		  $pp_pmts_tbl->assign_db_vars_token($orders['token']);
	  	  
		  $row_array = array(
							  '<a href="'.SITE_ADMIN_SSL_URL.'?sect=orders&mode=view_order&pid='.$orders['token'].'">'.date('m/d/Y g:iA',strtotime($orders['date_added'])).'</a>',
							  'PayPal',
							  $pp_pmts_tbl->amount,
							  '',
							  '',
							  );
		}
	
		$orders_listing .= draw_table_contect($row_array,0,'center');
		
	}
		
	$sql_query = "SELECT 
					DISTINCT date_added, 
					order_id, 
					token
				 FROM 
				 	certificate_orders
				 WHERE
					DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= date_added 
				 ORDER BY
					date_added DESC;";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	$row_count = $result->numRows();
	$page_count = (int)$row_count/$page_limiter;
	
	for($i = 0;$i <= $page_count;$i++) {
		$pages_array[] = '<a href="?sect=orders&mode=recent_orders&page_val='.($i*$page_limiter).'">'.(isset($_GET['page_val']) ? $_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '' : '').($i+1).(isset($_GET['page_val']) ? $_GET['page_val'] == $i*$page_limiter ? '</font>' : '' : '').'</a>';
	}
	
	$pages_links = implode(', ',$pages_array);
	
	$sql_query = "SELECT 
					DISTINCT date_added, 
					order_id, 
					token
				 FROM 
				 	certificate_orders
				 WHERE
					DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= date_added
				 ORDER BY date_added";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	$total_payments = 0;
	while($orders = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		if($orders['order_id'] > 0) {
		  $odrs_tbl->get_db_vars($orders['order_id']);
		  $total_payments += $odrs_tbl->order_total;
		} else {
		  $pp_pmts_tbl->assign_db_vars_token($orders['token']);
		  $total_payments += $pp_pmts_tbl->amount;
		}
	}
	
	$total_cnt = $row_count;
	
	$orders_listing .= draw_table_contect(array('<strong>Processed Total:</strong> '.number_format($total_cnt).'<br> <strong>Payments Total:</strong> $'.number_format($total_payments)),$table_boxes_cnt,'center');
	
	$orders_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	
	$orders_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
	
  return $orders_listing;
  }
  

  // display all orders listing
  function all_orders_listing($message = '') {
	$all_orders_view = open_table_listing_form('All Orders Listing','view_page','','post',$message);
	$all_orders_view .= $this->all_orders_listing_content();
	$all_orders_view .= close_table_form();
  return $all_orders_view;
  }
  
  function all_orders_listing_content() {
	global $dbh, $customer_info_table, $pp_pmts_tbl, $odrs_tbl;
	
	// set current page session var
	$_SESSION['previous_page'] = curPageURL();

	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'Order Date/Time',
						'Email Address',
						'Order Total',
						'Payment Type',
						'Delete Order',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);
						
	// print title boxes
	$orders_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT 
					DISTINCT date_added, 
					order_id, 
					token
				 FROM 
				 	certificate_orders
				 ORDER BY
					date_added DESC";
						
	if (!empty($_GET['page_val'])) {
	$sql_query .= "
			LIMIT ".$_GET['page_val'].",".$page_limiter."  ";
	} else {
	$sql_query .= "
			LIMIT
			".$page_limiter." ";
	}
	$sql_query .= ";";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	
	while($orders = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
		if($orders['order_id'] > 0) {
		  $odrs_tbl->get_db_vars($orders['order_id']);
	  
		  // pull customer data
		  $customer_info_table->get_db_vars($odrs_tbl->customer_id);
	  
		  $row_array = array(
							  '<a href="'.SITE_ADMIN_SSL_URL.'?sect=orders&mode=view_order&oid='.$odrs_tbl->id.'">'.date('m/d/Y g:iA',strtotime($odrs_tbl->date_added)).'</a>',
							  '<a href="'.SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=edit&cid='.$odrs_tbl->customer_id.'">'.$customer_info_table->email_address.'</a>',
							  $odrs_tbl->order_total,
							  $odrs_tbl->credit_card_type,
							  '<input name="delete_orders[]" type="checkbox" value="'.$odrs_tbl->id.'">',
							  );
		} else {
		  $pp_pmts_tbl->assign_db_vars_token($orders['token']);
	  	  
		  $row_array = array(
							  '<a href="'.SITE_ADMIN_SSL_URL.'?sect=orders&mode=view_order&pid='.$orders['token'].'">'.date('m/d/Y g:iA',strtotime($orders['date_added'])).'</a>',
							  'PayPal',
							  $pp_pmts_tbl->amount,
							  '',
							  '',
							  );
		}
		$orders_listing .= draw_table_contect($row_array,0,'center');
	}
	
	$sql_query = "SELECT 
					DISTINCT date_added, 
					order_id, 
					token
				 FROM 
				 	certificate_orders
				 ORDER BY
					date_added DESC;";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	$row_count = $result->numRows();
	
//	$row_count = count($row_count);
	$page_count = (int)$row_count/$page_limiter;
	
	for($i = 0;$i <= $page_count;$i++) {
		$pages_array[] = '<a href="?sect=orders&mode=all_orders&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
	}
	
	$pages_links = implode(', ',$pages_array);
	
	$sql_query = "SELECT 
					DISTINCT date_added, 
					order_id, 
					token
				 FROM 
				 	certificate_orders
				 ORDER BY date_added;";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	$total_payments = 0;
	while($orders = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		if($orders['order_id'] > 0) {
		  $odrs_tbl->get_db_vars($orders['order_id']);
		  $total_payments += $odrs_tbl->order_total;
		} else {
		  $pp_pmts_tbl->assign_db_vars_token($orders['token']);
		  $total_payments += $pp_pmts_tbl->amount;
		}
	}
	
	$total_cnt = $row_count;
	
	$orders_listing .= draw_table_contect(array('<strong>Processed Total:</strong> '.number_format($total_cnt).'<br> <strong>Payments Total:</strong> $'.number_format($total_payments)),$table_boxes_cnt,'center');
	
	$orders_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	
	$orders_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
	
  return $orders_listing;
  }
	  
  // display active_certificates listing
  function active_certificates_listing($message = '') {
	$all_orders_view = open_table_listing_form('Active Certificates Listing','view_page','','post',$message,6);
	$all_orders_view .= $this->active_certificates_listing_content();
	$all_orders_view .= close_table_form();
  return $all_orders_view;
  }
  
  function active_certificates_listing_content() {
	global $dbh, $customer_info_table, $cert_amt_tbl, $adv_info_tbl;
	
	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'Order Date/Time',
						'Advertiser Name',
						'Customer Name',
						'Requirements & Code',
						'Disable',
						'Delete Certificate',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);
						
	// print title boxes
	$orders_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					order_id,
					customer_id,
					advertiser_id,
					requirements,
					certificate_amount_id,
					certificate_code,
					enabled,
					date_added,
					cert_id
				 FROM
					certificate_orders
				 WHERE
					enabled = 1
				 ORDER BY
					date_added DESC
				 ";
						
	if (!empty($_GET['page_val'])) {
	$sql_query .= "
			LIMIT ".$_GET['page_val'].",".$page_limiter."  ";
	} else {
	$sql_query .= "
			LIMIT
			".$page_limiter." ";
	}
	$sql_query .= ";";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	
	while($orders = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
		// pull customer data
		$customer_info_table->get_db_vars($orders['customer_id']);
		$customer_name = $customer_info_table->first_name . ' ' . $customer_info_table->last_name;
		
		$cert_amt_tbl->get_db_vars($orders['certificate_amount_id']);
		
		$adv_info_tbl->get_db_vars($orders['advertiser_id']);
	
		$row_array = array(
							'<a target="_blank" href="'.SITE_URL.'customer_admin/view_certificate.deal?cert_id='.$orders['cert_id'].'">'.date('m/d/Y g:iA',strtotime($orders['date_added'])).'</a>',
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=edit&cid='.$orders['advertiser_id'].'">'.$adv_info_tbl->company_name.'</a>',
							$customer_name,
							$orders['requirements'].' <br>Code: '.$orders['certificate_code'].' <br>Cost: $'.$cert_amt_tbl->cost.' Amount: $'.$cert_amt_tbl->discount_amount,
							'<a href="?sect=orders&mode=active_certificates_listing&action=disable&cid='.$orders['id'].(isset($_GET['page_val']) ? '&page_val='.$_GET['page_val'] : '').'">Disable</a>',
							'<input name="delete_certificates[]" type="checkbox" value="'.$orders['id'].'">',
							);
	
		$orders_listing .= draw_table_contect($row_array,0,'center');
					
	}
	
	$sql_query = "SELECT
					count(*) as rcount
				 FROM
					certificate_orders ;";
	
	$rowscount = $dbh->queryRow($sql_query);
	
	$row_count = $rowscount['rcount'];
	$page_count = (int)$row_count/$page_limiter;
	
	for($i = 0;$i <= $page_count;$i++) {
		$pages_array[] = '<a href="?sect=orders&mode=active_certificates_listing&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
	}
	
	$pages_links = implode(', ',$pages_array);
	
	$orders_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	
	$orders_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
	
  return $orders_listing;
  }
	  
  // display active_certificates listing
  function inactive_certificates_listing($message = '') {
	$all_orders_view = open_table_listing_form('Inactive Certificates Listing','view_page','','post',$message,6);
	$all_orders_view .= $this->inactive_certificates_listing_content();
	$all_orders_view .= close_table_form();
  return $all_orders_view;
  }
  
  function inactive_certificates_listing_content() {
	global $dbh, $customer_info_table, $cert_amt_tbl, $adv_info_tbl;
	
	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'Order Date/Time',
						'Advertiser Name',
						'Customer Name',
						'Requirements & Code',
						'Enable',
						'Delete Certificate',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);
						
	// print title boxes
	$orders_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					order_id,
					customer_id,
					advertiser_id,
					requirements,
					certificate_amount_id,
					certificate_code,
					enabled,
					date_added,
					cert_id
				 FROM
					certificate_orders
				 WHERE
					enabled = 0
				 ORDER BY
					date_added DESC
				 ";
						
	if (!empty($_GET['page_val'])) {
	$sql_query .= "
			LIMIT ".$_GET['page_val'].",".$page_limiter."  ";
	} else {
	$sql_query .= "
			LIMIT
			".$page_limiter." ";
	}
	$sql_query .= ";";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	
	while($orders = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
		// pull customer data
		$customer_info_table->get_db_vars($orders['customer_id']);
		$customer_name = $customer_info_table->first_name . ' ' . $customer_info_table->last_name;
		
		$cert_amt_tbl->get_db_vars($orders['certificate_amount_id']);
	
		$adv_info_tbl->get_db_vars($orders['advertiser_id']);
	
		$row_array = array(
							'<a target="_blank" href="'.SITE_URL.'customer_admin/view_certificate.deal?cert_id='.$orders['cert_id'].'">'.date('m/d/Y g:iA',strtotime($orders['date_added'])).'</a>',
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=edit&cid='.$orders['advertiser_id'].'">'.$adv_info_tbl->company_name.'</a>', 
							$customer_name,
							$orders['requirements'].' <br>Code: '.$orders['certificate_code'].' <br>Cost: $'.$cert_amt_tbl->cost.' Amount: $'.$cert_amt_tbl->discount_amount,
							'<a href="?sect=orders&mode=inactive_certificates_listing&action=enable&cid='.$orders['id'].(isset($_GET['page_val']) ? '&page_val='.$_GET['page_val'] : '').'">Enable</a>',
							'<input name="delete_certificates[]" type="checkbox" value="'.$orders['id'].'">',
							);
	
		$orders_listing .= draw_table_contect($row_array,0,'center');
		
	}
	
	$sql_query = "SELECT
					count(*) as rcount
				 FROM
					certificate_orders
				 WHERE
					enabled = 0 ;";
	
	$rowscount = $dbh->queryRow($sql_query);
	
	$row_count = $rowscount['rcount'];
	$page_count = (int)$row_count/$page_limiter;
	
	for($i = 0;$i <= $page_count;$i++) {
		$pages_array[] = '<a href="?sect=orders&mode=inactive_certificates_listing&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
	}
	
	$pages_links = implode(', ',$pages_array);
	
	$orders_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	
	$orders_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
	
  return $orders_listing;
  }
  
  // display recent orders listing
  function processed_advertiser_mems_listing($message = '') {
	$recent_orders_view = open_table_listing_form('Processed Members Listing','view_page','','post',$message,6);
	$recent_orders_view .= $this->processed_advertiser_mems_content();
	$recent_orders_view .= close_table_form();
  return $recent_orders_view;
  }
  
  function processed_advertiser_mems_content() {
	global $dbh, $customer_info_table, $adv_info_tbl;
	
	// set current page session var
	$_SESSION['previous_page'] = curPageURL();
	
	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'Date',
						'Advertiser Company',
						'Advertiser Name',
						'Payment',
						'CC Type',
						'Payment Approved',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);
						
	// print title boxes
	$orders_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					advertiser_id,
					date,
					payment,
					cc_type,
					payment_approved,
					other_info
				 FROM
					membership_process
				 ORDER BY
					date DESC";
						
	if (!empty($_GET['page_val'])) {
	$sql_query .= "
			LIMIT ".$_GET['page_val'].",".$page_limiter."  ";
	} else {
	$sql_query .= "
			LIMIT
			".$page_limiter." ";
	}
	$sql_query .= ";";
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	
	while($orders = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
		// pull customer data
		$adv_info_tbl->get_db_vars($orders['advertiser_id']);
		$customer_name = $adv_info_tbl->first_name . ' ' . $adv_info_tbl->last_name;
	
		$row_array = array(
							date('m/d/Y',strtotime($orders['date'])),
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=edit&cid='.$orders['advertiser_id'].'">'.$adv_info_tbl->company_name.'</a>',
							$customer_name,
							'$'.$orders['payment'],
							$orders['cc_type'],
//								$orders['other_info'],
							($orders['payment_approved'] == 1 ? 'Y' : 'N'),
							);
	
		$orders_listing .= draw_table_contect($row_array,0,'center');
		
	}
	
	$sql_query = "SELECT
					count(*) as rcount
				 FROM
					membership_process;";
	
	$rowscount = $dbh->queryRow($sql_query);
	
	$row_count = $rowscount['rcount'];
	$page_count = (int)$row_count/$page_limiter;
	
	for($i = 0;$i <= $page_count;$i++) {
		$pages_array[] = '<a href="?sect=orders&mode=processed_advertiser_mems&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
	}
	
	$pages_links = implode(', ',$pages_array);
	
	// get overall sales stats
	$sql_query = "SELECT
					sum(payment) as total_mems
				 FROM
					membership_process
				;";
	
	$rows = $dbh->queryRow($sql_query);
	$total_payments = $rows['total_mems'];
	
	$sql_query = "SELECT
					count(id) as total_cnt
				 FROM
					membership_process
				;";
	
	$rows = $dbh->queryRow($sql_query);
	$total_cnt = $rows['total_cnt'];
		
	// get monthly sales state
	$sql_query = "SELECT
					sum(payment) as total_mems
				 FROM
					membership_process
				 WHERE
					extract(month from date) = ".date("m")."
				;";
	
	$rows = $dbh->queryRow($sql_query);
	$month_total_payments = $rows['total_mems'];
	
	$sql_query = "SELECT
					count(id) as total_cnt
				 FROM
					membership_process
				 WHERE
					extract(month from date) = ".date("m")."
				;";
	
	$rows = $dbh->queryRow($sql_query);
	$month_total_cnt = $rows['total_cnt'];
		
	// get last months sales state
	$sql_query = "SELECT
					sum(payment) as total_mems
				 FROM
					membership_process
				 WHERE
					extract(month from date) = ".date("m",strtotime("-1 month"))."
				;";
	
	$rows = $dbh->queryRow($sql_query);
	$last_month_total_payments = $rows['total_mems'];
	
	$sql_query = "SELECT
					count(id) as total_cnt
				 FROM
					membership_process
				 WHERE
					extract(month from date) = ".date("m",strtotime("-1 month"))."
				;";
	
	$rows = $dbh->queryRow($sql_query);
	$last_month_total_cnt = $rows['total_cnt'];

	$orders_listing .= draw_table_contect(array('<strong>Over All</strong><br> 
	<strong>Processed Total:</strong> '.number_format($total_cnt).'<br> 
	<strong>Payments Total:</strong> $'.number_format($total_payments).'<br><br>
	<strong>This Month</strong> <br>		
	<strong>Processed Total:</strong> '.number_format($month_total_cnt).'<br> 
	<strong>Payments Total:</strong> $'.number_format($month_total_payments).'<br><br>
	<strong>Last Month</strong> <br>		
	<strong>Processed Total:</strong> '.number_format($last_month_total_cnt).'<br> 
	<strong>Payments Total:</strong> $'.number_format($last_month_total_payments).'<br>
'),$table_boxes_cnt,'center');

	$orders_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	
	$orders_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
	
  return $orders_listing;
  }

}

?>