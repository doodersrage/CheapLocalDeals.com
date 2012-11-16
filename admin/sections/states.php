<?PHP

// load zipcodes page
require(SITE_ADMIN_CLASSES_DIR.'forms/states.php');
$states_frm = new states_frm;

// load zipcodes page
require(SITE_ADMIN_CLASSES_DIR.'listings/states.php');
$states_lst = new states_lst;

// write page header
$page_content = page_header('States');

// delete selected zip codes
if (!empty($_POST['delete_states'])) {
  $states_frm->delete();
}

// select page output function
switch($_GET['mode']) {
// view zip code listing
case 'view';
  $page_content .= $states_lst->listing();
break;
// edit existing zip
case 'edit';
  $stes_tbl->get_db_vars($_GET['cid']);
  $page_content .= $states_frm->edit();
break;
// check edited zip
case 'editcheck':
  $stes_tbl->get_post_vars();
  $error_message = $states_frm->form_check();
  if (empty($error_message)) {
	$stes_tbl->update();
	$page_content .= create_warning_box('<center>State Updated</center>');
	$page_content .= $states_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $states_frm->edit();
  } 
break;
// add new state
case 'add';
  $page_content .= $states_frm->add();
break;
// add new state submission check
case 'addcheck':
  $stes_tbl->get_post_vars();
  $error_message = $states_frm->form_check();
  if (empty($error_message)) {
	$stes_tbl->insert();
	$page_content .= create_warning_box('<center>New state has been added</center>');
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $states_frm->add();
  } 
break;
}

?>