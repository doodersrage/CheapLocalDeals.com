<?PHP

// load settings orders class
require(SITE_ADMIN_CLASSES_DIR.'forms/admin_users.php');
$admin_users_frm = new admin_users_frm;

// load settings orders class
require(SITE_ADMIN_CLASSES_DIR.'listings/admin_users.php');
$admin_users_lst = new admin_users_lst;

// delete selected admin_users
if(isset($_POST['delete_admin_users'])) {
  $admin_users_frm->delete();
}

// write page header
$page_content = page_header('Admin Users');

// select page output function
switch ($_GET['mode']) {
// display a listing of admin users
case 'admin_users_listing':
  $page_content .= $admin_users_lst->listing();
break;
// create a new admin user
case 'new_user':
  $page_content .= $admin_users_frm->add();
break;
// perform new user insertion
case 'new_user_check':
  $admin_users_table->get_post_vars();
  $error_message = $admin_users_frm->form_check();
  if (empty($error_message)) {
	$admin_users_table->insert();
	$page_content .= create_warning_box('<center>New admin user has been added</center>');
	header("Location: ".SITE_ADMIN_SSL_URL."?sect=admin_users&mode=admin_users_listing");
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $admin_users_frm->add();
  } 
break;
// edit existing admin user
case 'admin_users_edit':
  $admin_users_table->get_db_vars($_GET['uid']);
  $page_content .= $admin_users_frm->edit();
break;
// perform user edit check
case 'edit_user_check':
  $admin_users_table->get_post_vars();
  $error_message = $admin_users_frm->form_check();
  if (empty($error_message)) {
	$admin_users_table->update();
	$page_content .= create_warning_box('<center>Admin User Updated</center>');
	header("Location: ".SITE_ADMIN_SSL_URL."?sect=admin_users&mode=admin_users_listing");
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $admin_users_frm->edit();
  } 
break;
}

?>