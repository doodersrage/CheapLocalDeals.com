<?PHP

// ad_payment_methods management class
class ad_payment_methods_lst {

  // display ad_payment_methods listing
  function listing($message = '') {
	$ad_payment_methods_view = open_table_listing_form('Advertiser Payment Methods Listing','view_ad_payment_methods',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=paymentmethods','post',$message);
	$ad_payment_methods_view .= $this->listing_content();
	$ad_payment_methods_view .= close_table_form();
  return $ad_payment_methods_view;
  }

  // list ad_payment_methods	
  function listing_content() {
	global $dbh;	

	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	$advertiser_levels_listing .= draw_table_contect(array('<center><a href="?sect=retcustomer&mode=paymentmethodsnew"><strong><font color="red">Add New Payment Method</font></strong></a></center>'),2);
	
	// table title array							
	$title_array = array(
						'Method',
						'Image',
						'Delete Payment Method<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_advertiser_payment_methods\').attr(\'checked\', \'checked\')">Select All</a>'
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);
	
	// print title boxes
	$advertiser_levels_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					method,
					image
				 FROM
					advertiser_payment_methods
				 ORDER BY method ";
	
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
	  $image_location = CONNECTION_TYPE.'includes/resize_image.deal?image='.urlencode('payment_logos/'.$advertiser_levels_advertiser_levels['image']).'&amp;new_width=50&amp;new_height=50';
	  $row_array = array(
						 '<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=paymentmethodsedit&cid='.$advertiser_levels_advertiser_levels['id'].'">'.$advertiser_levels_advertiser_levels['method'].'</a>',
						 (!empty($advertiser_levels_advertiser_levels['image']) ? '<img src="'.$image_location.'" alt="' . htmlentities($advertiser_levels_advertiser_levels['method']) . '" />' : ''),
						 '<input class="delete_advertiser_payment_methods" name="delete_advertiser_payment_methods[]" type="checkbox" value="'.$advertiser_levels_advertiser_levels['id'].'">')
	  ;
	  $advertiser_levels_listing .= draw_table_contect($row_array,0,'center');
	}
	
	if (empty($_POST['search_box'])) {
	  $sql_query = "SELECT
					  count(*) as rcount
				   FROM
					  advertiser_payment_methods
				   ;";
	  
	  $rowscount = $dbh->queryRow($sql_query);
	  
	  $row_count = $rowscount['rcount'];
	  $page_count = (int)$row_count/$page_limiter;
	  
	  for($i = 0;$i <= $page_count;$i++) {
		$pages_array[] = '<a href="?sect=retcustomer&mode=paymentmethods&cid='.$_GET['lid'].'&page_val='.($i*$page_limiter).'">'.($i+1).'</a>';
	  }
	  
	  $pages_links = implode(', ',$pages_array);
	  
	  $advertiser_levels_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt);
	}
	
	$advertiser_levels_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt);
	
  return $advertiser_levels_listing;
  }

}
?>