<?PHP

// load api_access_lst class
require(SITE_ADMIN_CLASSES_DIR.'listings/api_access.php');
$api_access_lst = new api_access_lst;

// load api_access_frm class
require(SITE_ADMIN_CLASSES_DIR.'forms/api_access.php');
$api_access_frm = new api_access_frm;

// delete selected admin_users
if(isset($_POST['delete_api_access'])) {
  $api_access_lst->delete();
}

// write page header
$page_content = page_header('API Access Settings');

// select page output function
switch ($_GET['mode']) {
// display a listing of admin users
case 'view':
  $page_content .= $api_access_lst->listing();
break;
// create a new admin user
case 'new':
  $page_content .= $api_access_frm->add();
break;
// perform new user insertion
case 'newcheck':
  $api_acc_tbl->get_post_vars();
  $error_message = $api_access_frm->form_check();
  if (empty($error_message)) {
	$api_acc_tbl->insert();
	$page_content .= create_warning_box('<center>New api access user has been added</center>');
	header("Location: ".SITE_ADMIN_SSL_URL."?sect=apiaccess&mode=view");
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $api_access_frm->add();
  } 
break;
// edit existing admin user
case 'edit':
  $api_acc_tbl->get_db_vars($_GET['uid']);
  $page_content .= $api_access_frm->edit();
break;
// perform user edit check
case 'editcheck':
  $api_acc_tbl->get_post_vars();
  $error_message = $api_access_frm->form_check();
  if (empty($error_message)) {
	$api_acc_tbl->update();
	$page_content .= create_warning_box('<center>API Access User Updated</center>');
	header("Location: ".SITE_ADMIN_SSL_URL."?sect=apiaccess&mode=view");
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $api_access_frm->edit();
  } 
break;
}
?>