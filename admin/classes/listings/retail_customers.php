<?PHP

// retail customers management class
class retail_customers_lst {
	
	// display retail customers listing
	function retail_customers_paymentprob($message = '') {
		$retail_customers_view = open_table_listing_form('Advertisers Missing Payment Data Listing','view_retail_customers',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=view','post',$message);
		$retail_customers_view .= $this->retail_customers_paymentprob_content();
		$retail_customers_view .= close_table_form();
		
		return $retail_customers_view;
	}
		
	// list retail customers
	function retail_customers_paymentprob_content() {
			global $dbh, $adv_info_tbl;
			
		// set sort option
		if (!empty($_GET['sort'])) $_SESSION['advert_sort'] = $_GET['sort'];
		
		$sql_query = "SELECT
						id
					 FROM
						advertiser_info
					 WHERE 
						approved = 1
					 ";
		
		
		// add search lines to page query
		if(!empty($_POST['search_box'])) {
			
			$search_terms = explode(" ",$_POST['search_box']);
			
			$search_query = array();
							
			foreach ($search_terms as $cur_term) {
				$search_query[] = "(company_name LIKE
						'%".str_replace("'","''",$cur_term)."%' OR username LIKE '%".str_replace("'","''",$cur_term)."%' OR last_name LIKE '%".str_replace("'","''",$cur_term)."%')";
			}
			$sql_query .= " AND ".implode(" AND ",$search_query);
		}
		
		// sets output order by
		if(isset($_SESSION['advert_sort'])) {
			switch($_SESSION['advert_sort']) {
			case 'cnameasc':
				$order_sel = 'company_name ASC';
			break;
			case 'cnamedesc':
				$order_sel = 'company_name DESC';
			break;
			case 'unameasc':
				$order_sel = 'username ASC';
			break;
			case 'unamedesc':
				$order_sel = 'username DESC';
			break;
			case 'clnasc':
				$order_sel = 'last_name ASC';
			break;
			case 'clndesc':
				$order_sel = 'last_name DESC';
			break;
			case 'updatedasc':
				$order_sel = 'date_updated ASC';
			break;
			case 'updateddesc':
				$order_sel = 'date_updated DESC';
			break;
			}
			
			$sql_query .= "
					ORDER BY ".$order_sel;
		
		} else {
			$sql_query .= "
					ORDER BY
						company_name ASC ";
		}
							
		$sql_query .= ";";
		
		// reset selected array
		$selected_ads = array();
			
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute();
		
		while($retail_customers = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			// filters results page for advertisers with payment issues
			$adv_info_tbl->get_db_vars($retail_customers['id']);
			if ($adv_info_tbl->customer_level != 3) {
				if(empty($adv_info_tbl->payment_method)) {
				  $selected_ads[] = $retail_customers['id'];
				}
			}
		}
		
		// sets record limit per page	
		$page_limiter = ADMIN_PER_PAGE_RESULTS; 
		
		// table title array							
		$title_array = array(
							'Company Name <br><a href="?sect=retcustomer&mode=view&sort=cnameasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=cnamedesc">DESC</a>',
							'Username <br><a href="?sect=retcustomer&mode=view&sort=unameasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=unamedesc">DESC</a>',
							'Contact Last Name <br><a href="?sect=retcustomer&mode=view&sort=clnasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=clndesc">DESC</a>',
							'Updated <br><a href="?sect=retcustomer&mode=view&sort=updatedasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=updateddesc">DESC</a>',
							'Delete<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_advertiser\').attr(\'checked\', \'checked\')">Select All</a>',
							);
		
		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// draw table header
		$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
		$retail_customers_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
							
		// print title boxes
		$retail_customers_listing .= draw_table_header($title_array);		
		
		foreach ($selected_ads as $cur_ad) {
		// filters results page for advertisers with payment issues
		$adv_info_tbl->get_db_vars($cur_ad);
		
			$row_array = array(
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=edit&cid='.$adv_info_tbl->id.'">'.$adv_info_tbl->company_name.'</a>',
								$adv_info_tbl->username,
								$adv_info_tbl->last_name,
								date('n/j/Y h:i:s A',strtotime($adv_info_tbl->date_updated)),
								'<input class="delete_advertiser" name="delete_advertiser[]" type="checkbox" value="'.$adv_info_tbl->id.'">',
								);
		
			$retail_customers_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
		$retail_customers_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Perform Selected"></center>'),$table_boxes_cnt,'center');
		
		return $retail_customers_listing;
	}
	
	// display retail customers listing
	function retail_customers_change_pending_approval_listing($message = '') {
		$retail_customers_view = open_table_listing_form('Changed Advertisers Listing','view_retail_customers',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=change_pending_approval','post',$message);
		$retail_customers_view .= $this->retail_customers_change_pending_approval_content();
		$retail_customers_view .= close_table_form();
		
		return $retail_customers_view;
	}
		
	// list retail customers	
	function retail_customers_change_pending_approval_content() {
			global $dbh;
			
		// sets record limit per page	
		$page_limiter = ADMIN_PER_PAGE_RESULTS; 
		
		// table title array							
		$title_array = array(
							'Company Name <br><a href="?sect=retcustomer&mode=change_pending_approval&sort=cnameasc">ASC</a> <a href="?sect=retcustomer&mode=change_pending_approval&sort=cnamedesc">DESC</a>',
							'Username <br><a href="?sect=retcustomer&mode=change_pending_approval&sort=unameasc">ASC</a> <a href="?sect=retcustomer&mode=change_pending_approval&sort=unamedesc">DESC</a>',
							'Contact Last Name <br><a href="?sect=retcustomer&mode=change_pending_approval&sort=clnasc">ASC</a> <a href="?sect=retcustomer&mode=change_pending_approval&sort=clndesc">DESC</a>',
							'Approve<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].change_approve_advertiser\').attr(\'checked\', \'checked\')">Select All</a>',
							'Delete<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_advertiser\').attr(\'checked\', \'checked\')">Select All</a>',
							);
		
		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// draw table header
		$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
		$retail_customers_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
		
		// prints page links
		if (empty($_POST['search_box'])) {
			$sql_query = "SELECT
							count(*) as rcount
						 FROM
							advertiser_info
						WHERE
							update_approval = 0 OR update_approval is null
						;";
					
		
		// add search lines to page query
		if(!empty($_POST['search_box'])) {
			
			$search_terms = explode(" ",$_POST['search_box']);
			
			$search_query = array();
							
			foreach ($search_terms as $cur_term) {
				$search_query[] = "(company_name LIKE
						'%".str_replace("'","''",$cur_term)."%' OR username LIKE '%".str_replace("'","''",$cur_term)."%' OR last_name LIKE '%".str_replace("'","''",$cur_term)."%')";
			}
			$sql_query .= " AND ".implode(" AND ",$search_query);
		}
			
			$rowscount = $dbh->queryRow($sql_query);
			
			$row_count = $rowscount['rcount'];
			$page_count = (int)$row_count/$page_limiter;
			
			for($i = 0;$i <= $page_count;$i++) {
				$pages_array[] = '<a href="?sect=retcustomer&mode=change_pending_approval&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
			}
			
			$pages_links = implode(', ',$pages_array);
			
			$retail_customers_listing .= draw_table_contect(array('Total Advertisers: '.number_format($row_count).'<br><center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		}
							
		// print title boxes
		$retail_customers_listing .= draw_table_header($title_array);	
		
		// set sort option
		if (!empty($_GET['sort'])) $_SESSION['advert_sort'] = $_GET['sort'];
		
		$sql_query = "SELECT
						id,
						company_name,
						username,
						last_name,
						date_updated
					 FROM
						advertiser_info
					 WHERE
						update_approval = 0 OR update_approval is null
						";
		
		
		// add search lines to page query
		if(!empty($_POST['search_box'])) {
			
			$search_terms = explode(" ",$_POST['search_box']);
			
			$search_query = array();
							
			foreach ($search_terms as $cur_term) {
				$search_query[] = "(company_name LIKE
						'%".str_replace("'","''",$cur_term)."%' OR username LIKE '%".str_replace("'","''",$cur_term)."%' OR last_name LIKE '%".str_replace("'","''",$cur_term)."%')";
			}
			$sql_query .= " AND ".implode(" AND ",$search_query);
		}
		
		// sets output order by
		if(isset($_SESSION['advert_sort'])) {
			switch($_SESSION['advert_sort']) {
			case 'cnameasc':
				$order_sel = 'company_name ASC';
			break;
			case 'cnamedesc':
				$order_sel = 'company_name DESC';
			break;
			case 'unameasc':
				$order_sel = 'username ASC';
			break;
			case 'unamedesc':
				$order_sel = 'username DESC';
			break;
			case 'clnasc':
				$order_sel = 'last_name ASC';
			break;
			case 'clndesc':
				$order_sel = 'last_name DESC';
			break;
			case 'updatedasc':
				$order_sel = 'date_updated ASC';
			break;
			case 'updateddesc':
				$order_sel = 'date_updated DESC';
			break;
			case 'addedasc':
				$order_sel = 'date_created ASC';
			break;
			case 'addeddesc':
				$order_sel = 'date_created DESC';
			break;
			}
			
			$sql_query .= "
					ORDER BY ".$order_sel;
		
		} else {
			$sql_query .= "
					ORDER BY
						company_name ASC ";
		}		
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
		
		while($retail_customers = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
			$row_array = array(
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=edit&cid='.$retail_customers['id'].'">'.$retail_customers['company_name'].'</a>',
								$retail_customers['username'],
								$retail_customers['last_name'],
								'<input class="change_approve_advertiser" name="change_approve_advertiser[]" type="checkbox" value="'.$retail_customers['id'].'">',
								'<input class="delete_advertiser" name="delete_advertiser[]" type="checkbox" value="'.$retail_customers['id'].'">',
								);
		
			$retail_customers_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
		// print page links
		$retail_customers_listing .= draw_table_contect(array('Total Advertisers: '.number_format($row_count).'<br><center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		
		$retail_customers_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Update Selected"></center>'),$table_boxes_cnt,'center');
		
		return $retail_customers_listing;
	}
	
	// display retail customers listing
	function retail_customers_pending_approval_listing($message = '') {
		$retail_customers_view = open_table_listing_form('Advertisers Pending Approval Listing','view_retail_customers',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=pending_approval','post',$message);
		$retail_customers_view .= $this->retail_customers_pending_approval_content();
		$retail_customers_view .= close_table_form();
		
		return $retail_customers_view;

	}
		
	// list retail customers	
	function retail_customers_pending_approval_content() {
			global $dbh;
			
		// sets record limit per page	
		$page_limiter = ADMIN_PER_PAGE_RESULTS; 
		
		// table title array							
		$title_array = array(
							'Company Name <br><a href="?sect=retcustomer&mode=pending_approval&sort=cnameasc">ASC</a> <a href="?sect=retcustomer&mode=pending_approval&sort=cnamedesc">DESC</a>',
							'Username <br><a href="?sect=retcustomer&mode=pending_approval&sort=unameasc">ASC</a> <a href="?sect=retcustomer&mode=pending_approval&sort=unamedesc">DESC</a>',
							'Contact Last Name <br><a href="?sect=retcustomer&mode=pending_approval&sort=clnasc">ASC</a> <a href="?sect=retcustomer&mode=pending_approval&sort=clndesc">DESC</a>',
							'Date Added<br><a href="?sect=retcustomer&mode=pending_approval&sort=addedasc">ASC</a> <a href="?sect=retcustomer&mode=pending_approval&sort=addeddesc">DESC</a>',
							'Approve<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].approve_advertiser\').attr(\'checked\', \'checked\')">Select All</a>',
							'Delete<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_advertiser\').attr(\'checked\', \'checked\')">Select All</a>',
							);
		
		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// draw table header
		$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
		$retail_customers_listing = draw_table_header($searchbox_head,$table_boxes_cnt,'center');
		
		// prints page links
		if (empty($_POST['search_box'])) {
			$sql_query = "SELECT
							count(*) as rcount
						 FROM
							advertiser_info
						WHERE
							approved is NULL or approved = 0
						;";
					
		
		// add search lines to page query
		if(!empty($_POST['search_box'])) {
			
			$search_terms = explode(" ",$_POST['search_box']);
			
			$search_query = array();
							
			foreach ($search_terms as $cur_term) {
				$search_query[] = "(company_name LIKE
						'%".str_replace("'","''",$cur_term)."%' OR username LIKE '%".str_replace("'","''",$cur_term)."%' OR last_name LIKE '%".str_replace("'","''",$cur_term)."%')";
			}
			$sql_query .= " AND ".implode(" AND ",$search_query);
		}
			
			$rowscount = $dbh->queryRow($sql_query);
			
			$row_count = $rowscount['rcount'];
			$page_count = (int)$row_count/$page_limiter;
			
			for($i = 0;$i <= $page_count;$i++) {
				$pages_array[] = '<a href="?sect=retcustomer&mode=pending_approval&page_val='.($i*$page_limiter).'">'.(isset($_GET['page_val']) ? $_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '' : '').($i+1).(isset($_GET['page_val']) ? $_GET['page_val'] == $i*$page_limiter ? '</font>' : '' : '').'</a>';
			}
			
			$pages_links = implode(', ',$pages_array);
			
			$retail_customers_listing .= draw_table_contect(array('Total Advertisers: '.number_format($row_count).'<br><center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		}
							
		// print title boxes
		$retail_customers_listing .= draw_table_header($title_array);	
	
		// set sort option
		if (!empty($_GET['sort'])) $_SESSION['advert_sort'] = $_GET['sort'];
		
		$sql_query = "SELECT
						id,
						company_name,
						username,
						last_name,
						date_updated,
						date_created
					 FROM
						advertiser_info
					 WHERE
						approved is NULL or approved = 0
						";
		
		
		// add search lines to page query
		if(!empty($_POST['search_box'])) {
			
			$search_terms = explode(" ",$_POST['search_box']);
			
			$search_query = array();
							
			foreach ($search_terms as $cur_term) {
				$search_query[] = "(company_name LIKE
						'%".str_replace("'","''",$cur_term)."%' OR username LIKE '%".str_replace("'","''",$cur_term)."%' OR last_name LIKE '%".str_replace("'","''",$cur_term)."%')";
			}
			$sql_query .= " AND ".implode(" AND ",$search_query);
		}
		// sets output order by
		if(isset($_SESSION['advert_sort'])) {
			switch($_SESSION['advert_sort']) {
			case 'cnameasc':
				$order_sel = 'company_name ASC';
			break;
			case 'cnamedesc':
				$order_sel = 'company_name DESC';
			break;
			case 'unameasc':
				$order_sel = 'username ASC';
			break;
			case 'unamedesc':
				$order_sel = 'username DESC';
			break;
			case 'clnasc':
				$order_sel = 'last_name ASC';
			break;
			case 'clndesc':
				$order_sel = 'last_name DESC';
			break;
			case 'updatedasc':
				$order_sel = 'date_updated ASC';
			break;
			case 'updateddesc':
				$order_sel = 'date_updated DESC';
			break;
			case 'addedasc':
				$order_sel = 'date_created ASC';
			break;
			case 'addeddesc':
				$order_sel = 'date_created DESC';
			break;
			}
			
			$sql_query .= "
					ORDER BY ".$order_sel;
		
		} else {
			$sql_query .= "
					ORDER BY
						company_name ASC ";
		}
		
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
		
		while($retail_customers = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
			$row_array = array(
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=edit&cid='.$retail_customers['id'].'">'.$retail_customers['company_name'].'</a>',
								$retail_customers['username'],
								$retail_customers['last_name'],
								date('n/j/Y h:i:s A',strtotime($retail_customers['date_created'])),
								'<input class="approve_advertiser" name="approve_advertiser[]" type="checkbox" value="'.$retail_customers['id'].'">',
								'<input class="delete_advertiser" name="delete_advertiser[]" type="checkbox" value="'.$retail_customers['id'].'">',
								);
		
			$retail_customers_listing .= draw_table_contect($row_array,0,'center');
					
		}
		
		// prints page links
		$retail_customers_listing .= draw_table_contect(array('Total Advertisers: '.number_format($row_count).'<br><center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		
		$retail_customers_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Perform Selected"></center>'),$table_boxes_cnt,'center');
		
		return $retail_customers_listing;
	}
	
	// display retail customers listing
	function retail_customers_listing_new($message = '') {
		$retail_customers_view = open_table_listing_form('New Advertisers Listing','view_retail_customers',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=newcustomers','post',$message,6);
		$retail_customers_view .= $this->retail_customers_listing_new_content();
		$retail_customers_view .= close_table_form();
		
		return $retail_customers_view;
	}
		
	// list retail customers	
	function retail_customers_listing_new_content() {
			global $dbh;
			
		// sets record limit per page	
		$page_limiter = ADMIN_PER_PAGE_RESULTS; 
		
		// table title array							
		$title_array = array(
							'Company Name <br><a href="?sect=retcustomer&mode=newcustomers&sort=cnameasc">ASC</a> <a href="?sect=retcustomer&mode=newcustomers&sort=cnamedesc">DESC</a>',
							'Username <br><a href="?sect=retcustomer&mode=newcustomers&sort=unameasc">ASC</a> <a href="?sect=retcustomer&mode=newcustomers&sort=unamedesc">DESC</a>',
							'Contact Last Name <br><a href="?sect=retcustomer&mode=newcustomers&sort=clnasc">ASC</a> <a href="?sect=retcustomer&mode=newcustomers&sort=clndesc">DESC</a>',
							'Updated <br><a href="?sect=retcustomer&mode=newcustomers&sort=updatedasc">ASC</a> <a href="?sect=retcustomer&mode=newcustomers&sort=updateddesc">DESC</a>',
							'Added <br><a href="?sect=retcustomer&mode=newcustomers&sort=addedasc">ASC</a> <a href="?sect=retcustomer&mode=newcustomers&sort=addeddesc">DESC</a>',
							'Delete<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_advertiser\').attr(\'checked\', \'checked\')">Select All</a>',
							);
		
		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// draw table header
		$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
		$retail_customers_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
		
		// prints page links
		if (empty($_POST['search_box'])) {
			$sql_query = "SELECT
							count(*) as rcount
						 FROM
							advertiser_info
						WHERE
							DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= date_created
						;";
		
		
		// add search lines to page query
		if(!empty($_POST['search_box'])) {
			
			$search_terms = explode(" ",$_POST['search_box']);
			
			$search_query = array();
							
			foreach ($search_terms as $cur_term) {
				$search_query[] = "(company_name LIKE
						'%".str_replace("'","''",$cur_term)."%' OR username LIKE '%".str_replace("'","''",$cur_term)."%' OR last_name LIKE '%".str_replace("'","''",$cur_term)."%')";
			}
			$sql_query .= " AND ".implode(" AND ",$search_query);
		}
			
			$rowscount = $dbh->queryRow($sql_query);
			
			$row_count = $rowscount['rcount'];
			$page_count = (int)$row_count/$page_limiter;
			
			for($i = 0;$i <= $page_count;$i++) {
				$pages_array[] = '<a href="?sect=retcustomer&mode=newcustomers&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
			}
			
			$pages_links = implode(', ',$pages_array);
			
			$retail_customers_listing .= draw_table_contect(array('Total Advertisers: '.number_format($row_count).'<br><center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		}
							
		// print title boxes
		$retail_customers_listing .= draw_table_header($title_array);	
		
		// set sort option
		if (!empty($_GET['sort'])) $_SESSION['advert_sort'] = $_GET['sort'];
				
		$sql_query = "SELECT
						id,
						company_name,
						username,
						last_name,
						date_updated,
						date_created
					 FROM
						advertiser_info
					 WHERE
						DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= date_created";
		
		
		// add search lines to page query
		if(!empty($_POST['search_box'])) {
			
			$search_terms = explode(" ",$_POST['search_box']);
			
			$search_query = array();
							
			foreach ($search_terms as $cur_term) {
				$search_query[] = "(company_name LIKE
						'%".str_replace("'","''",$cur_term)."%' OR username LIKE '%".str_replace("'","''",$cur_term)."%' OR last_name LIKE '%".str_replace("'","''",$cur_term)."%')";
			}
			$sql_query .= " AND ".implode(" AND ",$search_query);
		}
		
		// sets output order by
		if(isset($_SESSION['advert_sort'])) {
			switch($_SESSION['advert_sort']) {
			case 'cnameasc':
				$order_sel = 'company_name ASC';
			break;
			case 'cnamedesc':
				$order_sel = 'company_name DESC';
			break;
			case 'unameasc':
				$order_sel = 'username ASC';
			break;
			case 'unamedesc':
				$order_sel = 'username DESC';
			break;
			case 'clnasc':
				$order_sel = 'last_name ASC';
			break;
			case 'clndesc':
				$order_sel = 'last_name DESC';
			break;
			case 'updatedasc':
				$order_sel = 'date_updated ASC';
			break;
			case 'updateddesc':
				$order_sel = 'date_updated DESC';
			break;
			case 'addedasc':
				$order_sel = 'date_created ASC';
			break;
			case 'addeddesc':
				$order_sel = 'date_created DESC';
			break;
			}
			
			$sql_query .= "
					ORDER BY ".$order_sel;
		
		} else {
			$sql_query .= "
					ORDER BY
						company_name ASC ";
		}
		
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
		
		while($retail_customers = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
			$row_array = array(
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=edit&cid='.$retail_customers['id'].'">'.$retail_customers['company_name'].'</a>',
								$retail_customers['username'],
								$retail_customers['last_name'],
								date('n/j/Y h:i:s A',strtotime($retail_customers['date_updated'])),
								date('n/j/Y h:i:s A',strtotime($retail_customers['date_created'])),
								'<input class="delete_advertiser" name="delete_advertiser[]" type="checkbox" value="'.$retail_customers['id'].'">',
								);
		
			$retail_customers_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
		// print page links
		$retail_customers_listing .= draw_table_contect(array('Total Advertisers: '.number_format($row_count).'<br><center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		
		$retail_customers_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Perform Selected"></center>'),$table_boxes_cnt,'center');
		
		return $retail_customers_listing;
	}
	
	// display retail customers listing
	function listing($message = '') {
		$retail_customers_view = open_table_listing_form('Advertisers Listing','view_retail_customers',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=view','post',$message,13);
		$retail_customers_view .= $this->listing_content();
		$retail_customers_view .= close_table_form();
		
		return $retail_customers_view;
	}
		
	// list retail customers
	function listing_content() {
	  global $dbh;
			
		// sets record limit per page	
		$page_limiter = ADMIN_PER_PAGE_RESULTS; 
		
		// table title array							
		$title_array = array(
							'#',
							'Company Name <br><a href="?sect=retcustomer&mode=view&sort=cnameasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=cnamedesc">DESC</a>',
							'Edit',
							'Staff Pick',
							'Delete<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_advertiser\').attr(\'checked\', \'checked\')">Select All</a>',
							'Username <br><a href="?sect=retcustomer&mode=view&sort=unameasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=unamedesc">DESC</a>',
							'Contact Last Name <br><a href="?sect=retcustomer&mode=view&sort=clnasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=clndesc">DESC</a>',
							'Affiliate Code',
							'Link Affiliate Code',
							'Affiliates',
							'Updated <br><a href="?sect=retcustomer&mode=view&sort=updatedasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=updateddesc">DESC</a>',
							'Created <br><a href="?sect=retcustomer&mode=view&sort=addedasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=addeddesc">DESC</a>',
							'Preview',
							);
		
		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// draw table header
		$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
		$retail_customers_listing = draw_table_header($searchbox_head,$table_boxes_cnt,'center');
	
		// draw table header
		$searchbox_head = array('Filter by State: <select name="state_fltr">'.gen_state_dd($_SESSION['state_fltr']).'</select><input name="submit" type="submit" value="Submit">');
		$retail_customers_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
		
		// prints page links
		if (empty($_POST['search_box'])) {
			$sql_query = "SELECT
							count(*) as rcount
						 FROM
							advertiser_info
						 WHERE 
							approved = 1
						 ";
						 
		if(!empty($_SESSION['state_fltr'])) {
			$sql_query .= " AND state = '".$_SESSION['state_fltr']."' ";
		}
		
		// add search lines to page query
		if(!empty($_POST['search_box'])) {
			
			$search_terms = explode(" ",$_POST['search_box']);
			
			$search_query = array();
							
			foreach ($search_terms as $cur_term) {
				$search_query[] = "(company_name LIKE
						'%".str_replace("'","''",trim($cur_term))."%' OR username LIKE '%".str_replace("'","''",trim($cur_term))."%' OR last_name LIKE '%".str_replace("'","''",trim($cur_term))."%')";
			}
			$sql_query .= " AND ".implode(" AND ",$search_query);
		}
			
			$rowscount = $dbh->queryRow($sql_query);
			
			$row_count = $rowscount['rcount'];
			$page_count = (int)$row_count/$page_limiter;
			
			for($i = 0;$i <= $page_count;$i++) {
				$curpagenum = ($i+1);
				$curpagevalue = $i*$page_limiter;
				$pages_array[] = '<option value="'.$i*$page_limiter.'"'.($_POST['page_val'] == $curpagevalue ? ' selected="selected" ' : '').'>'.$curpagenum.'</option>';
			}
			
			$pages_links = '<select name="page_val">'.implode(', ',$pages_array).'</select><input name="Submit" type="submit" value="Submit" />';
			
			$retail_customers_listing .= draw_table_contect(array('Total Advertisers: '.number_format($row_count).'<br><center>Pages:'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		}
							
		// print title boxes
		$retail_customers_listing .= draw_table_header($title_array);	
		
		// set sort option
		if (!empty($_GET['sort'])) $_SESSION['advert_sort'] = $_GET['sort'];
		
		$sql_query = "SELECT
						id,
						company_name,
						username,
						last_name,
						date_updated,
						affiliate_code,
						link_affiliate_code,
						date_created
					 FROM
						advertiser_info
					 WHERE 
						approved = 1
					 ";
					 
		if(!empty($_SESSION['state_fltr'])) {
			$sql_query .= " AND state = '".$_SESSION['state_fltr']."' ";
		}
		
		// add search lines to page query
		if(!empty($_POST['search_box'])) {
			
			$search_terms = explode(" ",$_POST['search_box']);
			
			$search_query = array();
							
			foreach ($search_terms as $cur_term) {
				$search_query[] = "(company_name LIKE
						'%".str_replace("'","''",$cur_term)."%' OR username LIKE '%".str_replace("'","''",$cur_term)."%' OR last_name LIKE '%".str_replace("'","''",$cur_term)."%')";
			}
			$sql_query .= " AND ".implode(" AND ",$search_query);
		}
		
		// sets output order by
		if(isset($_SESSION['advert_sort'])) {
			switch($_SESSION['advert_sort']) {
			case 'cnameasc':
				$order_sel = 'company_name ASC';
			break;
			case 'cnamedesc':
				$order_sel = 'company_name DESC';
			break;
			case 'unameasc':
				$order_sel = 'username ASC';
			break;
			case 'unamedesc':
				$order_sel = 'username DESC';
			break;
			case 'clnasc':
				$order_sel = 'last_name ASC';
			break;
			case 'clndesc':
				$order_sel = 'last_name DESC';
			break;
			case 'updatedasc':
				$order_sel = 'date_updated ASC';
			break;
			case 'updateddesc':
				$order_sel = 'date_updated DESC';
			break;
			case 'addedasc':
				$order_sel = 'date_created ASC';
			break;
			case 'addeddesc':
				$order_sel = 'date_created DESC';
			break;
			}
			
			$sql_query .= "
					ORDER BY ".$order_sel;
		
		} else {
			$sql_query .= "
					ORDER BY
						company_name ASC ";
		}
			
		if (!empty($_POST['page_val']) && empty($_POST['search_box'])) {
		$sql_query .= "
				LIMIT ".$_POST['page_val'].", ".$page_limiter."  ";
		} elseif (empty($_POST['search_box'])) {
		$sql_query .= "
				LIMIT
				".$page_limiter." ";
		}
					
		$sql_query .= ";";
		
		$item_num = 0;
		$item_num += (isset($_POST['page_val']) ? $_POST['page_val'] : 0);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute();
		
		while($retail_customers = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		  
		  $item_num++;
		  
		  $page_link = SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $retail_customers['company_name'])).'/'.$retail_customers['id'].'/';
		  
		  $row_array = array(
							  $item_num.'.',
							  '<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=edit&cid='.$retail_customers['id'].'">'.$retail_customers['company_name'].'</a>',
							  '<a class="edit_image" href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=edit&cid='.$retail_customers['id'].'">'.$retail_customers['company_name'].'</a>',
							  '<input class="delete_advertiser" name="staff_picks[]" type="checkbox" value="'.$retail_customers['id'].'">',
							  '<input class="delete_advertiser" name="delete_advertiser[]" type="checkbox" value="'.$retail_customers['id'].'">',
							  $retail_customers['username'],
							  $retail_customers['last_name'],
							  $retail_customers['affiliate_code'],
							  $retail_customers['link_affiliate_code'],
							  $this->advert_affiliate_count($retail_customers['affiliate_code']),
							  (!empty($retail_customers['date_updated']) ? date('n/j/Y h:i:s A',strtotime($retail_customers['date_updated'])) : ''),
							  (!empty($retail_customers['date_created']) ? date('n/j/Y h:i:s A',strtotime($retail_customers['date_created'])) : ''),
							  '<a href="'.$page_link.'" target="_blank">Preview</a>',
							  );
	  
		  $retail_customers_listing .= draw_table_contect($row_array,0,'center');
	  
		}
		
//		// print page links
//		$retail_customers_listing .= draw_table_contect(array('Total Advertisers: '.number_format((isset($row_count) ? $row_count : 0)).'<br><center>Pages:<br>'.(isset($pages_links) ? $pages_links : '').'</center>'),$table_boxes_cnt,'center');
		
		$retail_customers_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Perform Selected"></center>'),$table_boxes_cnt,'center');
		
		return $retail_customers_listing;
	}
	
	private function advert_affiliate_count($affiliate_code) {
			global $dbh;
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						advertiser_info
					 WHERE 
						link_affiliate_code = '".$affiliate_code."'
						 ;";
		$rows = $dbh->queryRow($sql_query);

	return $rows['rcount'];
	}

}

?>