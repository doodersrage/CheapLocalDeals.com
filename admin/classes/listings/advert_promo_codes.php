<?PHP

// document used to add or modify available advertiser promo codes selection

// advert_promo_codes management class
class advert_promo_codes_lst {

  // display advert_promo_codes listing
  function listing($message = '') {
	$ad_payment_methods_view = open_table_listing_form('Advertiser Promo Codes Listing','view_advert_promo_codes',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=promocodes','post',$message);
	$ad_payment_methods_view .= $this->listing_content();
	$ad_payment_methods_view .= close_table_form();
  return $ad_payment_methods_view;
  }

  // list advert_promo_codes	
  function listing_content() {
	global $dbh;	

	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	$advert_promo_codes_listing .= draw_table_contect(array('<center><a href="?sect=retcustomer&mode=promocodesnew"><strong><font color="red">Add New Promo Code</font></strong></a></center>'),4);
	
	// table title array							
	$title_array = array(
						'Promo Code',
						'Percentage',
						'Updated',
						'Delete<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_advert_promo_codes\').attr(\'checked\', \'checked\')">Select All</a>'
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);
	
	// print title boxes
	$advert_promo_codes_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					promo_code,
					percentage,
					updated
				 FROM
					advert_promo_codes
				 ORDER BY id ";
	
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
	
	while($advert_promo_codes_ret = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	
		$row_array = array(
						   '<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=promocodesedit&cid='.$advert_promo_codes_ret['id'].'">'.$advert_promo_codes_ret['promo_code'].'</a>',
						   $advert_promo_codes_ret['percentage'],
						   date('n/j/Y h:i:s A',strtotime($advert_promo_codes_ret['updated'])),
						   '<input class="delete_advert_promo_codes" name="delete_advert_promo_codes[]" type="checkbox" value="'.$advert_promo_codes_ret['id'].'">'
						);
	
		$advert_promo_codes_listing .= draw_table_contect($row_array,0,'center');
	
	}
	
	if (empty($_POST['search_box'])) {
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						advert_promo_codes
					 ;";
		
		$rowscount = $dbh->queryRow($sql_query);
		
		$row_count = $rowscount['rcount'];
		$page_count = (int)$row_count/$page_limiter;
		
		for($i = 0;$i <= $page_count;$i++) {
			$pages_array[] = '<a href="?sect=retcustomer&mode=promocodes&cid='.$_GET['lid'].'&page_val='.($i*$page_limiter).'">'.($i+1).'</a>';
		}
		
		$pages_links = implode(', ',$pages_array);
		
		$advert_promo_codes_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt);
	}
	
	$advert_promo_codes_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt);
	
  return $advert_promo_codes_listing;
  }
  
}
?>