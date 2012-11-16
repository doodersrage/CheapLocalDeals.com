<?PHP

// load certificates amount page
require(SITE_ADMIN_CLASSES_DIR.'forms/customer_promo_codes.php');
$customer_promo_codes_frm = new customer_promo_codes_frm;

// load certificates amount page
require(SITE_ADMIN_CLASSES_DIR.'listings/customer_promo_codes.php');
$customer_promo_codes_lst = new customer_promo_codes_lst;

// load retail_customers page
require(SITE_ADMIN_CLASSES_DIR.'forms/reg_customers.php');
$reg_customers_frm = new reg_customers_frm;

// load retail_customers page
require(SITE_ADMIN_CLASSES_DIR.'listings/reg_customers.php');
$reg_customers_lst = new reg_customers_lst;

// load balance coupons page
require(SITE_ADMIN_CLASSES_DIR.'forms/balance_coupons.php');
$balance_coupons_frm = new balance_coupons_frm;

// load balance coupons page
require(SITE_ADMIN_CLASSES_DIR.'listings/balance_coupons.php');
$balance_coupons_lst = new balance_coupons_lst;

// write page header
$page_content = page_header('Customers');

// delete selected regular customers
if (!empty($_POST['delete_regcustomer'])) {
  $reg_customers_frm->delete();
}

// deleted selected promo codes
if(isset($_POST['delete_customer_promo_codes'])) {
  $customer_promo_codes_frm->delete();
}

if(isset($_POST['delete_coupons'])) {
  if(is_array($_POST['delete_coupons'])) {
	$balance_coupons_frm->delete();
  }
}

// select page output function
switch($_GET['mode']) {
case 'printcsv';
  $page_content .= $reg_customers_frm->generate_csv();
break;
case 'add';
  $page_content .= $reg_customers_frm->add();
break;
case 'addcheck';
  $customer_info_table->get_post_vars();
  $error_message = $reg_customers_frm->form_check();
  if (empty($error_message)) {
	$customer_info_table->insert();
	$page_content .= create_warning_box('<center>New regular customer has been added</ccenter>');
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $reg_customers_frm->add();
  } 
break;
case 'view';
  $page_content .= $reg_customers_lst->listing();
break;
case 'edit';
  $customer_info_table->get_db_vars($_GET['cid']);
  $page_content .= $reg_customers_frm->edit();
break;
case 'editcheck';
  $customer_info_table->get_post_vars();
  $error_message = $reg_customers_frm->form_check();
  if (empty($error_message)) {
	$customer_info_table->update();
	$page_content .= create_warning_box('<center>Regular Customer Updated</ccenter>');
	$page_content .= $reg_customers_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $reg_customers_frm->edit();
  } 
break;
case 'newcustomers';
  $page_content .= $reg_customers_lst->reg_customers_listing_new();
break;
case 'promocodes';
  $page_content .= $customer_promo_codes_lst->listing();
break;
case 'promocodesnew';
  $page_content .= $customer_promo_codes_frm->add();
break;
case 'promocodesnewcheck';
  $cust_promo_cds_tbl->get_post_vars();
  $error_message = $customer_promo_codes_frm->form_check();
  if (empty($error_message) && $cust_promo_cds_tbl->promo_code_chk($cust_promo_cds_tbl->promo_code) == 0) {
	$cust_promo_cds_tbl->insert();
	$page_content .= create_warning_box('<center>New promo code has been added</center>');
	$page_content .= $customer_promo_codes_lst->listing();
  } else {
	if ($cust_promo_cds_tbl->promo_code_chk($cust_promo_cds_tbl->promo_code) > 0) $error_message .= '<br>This promo code already exists.';
	$page_content .= create_warning_box($error_message);
	$page_content .= $customer_promo_codes_frm->add();
  } 
break;
case 'promocodesedit';
  $cust_promo_cds_tbl->get_db_vars($_GET['cid']);
  $page_content .= $customer_promo_codes_frm->edit();
break;
case 'promocodeseditcheck';
  $cust_promo_cds_tbl->get_post_vars();
  $error_message = $customer_promo_codes_frm->form_check();
  if (empty($error_message)) {
	$cust_promo_cds_tbl->update();
	$page_content .= create_warning_box('<center>Promo Code Updated</center>');
	$page_content .= $customer_promo_codes_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $customer_promo_codes_frm->edit();
  } 
break;
case 'balancecoupons';
  $page_content .= $balance_coupons_lst->listing();
break;
case 'balancecouponsadd';
  $page_content .= $balance_coupons_frm->add();
break;
case 'balancecouponsaddcheck';
  $cust_cpns_tbl->get_post_vars();
  $error_message = $balance_coupons_frm->form_check();
  if (empty($error_message)) {
	$cust_cpns_tbl->insert();
	$page_content .= create_warning_box('<center>New balance coupon has been added</center>');
	$page_content .= $balance_coupons_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $balance_coupons_frm->add();
  } 
break;
case 'balancecouponsedit';
  $cust_cpns_tbl->get_db_vars($_GET['pid']);
  $page_content .= $balance_coupons_frm->edit();
break;
case 'balancecouponseditcheck';
  $cust_cpns_tbl->get_post_vars();
  $error_message = $balance_coupons_frm->form_check();
  if (empty($error_message)) {
	$cust_promo_cds_tbl->update();
	$page_content .= create_warning_box('<center>Balance Coupon Updated</center>');
	$page_content .= $balance_coupons_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $balance_coupons_frm->edit();
  } 
break;
}

?>