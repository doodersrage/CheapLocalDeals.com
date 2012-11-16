<?PHP

// create and modify certificate amount values

// certificates management class
class certificate_amount_lst {

  // display certificates listing
  function listing($message = '') {
	$certificates_view = open_table_listing_form('Certificate Amount Listing','view_certificates',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=certificateamount','post',$message);
	$certificates_view .= $this->listing_content();
	$certificates_view .= close_table_form();
  return $certificates_view;
  }

  // list certificates	
  function listing_content() {
	global $dbh;

	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'Discount Amount',
						'Sort Order',
						'Cost',
						'Min Spend Amounts',
						'Delete Certificates Amounts<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_certificate_amount\').attr(\'checked\', \'checked\')">Select All</a>'
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);

	// draw table header
	$searchbox_head = array('<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=certificateamountnew">Add New Certificate Amount</a>');
	$certificates_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
	
	// print title boxes
	$certificates_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					discount_amount,
					cost,
					min_spend_amts,
					crtamt_sort
				 FROM
					certificate_amount
				 ORDER BY crtamt_sort ASC
				 ";
	
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
	
	while($certificates_certificates = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	
		$row_array = array(
							'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=certificateamountedit&cid='.$certificates_certificates['id'].'">'.$certificates_certificates['discount_amount'].'</a>',
							$certificates_certificates['crtamt_sort'],
							$certificates_certificates['cost'],
							$certificates_certificates['min_spend_amts'],
							'<input class="delete_certificate_amount" name="delete_certificate_amount[]" type="checkbox" value="'.$certificates_certificates['id'].'">'
							);
	
		$certificates_listing .= draw_table_contect($row_array,0,'center');
	
	}
	
	if (empty($_POST['search_box'])) {
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						certificate_amount
					 ;";
		
		$rowscount = $dbh->queryRow($sql_query);
		
		$row_count = $rowscount['rcount'];
		$page_count = (int)$row_count/$page_limiter;
		
		for($i = 0;$i <= $page_count;$i++) {
			$pages_array[] = '<a href="?sect=retcustomer&mode=certificates&page_val='.($i*$page_limiter).'">'.($i+1).'</a>';
		}
		
		$pages_links = implode(', ',$pages_array);
		
		$certificates_listing .= table_listing_span_form_field('<center>Pages:<br>'.$pages_links.'</center>');
	}
	
	$certificates_listing .= table_listing_span_form_field('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>');
	
  return $certificates_listing;
  }

}

?>