<?PHP

// load categories page
require(SITE_ADMIN_CLASSES_DIR.'forms/state_city_category.php');
$state_city_category_frm = new state_city_category_frm;

// load categories page
require(SITE_ADMIN_CLASSES_DIR.'listings/state_city_category.php');
$state_city_category_lst = new state_city_category_lst;

$selected_city = (isset($_GET['cid']) ? (int)$_GET['cid'] : '');
$selected_cat = (isset($_GET['ccid']) ? (int)$_GET['ccid'] : '');

// load selected cities info
$cities_tbl->get_db_vars($selected_city);

// write page header
$page_content = page_header($cities_tbl->city.' '.$cities_tbl->state.' Categories');

// get current category id
$sql_query = "SELECT
				id
			 FROM
				state_city_category
			 WHERE
				city = '".$selected_city."' AND category = '".$selected_cat."'
			 ;";
$return_cat = $dbh->queryRow($sql_query);
$set_cit_cat = $return_cat['id'];
	
// select page output function
switch ($_GET['mode']) {
// edit existing category
case 'edit':
  $ste_cty_cat_tbl->get_db_vars($set_cit_cat);
  $cats_tbl->get_db_vars($_GET['ccid']);
  $page_content .= $state_city_category_frm->edit();
break;
// check category edit submission
case 'editcheck':
  $ste_cty_cat_tbl->get_post_vars();
  $error_message = $state_city_category_frm->form_check();
  if (empty($error_message)) {
	$ste_cty_cat_tbl->update();
	$page_content .= create_warning_box('<center>Category Updated</center>');
	$page_content .= $state_city_category_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $state_city_category_frm->edit();
  } 
break;
// add new category form
case 'add':
  $page_content .= $state_city_category_frm->add();
break;
// view category list
case 'view':

  switch($_GET['action']) {
	case 'gen_cat_list':
	  $state_city_category_lst->gen_cats();
	break;
  }
  
  $page_content .= $state_city_category_lst->listing();
break;
// category add check
case 'addcheck':
  $ste_cty_cat_tbl->get_post_vars();
  $error_message = $state_city_category_frm->form_check();
  if (empty($error_message)) {
	$ste_cty_cat_tbl->insert();
	$page_content .= create_warning_box('<center>New category has been added</center>');
	$page_content .= $state_city_category_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $state_city_category_frm->add();
  } 
break;
}

?>