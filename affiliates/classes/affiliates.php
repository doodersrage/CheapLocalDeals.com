<?PHP

// handles and displays affiliates_manage admin page
class affiliates_manage {
	
	// delete affiliates
	function delete_affiliates() {
		global $dbh;
		
		foreach($_POST['delete_affiliates'] as $selected_affiliates) {
			$stmt = $dbh->prepare("DELETE FROM affiliate_users WHERE id = '".$selected_affiliates."';");
			$stmt->execute();
		}
		
	}
	
	// display affiliates listing
	function affiliates_listing($message = '') {
		$page_view = open_table_listing_form('Affiliates Listing','view_affiliates','','post',$message);
		$page_view .= $this->affiliates_listing_content();
		$page_view .= close_table_form();
		
	return $page_view;
	}
	
	function affiliates_listing_content() {
			global $dbh, $pgs_tbl;
		
		// table title array							
		$title_array = array(
							'Affiliate Name',
							'Company',
							'Affiliate Code',
							'Delete Affiliate',
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
								'<a href="'.SITE_AFFILIATE_SSL_URL.'?sect=affiliates&mode=edit&pid='.$pages['id'].'">'.$pages['name'].'</a>',
								$pages['company'],
								$pages['affiliate_code'],
								'<input name="delete_affiliates[]" type="checkbox" value="'.$pages['id'].'">',
								);
		
			$pages_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
		$pages_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
		
	return $pages_listing;
	}
	
	// load add affiliates page
	function add_affiliates($message = '') {
		
		$add_pages = open_table_form('Add New Affiliate','add_affiliates',SITE_AFFILIATE_SSL_URL.'?sect=affiliates&mode=addcheck','post',$message);
		$add_pages .= $this->affiliates_form();
		$add_pages .= close_table_form();
		
	return $add_pages;
	}
	
	// load add affiliates page
	function edit_affiliates($message = '') {
		
		$add_pages = open_table_form('Edit Affiliate','edit_affiliates',SITE_AFFILIATE_SSL_URL.'?sect=affiliates&mode=editcheck','post',$message);
		$add_pages .= $this->affiliates_form();
		$add_pages .= close_table_form();
		
	return $add_pages;
	}
	
	// draw affiliates form
	function affiliates_form() {
		global $aff_usrs_tbl;
	
		$pages_form = table_form_header('* indicates required field');
		$pages_form .= table_form_field('<span class="required">*Name:</span>','<input name="name" type="text" size="60" value="'.$aff_usrs_tbl->name.'">');
		$pages_form .= table_form_field('Company Name:','<input name="company" type="text" size="60" value="'.$aff_usrs_tbl->company.'">');
		$pages_form .= table_form_field('<span class="required">*Affiliate Code:</span>','<input name="affiliate_code" type="text" size="8" value="'.$aff_usrs_tbl->affiliate_code.'">');
		$pages_form .= table_span_form_field('<center><input name="id" type="hidden" value="'.$aff_usrs_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
		
	return $pages_form;
	}
	
	// check form submission values
	function form_check() {
			global $aff_usrs_tbl;
		
		if ($aff_usrs_tbl->name == '') {
			$error_message .= '<center>You must atleast assign a name.</center>'.LB;
		}
		
		if ($aff_usrs_tbl->affiliate_code == '') {
			$error_message .= '<center>Affiliate code cannot be blank.</center>'.LB;
		}
		
		if($aff_usrs_tbl->affiliate_id_check($aff_usrs_tbl->affiliate_code) > 0) {
			$error_message .= '<center>Affiliate code has already been assigned to another affiliate. Please enter another code.</center>'.LB;
		}
				
	return $error_message;
	}
		
}

?>