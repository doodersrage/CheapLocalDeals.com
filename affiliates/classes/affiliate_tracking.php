<?PHP

// this class is used for browsing and printing affiliate tracking
class affiliate_tracking {

	// display affiliates listing
	function affiliates_tracking_listing($message = '') {
		$page_view = open_table_listing_form('Affiliate Tracking Listing<br>(Click name for advertiser signup listing)','view_affiliates','','post',$message);
		$page_view .= $this->affiliates_tracking_listing_content();
		$page_view .= close_table_form();
		
	return $page_view;
	}
	
	function affiliates_tracking_listing_content() {
			global $dbh, $pgs_tbl;
		
		// table title array							
		$title_array = array(
							'Affiliate Name',
							'Company',
							'Affiliate Code',
							);
				
		// gets table boxes count
		$table_boxes_cnt = count($title_array);
							
		// print title boxes
		$pages_listing .= draw_table_header($title_array);	
		
		$sql_query = "SELECT
						id,
						name,
						affiliate_code,
						company
					 FROM
						affiliate_users
					;";
		
		$rows = $dbh->queryAll($sql_query);
		
		foreach ($rows as $pages) {
		
			$row_array = array(
								'<a href="'.SITE_AFFILIATE_SSL_URL.'?sect=affiliatesreports&mode=advertiserlist&pid='.$pages['affiliate_code'].'">'.$pages['name'].'</a>',
								$pages['company'],
								$pages['affiliate_code'],
								);
		
			$pages_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
	return $pages_listing;
	}

	// display affiliates advertisers listing
	function affiliates_advertisers_listing($message = '') {
		$page_view = open_table_listing_form('Affiliate Advertisers Listing','view_affiliates_advertisers','','post',$message,6);
		$page_view .= $this->affiliates_advertisers_listing_content();
		$page_view .= close_table_form();
		
	return $page_view;
	}
	
	function affiliates_advertisers_listing_content() {
			global $dbh, $adv_lvls_tbl;
		
		// table title array							
		$title_array = array(
							'#',
							'Sign Up Date',
							'Company Name',
							'Plan',
							'Paid So Far',
							'Gift Certificates Sum',
							);
				
		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// table title array							
		$filter_title_array = array(
							'',
							'',
							'Start Date: <script>DateInput(\'start_date\', true, \'YYYY-MM-DD\',\''.(!empty($_POST['start_date']) ? $_POST['start_date'] : date("Y-m-d")).'\')</script>',
							'End Date: <script>DateInput(\'end_date\', true, \'YYYY-MM-DD\',\''.(!empty($_POST['end_date']) ? $_POST['end_date'] : date("Y-m-d")).'\')</script>',
							'<input type="submit" name="submit" value="Filter Results">',
							''
							);
							
		// print title boxes
		$pages_listing .= draw_table_header($filter_title_array,$table_boxes_cnt,'center');	
							
		// print title boxes
		$pages_listing .= draw_table_header($title_array);	
		
		$sql_query = "SELECT
						id,
						company_name,
						customer_level,
						date_created
					 FROM
						advertiser_info
					 WHERE
						link_affiliate_code = '".$_GET['pid']."' 
					 ";
		if (!empty($_POST['start_date'])) {
		$sql_query .= "AND
					 	date_created >= '".$_POST['start_date']."'
					 AND
					 	date_created <= '".$_POST['end_date']."' ";
		}
		$sql_query .= "ORDER BY date_created DESC
					;";
		
		$rows = $dbh->queryAll($sql_query);
		
		$paid_sum = 0;
		$cert_sum = 0;
		$count = 0;
		
		foreach ($rows as $pages) {
			// load selected plan details
			$adv_lvls_tbl->get_db_vars($pages['customer_level']);
			// get advertiser paid so far
			$sql_query = "SELECT
							sum(payment) as payment_amt
						 FROM
							membership_process
						 WHERE
							advertiser_id = '".$pages['id']."'
						 ;";
			$rowsa = $dbh->queryRow($sql_query);
			// get certificates sold count
			$sql_query = "SELECT
							count(cm.cost) as cost
						 FROM
							certificate_orders co LEFT JOIN certificate_amount cm ON co.certificate_amount_id = cm.id
						 WHERE
							co.advertiser_id = '".$pages['id']."'
						 ;";
			$rowsb = $dbh->queryRow($sql_query);
		
			$paid_sum += $rowsa['payment_amt'];
			$cert_sum += $rowsb['cost'];
			$count++;
		
			$row_array = array(
								$count,
								date('n/j/Y h:i:s A',strtotime($pages['date_created'])),
								'<a href="'.SITE_AFFILIATE_SSL_URL.'?sect=affiliatesreports&mode=advertiserlistreport&pid='.$_GET['pid'].'&advertid='.$pages['id'].'">'.$pages['company_name'].'</a>',
								$adv_lvls_tbl->level_name,
								(!empty($rowsa['payment_amt']) ? '$'.$rowsa['payment_amt'] : ''),
								'$'.$rowsb['cost'],
								);
		
			$pages_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
			$row_array = array(
								'',
								'',
								'Totals:',
								'$'.format_currency($paid_sum),
								'$'.format_currency($cert_sum),
								);
		
			$pages_listing .= draw_table_contect($row_array,0,'center');

		
		// table title array							
		$title_array = array(
							'<a href="'.SITE_AFFILIATE_SSL_URL.'?sect=affiliatesreports&mode=advertiserlist&pid='.$_GET['pid'].'&action=download_xls'.(!empty($_POST['start_date']) ? '&start_date='.urlencode($_POST['start_date']).'&end_date='.urlencode($_POST['end_date']) : '' ).'">Save as Excel Spreadsheet</a>',
							);
							
		// print title boxes
		$pages_listing .= draw_table_header($title_array,$table_boxes_cnt,'center');	
		
	return $pages_listing;
	}

	// display affiliates advertisers listing
	function affiliates_advert_tracking_listing($message = '') {
		$page_view = open_table_listing_form('Affiliate Advert Cert Sales Listing','view_affiliates_advert_advertisers','','post',$message);
		$page_view .= $this->affiliates_advert_tracking_content();
		$page_view .= close_table_form();
		
	return $page_view;
	}
	
	function affiliates_advert_tracking_content() {
			global $dbh, $adv_lvls_tbl, $cert_amt_tbl;
		
		// table title array							
		$title_array = array(
							'Sign Up Date',
							'Company Name',
							'Plan',
							'Paid So Far',
							'Gift Certificates Sum',
							);
				
		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// table title array							
		$filter_title_array = array(
							'',
							'Start Date: <script>DateInput(\'start_date\', true, \'YYYY-MM-DD\',\''.(!empty($_POST['start_date']) ? $_POST['start_date'] : date("Y-m-d")).'\')</script>',
							'End Date: <script>DateInput(\'end_date\', true, \'YYYY-MM-DD\',\''.(!empty($_POST['end_date']) ? $_POST['end_date'] : date("Y-m-d")).'\')</script>',
							'<input type="submit" name="submit" value="Filter Results">',
							''
							);
							
		// print title boxes
		$pages_listing .= draw_table_header($filter_title_array,$table_boxes_cnt,'center');	
							
		// print title boxes
		$pages_listing .= draw_table_header($title_array);	
		
		$sql_query = "SELECT
						id,
						company_name,
						customer_level,
						date_created
					 FROM
						advertiser_info
					 WHERE
						link_affiliate_code = '".$_GET['pid']."'
					 AND
						id = '".$_GET['advertid']."'
					 ORDER BY date_created DESC
					;";
		
		$rows = $dbh->queryAll($sql_query);
		
		foreach ($rows as $pages) {
			
			$page_link = '?sect=affiliatesreports&mode=advertiserlistreport&pid='.$_GET['pid'].'&advertid='.$pages['id'];
			// load selected plan details
			$adv_lvls_tbl->get_db_vars($pages['customer_level']);
			// get advertiser paid so far
			$sql_query = "SELECT
							sum(payment) as payment_amt
						 FROM
							membership_process
						 WHERE
							advertiser_id = '".$pages['id']."'
						 ;";
			$rowsa = $dbh->queryRow($sql_query);
			// get certificates sold count
			$sql_query = "SELECT
							count(cm.cost) as cost
						 FROM
							certificate_orders co LEFT JOIN certificate_amount cm ON co.certificate_amount_id = cm.id
						 WHERE
							co.advertiser_id = '".$pages['id']."'
						 ;";
			$rowsb = $dbh->queryRow($sql_query);
			
			$row_array = array(
								date('n/j/Y h:i:s A',strtotime($pages['date_created'])),
								'<a href="'.SITE_AFFILIATE_SSL_URL.'?sect=affiliatesreports&mode=advertiserlistreport&pid='.$_GET['pid'].'&advertid='.$pages['id'].'">'.$pages['company_name'].'</a>',
								$adv_lvls_tbl->level_name,
								(!empty($rowsa['payment_amt']) ? '$'.$rowsa['payment_amt'] : ''),
								'$'.$rowsb['cost'],
								);
		
			$pages_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
		$sql_query = "SELECT
						id,
						customer_id,
						advertiser_id,
						requirements,
						certificate_amount_id,
						certificate_code,
						date_added
					 FROM
						certificate_orders
					 WHERE
						advertiser_id = '".$_GET['advertid']."'
					 ";
		if (!empty($_POST['start_date'])) {
		$sql_query .= "AND
					 	date_added >= '".$_POST['start_date']."'
					 AND
					 	date_added <= '".$_POST['end_date']."' ";
		}
		$sql_query .= "ORDER BY date_created DESC
					;";
		
		$rows = $dbh->queryAll($sql_query);
		
		if (count($rows) > 0) {
			
			// table title array							
			$title_array = array(
								'Date Purchased',
								'Certificate Amount',
								'Certificate Cost',
								'Requirements',
								'Certificate Code',
								);
								
			// print title boxes
			$pages_listing .= draw_table_header($title_array);	
			
			foreach($rows as $certs){
				$cert_amt_tbl->get_db_vars($certs['certificate_amount_id']);
				
				$row_array = array(
									date('n/j/Y h:i:s A',strtotime($certs['date_added'])),
									$cert_amt_tbl->discount_amount,
									$cert_amt_tbl->cost,
									$certs['requirements'],
									$certs['certificate_code'],
									);
			
				$pages_listing .= draw_table_contect($row_array,0,'center');
				
			}
			
		} else {
			// table title array							
			$title_array = array(
								'No certificate Sales',
								);
										
			// print title boxes
			$pages_listing .= draw_table_header($title_array,5,'center');				
		}
		// table title array							
		$title_array = array(
							'<a href="'.SITE_AFFILIATE_SSL_URL.$page_link.'&action=download_xls'.(!empty($_POST['start_date']) ? '&start_date='.urlencode($_POST['start_date']).'&end_date='.urlencode($_POST['end_date']) : '' ).'">Save as Excel Spreadsheet</a>',
							);
									
		// print title boxes
		$pages_listing .= draw_table_header($title_array,5,'center');	
		
	return $pages_listing;
	}

}

?>