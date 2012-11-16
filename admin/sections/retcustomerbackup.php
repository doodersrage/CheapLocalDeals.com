<?PHP

// load retail_customers page
require(SITE_ADMIN_CLASSES_DIR.'retail_customers_backup.php');
$retail_customers_page = new retail_customers;

// write page header
$page_content = page_header('Advertisers');

// deleted selected items
if(isset($_POST['undelete_advertiser'])) {
  if(is_array($_POST['undelete_advertiser'])) {
	$retail_customers_page->undelete_advertiser();
  }
}

// deleted selected items
if(isset($_POST['delete_advertiser'])) {
  if(is_array($_POST['delete_advertiser'])) {
	foreach($_POST['delete_advertiser'] as $id => $del_advert) {
	  $stmt = $dbh->prepare("DELETE FROM advertiser_info_backup WHERE id = '".$del_advert."';");
	  $stmt->execute();
	}
  }
}

// select page output function
switch($_GET['mode']) {
case 'view';
  $page_content .= $retail_customers_page->retail_customers_listing();
break;
case 'payment_problems';
  $page_content .= $retail_customers_page->retail_customers_paymentprob();
break;
case 'edit';
  $adv_info_bu_tbl->get_db_vars($_GET['cid']);
  $page_content .= $retail_customers_page->edit_retail_customer();
break;
case 'editcheck';
  $adv_info_bu_tbl->get_post_vars();
  $error_message = $retail_customers_page->form_check();
  if (empty($error_message)) {
	$adv_info_bu_tbl->update();
	$adv_info_bu_tbl->insert_selected_categories($_POST['category_select'],$adv_info_bu_tbl->id);
	$page_content .= create_warning_box('<center>Advertiser Updated</center>');
	$page_content .= $retail_customers_page->retail_customers_listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $retail_customers_page->edit_retail_customer();
  } 
break;
}

?>