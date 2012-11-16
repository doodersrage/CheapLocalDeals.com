<?PHP

// load settings orders class
require(SITE_ADMIN_CLASSES_DIR.'forms/orders.php');
$orders_frm = new orders_frm;

// load settings orders class
require(SITE_ADMIN_CLASSES_DIR.'listings/orders.php');
$orders_lst = new orders_lst;

// delete selected orders
if(isset($_POST['delete_orders'])) {
  $orders_frm->delete_orders();
}

// delete selected certificates
if(isset($_POST['delete_certificates'])) {
  $orders_frm->delete_certificates();
}	

// write page header
$page_content = page_header('Orders');

// select page output function
switch ($_GET['mode']) {
// edit existing category
case 'recent_orders':
  $page_content .= $orders_lst->recent_orders_listing();
break;
case 'all_orders':
  $page_content .= $orders_lst->all_orders_listing();
break;
case 'view_order':
  $page_content .= $orders_frm->view_order_info();
break;
case 'active_certificates_listing':
  if($_GET['action'] == 'disable') {
	$cert_odrs_tbl->id = $_GET['cid'];
	$cert_odrs_tbl->enabled = 0;
	$cert_odrs_tbl->disable();
  }
  $page_content .= $orders_lst->active_certificates_listing();
break;
case 'inactive_certificates_listing':
  if($_GET['action'] == 'enable') {
	$cert_odrs_tbl->id = $_GET['cid'];
	$cert_odrs_tbl->enabled = 1;
	$cert_odrs_tbl->disable();
  }
  $page_content .= $orders_lst->inactive_certificates_listing();
break;
case 'processed_advertiser_mems':
  $page_content .= $orders_lst->processed_advertiser_mems_listing();
break;
}

?>