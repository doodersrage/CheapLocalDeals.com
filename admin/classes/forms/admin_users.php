<?PHP

class admin_users_frm {

  // delete selected users
  function delete() {
	global $dbh;
	if(is_array($_POST['delete_admin_users'])) {
	  foreach($_POST['delete_admin_users'] as $curid) {
		$stmt = $dbh->prepare("DELETE FROM admin_users WHERE id = '".$curid."';");
		$stmt->execute();
	  }
	}
  }

  // load add admin_users page
  function add($message = '') {
	$add_certificates = open_table_form('Add New Admin User','add_admin_user',SITE_ADMIN_SSL_URL.'?sect=admin_users&mode=new_user_check&uid='.$_GET['uid'],'post',$message);
	$add_certificates .= $this->form();
	$add_certificates .= close_table_form();
  return $add_certificates;
  }
  
  // load add admin_users page
  function edit($message = '') {
	$add_certificates = open_table_form('Edit Admin User','edit_admin_user',SITE_ADMIN_SSL_URL.'?sect=admin_users&mode=edit_user_check&uid='.$_GET['uid'],'post',$message);
	$add_certificates .= $this->form();
	$add_certificates .= close_table_form();
  return $add_certificates;
  }
  
  // draw admin_users form
  function form() {
	global $admin_users_table;
	
	// array added to handle user access
	$allowed_access_array = array(
								  'Advertisers',
								  'Customers',
								  'States',
								  'Cities',
								  'CitiesCategories',
								  'Zip Codes',
								  'Categories',
								  'Pages',
								  'Stats',
								  'Orders',
								  'Email',
								  'Admin Users',
								  'Settings',
								  'Affiliate System',
								  'CreateCertificate'
								  );
	
	// write section checkboxes
	foreach($allowed_access_array as $access_item) {
	  $allowed_access_list .= '<input name="allowed_access['.$access_item.']" type="checkbox" value="1" '.(is_array($admin_users_table->allowed_access) ? $admin_users_table->allowed_access[$access_item] == 1 ? 'checked' : '' : '').' /> '.$access_item.'<br>';
	}
	
	$admin_users_form = table_form_header('* indicates required field');
	$admin_users_form .= table_form_field('<span class="required">*Username:</span>','<input name="username" type="text" size="30" maxlength="30" value="'.$admin_users_table->username.'">');
	$admin_users_form .= table_form_field('<span class="required">*Password</span>:','<input name="password" type="password" size="30" maxlength="30" value="">');
	$admin_users_form .= table_form_field('Allow Access To These Admin Sections:',$allowed_access_list);
	$admin_users_form .= table_span_form_field('<center><input name="id" type="hidden" value="'.$admin_users_table->id.'"><input name="submit" type="submit" value="Submit"></center>');
	
  return $admin_users_form;
  }
	  
  // check form submission values
  function form_check() {
	global $admin_users_table;
	
	// required fields array
	$required_fields = array(
							'Username'=> $admin_users_table->username,
							'Password'=> $admin_users_table->password
							);
		
	// check error values and write error array					
	foreach($required_fields as $field_name => $output) {

		if (empty($output)) {
		  $errors_array[] = $field_name;
		}
	
	}
	
	if (!empty($errors_array)) {
	  $error_message = 'You did not supply a value for these fields: ' . implode(', ',$errors_array);
	}
	
	if ($admin_users_table->username_check() > 0) {
	  $error_message .= '<br>Username has already been assigned to another user. Please choose another.';
	}
	
  return $error_message;
  }

}

?>