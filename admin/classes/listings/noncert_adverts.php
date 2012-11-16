<?PHP

class noncert_adverts_lst {
  
  // display retail customers listing
  function listing($message = '') {
	$noncert_adverts_view = open_table_listing_form('Non-Certificate Advertisers Listing','view_noncert_adverts',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=noncertadverts','post',$message,10);
	$noncert_adverts_view .= $this->listing_content();
	$noncert_adverts_view .= close_table_form();
  return $noncert_adverts_view;
  }
	  
  // list retail customers
  function listing_content() {
	global $dbh;
		
	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS*10; 
	
	// table title array							
	$title_array = array(
						'#',
						'Name <br><a href="?sect=retcustomer&mode=noncertadverts&sort=cnameasc">ASC</a> <a href="?sect=retcustomer&mode=noncertadverts&sort=cnamedesc">DESC</a>',
						'City <br><a href="?sect=retcustomer&mode=noncertadverts&sort=cityasc">ASC</a> <a href="?sect=retcustomer&mode=noncertadverts&sort=citydesc">DESC</a>',
						'State <br><a href="?sect=retcustomer&mode=noncertadverts&sort=stateasc">ASC</a> <a href="?sect=retcustomer&mode=noncertadverts&sort=statedesc">DESC</a>',
						'Zip <br><a href="?sect=retcustomer&mode=noncertadverts&sort=zipasc">ASC</a> <a href="?sect=retcustomer&mode=noncertadverts&sort=zipdesc">DESC</a>',
						'Delete<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_noncertadvertiser\').attr(\'checked\', \'checked\')">Select All</a>',
						);
	
	// gets table boxes count
	$table_boxes_cnt = count($title_array);

	// draw table header
	$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
	$noncert_adverts_listing = draw_table_header($searchbox_head,$table_boxes_cnt,'center');
	
	$newbox_head = array('<a href="?sect=retcustomer&mode=addnoncert">Add New</a>');
	$noncert_adverts_listing .= draw_table_header($newbox_head,$table_boxes_cnt,'center');
	
	// prints page links
	if (empty($_POST['search_box'])) {
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						businesses
					 ";
	
	// add search lines to page query
	if(!empty($_POST['search_box'])) {
		
		$search_terms = explode(" ",$_POST['search_box']);
		
		$search_query = array();
						
		foreach ($search_terms as $cur_term) {
			$search_query[] = "(name LIKE
					'%".str_replace("'","''",$cur_term)."%' OR description LIKE '%".str_replace("'","''",$cur_term)."%' OR zip LIKE '%".str_replace("'","''",$cur_term)."%')";
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
	
		$noncert_adverts_listing .= draw_table_contect(array('Total Advertisers: '.number_format($row_count).'<br><center>Pages: '.$pages_links.'</center>'),$table_boxes_cnt,'center');
	}
						
	// print title boxes
	$noncert_adverts_listing .= draw_table_header($title_array);	
	
	// set sort option
	if (!empty($_GET['sort'])) $_SESSION['advert_sort'] = $_GET['sort'];
	
	$sql_query = "SELECT
					id,
					name,
					city,
					state,
					zip
				 FROM
					businesses
				 ";
	
	// add search lines to page query
	if(!empty($_POST['search_box'])) {
		
		$search_terms = explode(" ",$_POST['search_box']);
		
		$search_query = array();
						
		foreach ($search_terms as $cur_term) {
			$search_query[] = "(name LIKE
					'%".str_replace("'","''",$cur_term)."%' OR description LIKE '%".str_replace("'","''",$cur_term)."%' OR zip LIKE '%".str_replace("'","''",$cur_term)."%')";
		}
		$sql_query .= " WHERE ".implode(" AND ",$search_query);
	}
	
	// sets output order by
	if(isset($_SESSION['advert_sort'])) {
		switch($_SESSION['advert_sort']) {
		case 'cnameasc':
			$order_sel = 'name ASC';
		break;
		case 'cnamedesc':
			$order_sel = 'name DESC';
		break;
		case 'cityasc':
			$order_sel = 'city ASC';
		break;
		case 'citydesc':
			$order_sel = 'city DESC';
		break;
		case 'stateasc':
			$order_sel = 'state ASC';
		break;
		case 'statedesc':
			$order_sel = 'state DESC';
		break;
		case 'zipdasc':
			$order_sel = 'zip ASC';
		break;
		case 'zipddesc':
			$order_sel = 'zip DESC';
		break;
		}
		
		$sql_query .= "
				ORDER BY ".$order_sel;
	
	} else {
		$sql_query .= "
				ORDER BY
					name ASC ";
	}
		
	if (!empty($_POST['page_val']) && empty($_POST['search_box'])) {
	$sql_query .= "
			LIMIT ".$_GET['page_val'].",".$page_limiter."  ";
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
	
	while($noncert_adverts = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
		$item_num++;
		
		$row_array = array(
							$item_num.'.',
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=editnoncert&cid='.$noncert_adverts['id'].'">'.$noncert_adverts['name'].'</a>',
							$noncert_adverts['city'],
							$noncert_adverts['state'],
							$noncert_adverts['zip'],
							'<input class="delete_advertiser" name="delete_noncertadvertiser[]" type="checkbox" value="'.$noncert_adverts['id'].'">',
							);
	
		$noncert_adverts_listing .= draw_table_contect($row_array,0,'center');
	
	}
	
	// print page links
	$noncert_adverts_listing .= draw_table_contect(array('Total Advertisers: '.number_format((isset($row_count) ? $row_count : 0)).'<br><center>Pages:<br>'.(isset($pages_links) ? $pages_links : '').'</center>'),$table_boxes_cnt,'center');
	
	$noncert_adverts_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Perform Selected"></center>'),$table_boxes_cnt,'center');
	
  return $noncert_adverts_listing;
  }
}

?>