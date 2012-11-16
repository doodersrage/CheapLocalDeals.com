<?PHP

// load pages
require(SITE_ADMIN_CLASSES_DIR.'forms/pages.php');
$pages_frm = new pages_frm;

// load pages
require(SITE_ADMIN_CLASSES_DIR.'listings/pages.php');
$pages_lst = new pages_lst;

// write page header
$page_content = page_header('Static Pages');

// delete selected pages
if ($_POST['delete_selected'] == 1) {
  $pages_frm->delete();
}

// select page output function
switch ($_GET['mode']) {
// edit existing pages
case 'edit':
  $pgs_tbl->get_db_vars($_GET['pid']);
  $page_content .= $pages_frm->edit();
break;
// check pages edit submission
case 'editcheck':
  $pgs_tbl->get_post_vars();
  $error_message = $pages_frm->form_check();
  if (empty($error_message)) {
	$pgs_tbl->update();
	$page_content .= create_warning_box('<center>Page Updated</center>');
	$page_content .= $pages_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $pages_frm->edit();
  } 
break;
// add new pages form
case 'add':
  $page_content .= $pages_frm->add();
break;
// view pages list
case 'view':
  $page_content .= $pages_lst->listing();
break;
// pages add check
case 'addcheck':
  $pgs_tbl->get_post_vars();
  $error_message = $pages_frm->form_check();
  if (empty($error_message)) {
	$pgs_tbl->insert();
	$page_content .= create_warning_box('<center>New page has been added</center>');
	$page_content .= $pages_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $pages_frm->add();
  } 
break;
}

?>