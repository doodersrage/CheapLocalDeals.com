<?PHP

// document allows the creation and modication of admin users

// admin_users class
class admin_users_lst {

  // display admin_users listing
  function listing($message = '') {
	$certificates_view = open_table_listing_form('Admin Users Listing','view_certificates',SITE_ADMIN_SSL_URL.'?sect=admin_users&mode=admin_users_listing','post',$message);
	$certificates_view .= $this->listing_content();
	$certificates_view .= close_table_form();
  return $certificates_view;
  }

  // list admin_users	
  function listing_content() {
	global $dbh;

	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 
	
	// table title array							
	$title_array = array(
						'Username',
						'Created',
						'Last Login',
						'Login IP',
						'Delete Admin User',
						);

	// gets table boxes count
	$table_boxes_cnt = count($title_array);

	// draw table header
	$searchbox_head = array('<a href="'.SITE_ADMIN_SSL_URL.'?sect=admin_users&mode=new_user">Add New Admin User</a>');
	$admin_users_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
						
	// print title boxes
	$admin_users_listing .= draw_table_header($title_array);
	
	$sql_query = "SELECT
					id,
					username,
					created,
					last_login,
					login_ip
				 FROM
					admin_users
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
	
	while($admin_users = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	  $row_array = array(
						  '<a href="'.SITE_ADMIN_SSL_URL.'?sect=admin_users&mode=admin_users_edit&uid='.$admin_users['id'].'">'.$admin_users['username'].'</a>',
						  $admin_users['created'],
						  $admin_users['last_login'],
						  $admin_users['login_ip'],
						  '<input name="delete_admin_users[]" type="checkbox" value="'.$admin_users['id'].'">'
						  );
  
	  $admin_users_listing .= draw_table_contect($row_array,0,'center');
	}
	
	if (empty($_POST['search_box'])) {
	  $sql_query = "SELECT
					  count(*) as rcount
				   FROM
					  admin_users
				   ;";
	  
	  $rowscount = $dbh->queryRow($sql_query);
	  
	  $row_count = $rowscount['rcount'];
	  $page_count = (int)$row_count/$page_limiter;
	  
	  for($i = 0;$i <= $page_count;$i++) {
		$pages_array[] = '<a href="?sect=admin_usersmode=admin_users_listing&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
	  }
	  
	  $pages_links = implode(', ',$pages_array);
	  
	  $admin_users_listing .= table_listing_span_form_field('<center>Pages:<br>'.$pages_links.'</center>');
	}
	
	$admin_users_listing .= table_listing_span_form_field('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>');
	
  return $admin_users_listing;
  }

}

?>