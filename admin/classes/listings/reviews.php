<?PHP

class advert_reviews_lst {

	// display reviews
	function listing($message = '') {
		$cat_view = open_table_listing_form('Reviews Listing','view_reviews','','post',$message,7);
		$cat_view .= $this->listing_content();
		$cat_view .= close_table_form();
		
	return $cat_view;
	}

	function listing_content() {
			global $dbh, $adv_rvws_tbl, $customer_info_table, $adv_info_tbl;

		$page_limiter = ADMIN_PER_PAGE_RESULTS; 

		// table title array							
		$title_array = array(
							'Customer Name',
							'Advertiser',
							'Rating',
							'Review',
							'Added',
							'Approve',
							'Delete',
							);

		// gets table boxes count
		$table_boxes_cnt = count($title_array);
	
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						advertiser_reviews
					 WHERE
						(approved = 0 OR approved is null) OR approved = 2;";
		
		$rowscount = $dbh->queryRow($sql_query);
		
		$row_count = $rowscount['rcount'];
		$page_count = (int)$row_count/$page_limiter;
		
		for($i = 0;$i <= $page_count;$i++) {
			$pages_array[] = '<a href="?sect=retcustomer&mode=reviews&page_val='.($i*$page_limiter).'">'.($i+1).'</a>';
		}
		
		$pages_links = implode(', ',$pages_array);
		
		$cat_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt);
							
		// print title boxes
		$cat_listing .= draw_table_header($title_array);
		
		$sql_query = "SELECT
						id
					 FROM
						advertiser_reviews
					 WHERE
					 	(approved = 0 OR approved is null) OR approved = 2
					 ORDER BY added DESC";
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
		
		while($reviews = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
			$adv_rvws_tbl->get_db_vars($reviews['id']);
			$customer_info_table->get_db_vars($adv_rvws_tbl->customer_id);
			$adv_info_tbl->get_db_vars($adv_rvws_tbl->advertiser_id);
		
			$row_array = array(
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=edit&cid='.$customer_info_table->id.'">'.$customer_info_table->first_name.' '.$customer_info_table->last_name.'</a>',
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=edit&cid='.$adv_info_tbl->id.'">'.$adv_info_tbl->company_name.'</a>',
								$adv_rvws_tbl->rating,
								$adv_rvws_tbl->review,
								date('n/j/Y h:i:s A',strtotime($adv_rvws_tbl->added)),
								'<input class="delete_cat" name="approve_review[]" type="checkbox" value="'.$reviews['id'].'">',
								'<input class="delete_cat" name="delete_review[]" type="checkbox" value="'.$reviews['id'].'">'
								);
		
			$cat_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
		$cat_listing .= draw_table_contect(array('<center><input name="apply_review_changes" type="hidden" value="1"><input name="submit" type="submit" value="Apply Changes"></center>'),$table_boxes_cnt,'center');
				
	return $cat_listing;
	}
	
}

?>