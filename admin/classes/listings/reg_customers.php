<?PHP

// document adds and modifies customers

// retail customers management class
class reg_customers_lst {

  // display zip code listing
  function reg_customers_listing_new($message = '') {
	$reg_customers_view = open_table_listing_form('New Customers Listing','view_reg_customers',SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=newcustomers','post',$message,6);
	$reg_customers_view .= $this->reg_customers_listing_new_content();
	$reg_customers_view .= close_table_form();
  return $reg_customers_view;
  }
	  
  // list zip codes	
  function reg_customers_listing_new_content() {
	global $dbh;
		
	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'E-Mail <br><a href="?sect=regcustomer&mode=newcustomers&sort=unameasc">ASC</a> <a href="?sect=regcustomer&mode=newcustomers&sort=unamedesc">DESC</a>',
						'Contact First Name <br><a href="?sect=regcustomer&mode=newcustomers&sort=cfnasc">ASC</a> <a href="?sect=regcustomer&mode=newcustomers&sort=cfndesc">DESC</a>',
						'Contact Last Name <br><a href="?sect=regcustomer&mode=newcustomers&sort=clnasc">ASC</a> <a href="?sect=regcustomer&mode=newcustomers&sort=clndesc">DESC</a>',
						'Updated <br><a href="?sect=regcustomer&mode=newcustomers&sort=updatedasc">ASC</a> <a href="?sect=regcustomer&mode=newcustomers&sort=updateddesc">DESC</a>',
						'Added <br><a href="?sect=regcustomer&mode=newcustomers&sort=addedasc">ASC</a> <a href="?sect=regcustomer&mode=newcustomers&sort=addeddesc">DESC</a>',
						'Delete Customer<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_regcustomer\').attr(\'checked\', \'checked\')">Select All</a>',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);

	// draw table header
	$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
	$reg_customers_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
	
	// print page links
	if (empty($_POST['search_box'])) {
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						customer_info
					 WHERE
						DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= date_created ;";
	// add search lines to page query
	if(!empty($_POST['search_box'])) {
		
		$search_terms = explode(" ",$_POST['search_box']);
		
		$search_query = array();
						
		foreach ($search_terms as $cur_term) {
			$search_query[] = "(email_address LIKE '%".$cur_term."%' OR last_name LIKE '%".$cur_term."%' OR first_name LIKE '%".$cur_term."%')";
		}
		$sql_query .= " WHERE ".implode(" AND ",$search_query);
	}
		
		$rowscount = $dbh->queryRow($sql_query);
		
		$row_count = $rowscount['rcount'];
		$page_count = (int)$row_count/$page_limiter;
		
		for($i = 0;$i <= $page_count;$i++) {
		  $pages_array[] = '<a href="?sect=retcustomer&mode=newcustomers&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
		}
		
		$pages_links = implode(', ',$pages_array);
		
		$reg_customers_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	}
						
	// print title boxes
	$reg_customers_listing .= draw_table_header($title_array);
	
	// set sort option
	if (!empty($_GET['sort'])) $_SESSION['cust_sort'] = $_GET['sort'];
	
	$sql_query = "SELECT
					id,
					email_address,
					first_name,
					last_name,
					date_updated,
					date_created
				 FROM
					customer_info
				 WHERE
					DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= date_created ";
	
	// add search lines to page query
	if(!empty($_POST['search_box'])) {
		
		$search_terms = explode(" ",$_POST['search_box']);
		
		$search_query = array();
						
		foreach ($search_terms as $cur_term) {
		  $search_query[] = "(email_address LIKE '%".$cur_term."%' OR last_name LIKE '%".$cur_term."%' OR first_name LIKE '%".$cur_term."%')";
		}
		$sql_query .= " AND ".implode(" AND ",$search_query);
	}

	// sets output order by
	if(isset($_SESSION['cust_sort'])) {
		switch($_SESSION['cust_sort']) {
		case 'cfnasc':
		  $order_sel = 'first_name ASC';
		break;
		case 'cfndesc':
		  $order_sel = 'first_name DESC';
		break;
		case 'unameasc':
		  $order_sel = 'email_address ASC';
		break;
		case 'unamedesc':
		  $order_sel = 'email_address DESC';
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
				ORDER BY ".$order_sel." ";
	
	} else {
	  $sql_query .= "
			  ORDER BY
				  email_address ASC ";
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
	
	while($reg_customers = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	
		$row_array = array(
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=edit&cid='.$reg_customers['id'].'">'.$reg_customers['email_address'].'</a>',
							$reg_customers['first_name'],
							$reg_customers['last_name'],
							date('n/j/Y h:i:s A',strtotime($reg_customers['date_updated'])),
							date('n/j/Y h:i:s A',strtotime($reg_customers['date_created'])),
							'<input class="delete_regcustomer" name="delete_regcustomer[]" type="checkbox" value="'.$reg_customers['id'].'">',
							);
	
		$reg_customers_listing .= draw_table_contect($row_array,0,'center');
	
	}
	
	// print page links
	$reg_customers_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	
	$reg_customers_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
	
  return $reg_customers_listing;
  }
  
  // display retail customers listing
  function listing($message = '') {
	$reg_customers_view = open_table_listing_form('Customers Listing','view_reg_customers',SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=view','post',$message,6);
	$reg_customers_view .= $this->listing_content();
	$reg_customers_view .= close_table_form();
  return $reg_customers_view;
  }
	  
  // list zip codes	
  function listing_content() {
	global $dbh;
		
	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'#',
						'E-Mail <br><a href="?sect=regcustomer&mode=view&sort=unameasc">ASC</a> <a href="?sect=regcustomer&mode=view&sort=unamedesc">DESC</a>',
						'Contact First Name <br><a href="?sect=regcustomer&mode=view&sort=cfnasc">ASC</a> <a href="?sect=regcustomer&mode=view&sort=cfndesc">DESC</a>',
						'Contact Last Name <br><a href="?sect=regcustomer&mode=view&sort=clnasc">ASC</a> <a href="?sect=regcustomer&mode=view&sort=clndesc">DESC</a>',
						'Updated <br><a href="?sect=regcustomer&mode=view&sort=updatedasc">ASC</a> <a href="?sect=regcustomer&mode=view&sort=updateddesc">DESC</a>',
						'Delete Customer<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_regcustomer\').attr(\'checked\', \'checked\')">Select All</a>',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);

	// draw table header
	$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
	$reg_customers_listing = draw_table_header($searchbox_head,$table_boxes_cnt,'center');
								
	// print page links
	if (empty($_POST['search_box'])) {
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						customer_info
					 ;";
	// add search lines to page query
	if(!empty($_POST['search_box'])) {
	  
	  $search_terms = explode(" ",$_POST['search_box']);
	  
	  $search_query = array();
					  
	  foreach ($search_terms as $cur_term) {
		$search_query[] = "(email_address LIKE '%".$cur_term."%' OR last_name LIKE '%".$cur_term."%' OR first_name LIKE '%".$cur_term."%')";
	  }
	  $sql_query .= " WHERE ".implode(" AND ",$search_query);
	}
	  
	  $rowscount = $dbh->queryRow($sql_query);
	  
	  $row_count = $rowscount['rcount'];
	  $page_count = (int)$row_count/$page_limiter;
	  
	  for($i = 0;$i <= $page_count;$i++) {
		$pages_array[] = '<a href="?sect=regcustomer&mode=view&page_val='.($i*$page_limiter).'">'.(isset($_GET['page_val']) ? $_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '' : '').($i+1).(isset($_GET['page_val']) ? $_GET['page_val'] == $i*$page_limiter ? '</font>' : '' : '').'</a>';
	  }
	  
	  $pages_links = implode(', ',$pages_array);
	  
	  $reg_customers_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	}
						
	// print title boxes
	$reg_customers_listing .= draw_table_header($title_array);
	
	// set sort option
	if (!empty($_GET['sort'])) $_SESSION['cust_sort'] = $_GET['sort'];
	
	$sql_query = "SELECT
					id,
					email_address,
					first_name,
					last_name,
					date_updated
				 FROM
					customer_info
				 ";
	
	// add search lines to page query
	if(!empty($_POST['search_box'])) {
	  
	  $search_terms = explode(" ",$_POST['search_box']);
	  
	  $search_query = array();
					  
	  foreach ($search_terms as $cur_term) {
		$search_query[] = "(last_name LIKE '%".$cur_term."%' OR first_name LIKE '%".$cur_term."%' OR email_address LIKE '%".$cur_term."%')";
	  }
	  $sql_query .= " WHERE ".implode(" AND ",$search_query);
	}
	
	// sets output order by
	if(isset($_SESSION['cust_sort'])) {
	  switch($_SESSION['cust_sort']) {
	  case 'cfnasc':
		  $order_sel = 'first_name ASC';
	  break;
	  case 'cfndesc':
		  $order_sel = 'first_name DESC';
	  break;
	  case 'unameasc':
		  $order_sel = 'email_address ASC';
	  break;
	  case 'unamedesc':
		  $order_sel = 'email_address DESC';
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
	  default:
		  $order_sel = 'email_address ASC';
	  break;
	  }
	  
	  $sql_query .= "
			  ORDER BY ".$order_sel." ";
  
	} else {
	  $sql_query .= "
			  ORDER BY
				  email_address ASC ";
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
	
	$item_num = 0;
	$item_num += $_GET['page_val'];
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute();
	while($reg_customers = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	  
	  $item_num++;
  
	  $row_array = array(
						  $item_num.'.',
						  '<a href="'.SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=edit&cid='.$reg_customers['id'].'">'.$reg_customers['email_address'].'</a>',
						  $reg_customers['first_name'],
						  $reg_customers['last_name'],
						  date('n/j/Y h:i:s A',strtotime($reg_customers['date_updated'])),
						  '<input class="delete_regcustomer" name="delete_regcustomer[]" type="checkbox" value="'.$reg_customers['id'].'">',
						  );
  
	  $reg_customers_listing .= draw_table_contect($row_array,0,'center');
  
	}
	
	// print page links
	$reg_customers_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
	
	$reg_customers_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
	
  return $reg_customers_listing;
  }

}

?>