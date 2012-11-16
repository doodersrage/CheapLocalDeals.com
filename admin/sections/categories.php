<?PHP

// load categories page
require(SITE_ADMIN_CLASSES_DIR.'listings/categories.php');
$categories_lst = new categories_lst;

// load categories page
require(SITE_ADMIN_CLASSES_DIR.'forms/categories.php');
$categories_frm = new categories_frm;

// write page header
$page_content = page_header('Categories');

// delete selected categories
if(isset($_POST['delete_selected'])) {
  if ($_POST['delete_selected'] == 1 && !empty($_POST['delete_cat'])) {
	$categories_frm->delete_category();
  }

}

// select page output function
switch ($_GET['mode']) {
// edit existing category
case 'edit':
  $cats_tbl->get_db_vars($_GET['cid']);
  $page_content .= $categories_frm->edit();
break;
// check category edit submission
case 'editcheck':
  $cats_tbl->get_post_vars();
  $error_message = $categories_frm->form_check();
  if (empty($error_message)) {
	$cats_tbl->update();
	$page_content .= create_warning_box('<center>Category Updated</center>');
	$page_content .= $categories_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $categories_frm->edit();
  } 
break;
// add new category form
case 'add':
  $page_content .= $categories_frm->add();
break;
// view category list
case 'view':
  $page_content .= $categories_lst->listing();
break;
// view page hits
case 'viewhits':
  $page_content .= $categories_lst->category_hit_listing();
break;
// category add check
case 'addcheck':
  $cats_tbl->get_post_vars();
  $error_message = $categories_frm->form_check();
  if (empty($error_message)) {
	$cats_tbl->insert();
	$page_content .= create_warning_box('<center>New category has been added</center>');
	$page_content .= $categories_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $categories_frm->add();
  } 
break;
}

?>