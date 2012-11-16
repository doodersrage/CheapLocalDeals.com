<?PHP

// load zipcodes page
require(SITE_ADMIN_CLASSES_DIR.'forms/cities.php');
$cities_frm = new cities_frm;

// load zipcodes page
require(SITE_ADMIN_CLASSES_DIR.'listings/cities.php');
$cities_lst = new cities_lst;

// write page header
$page_content = page_header('Cities/Towns');

// delete selected zip codes
if (!empty($_POST['delete_states'])) {
  $cities_frm->delete();
}

// select page output function
switch($_GET['mode']) {
// view cities listing
case 'view';
  $page_content .= $cities_lst->listing();
break;
// edit existing city
case 'edit';
  $cities_tbl->get_db_vars($_GET['cid']);
  $page_content .= $cities_frm->edit();
break;
// check edited city
case 'editcheck':
  $cities_tbl->get_post_vars();
  $error_message = $cities_frm->form_check();
  if (empty($error_message)) {
	$cities_tbl->update();
	$page_content .= create_warning_box('<center>City/Town Updated</center>');
	$page_content .= $cities_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $cities_frm->edit();
  } 
break;
// add new city
case 'add';
  $page_content .= $cities_frm->add();
break;
// add new city submission check
case 'addcheck':
  $cities_tbl->get_post_vars();
  $error_message = $cities_frm->form_check();
  if (empty($error_message)) {
	$cities_tbl->insert();
	$page_content .= create_warning_box('<center>New city/town has been added</center>');
	$page_content .= $cities_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $cities_frm->add();
  } 
break;
}

?>