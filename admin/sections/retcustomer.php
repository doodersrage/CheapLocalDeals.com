<?PHP
// load certificates amount page
require(SITE_ADMIN_CLASSES_DIR.'forms/certificate_amount.php');
$certificate_amount_frm = new certificate_amount_frm;

// load certificates amount page
require(SITE_ADMIN_CLASSES_DIR.'listings/certificate_amount.php');
$certificate_amount_lst = new certificate_amount_lst;

// load certificates amount page
require(SITE_ADMIN_CLASSES_DIR.'listings/advertiser_levels.php');
$advertiser_levels_lst = new advertiser_levels_lst;

// load certificates amount page
require(SITE_ADMIN_CLASSES_DIR.'forms/advertiser_levels.php');
$advertiser_levels_frm = new advertiser_levels_frm;

// load ad_payment_methods page
require(SITE_ADMIN_CLASSES_DIR.'forms/ad_payment_methods.php');
$ad_payment_methods_frm = new ad_payment_methods_frm;

// load ad_payment_methods listings page
require(SITE_ADMIN_CLASSES_DIR.'listings/ad_payment_methods.php');
$ad_payment_methods_lst = new ad_payment_methods_lst;

// load ad_payment_methods page
require(SITE_ADMIN_CLASSES_DIR.'forms/advert_promo_codes.php');
$advert_promo_codes_frm = new advert_promo_codes_frm;

// load ad_payment_methods page
require(SITE_ADMIN_CLASSES_DIR.'listings/advert_promo_codes.php');
$advert_promo_codes_lst = new advert_promo_codes_lst;

// load ad_payment_methods page
require(SITE_ADMIN_CLASSES_DIR.'forms/noncert_adverts.php');
$noncert_adverts_frm = new noncert_adverts_frm;

// load ad_payment_methods page
require(SITE_ADMIN_CLASSES_DIR.'listings/noncert_adverts.php');
$noncert_adverts_lst = new noncert_adverts_lst;

// load retail_customers page
require(SITE_ADMIN_CLASSES_DIR.'forms/retail_customers.php');
$retail_customers_frm = new retail_customers_frm;

// load retail_customers page
require(SITE_ADMIN_CLASSES_DIR.'listings/retail_customers.php');
$retail_customers_lst = new retail_customers_lst;

// load advertiser reviews page
require(SITE_ADMIN_CLASSES_DIR.'listings/reviews.php');
$advert_reviews_lst = new advert_reviews_lst;

// write page header
$page_content = page_header('Advertisers');

// deleted selected advertisers
if(isset($_POST['delete_advertiser'])) {
  $retail_customers_frm->delete_move_advert_backup();
}

// mass update staff pick settings
if(isset($_POST['staff_picks'])) {
  $retail_customers_frm->update_staff_picks();
}

// deleted selected promo codes
if(isset($_POST['delete_advert_promo_codes'])) {
  $advert_promo_codes_frm->delete();
}

// deleted selected noncert adverts
if(isset($_POST['delete_noncertadvertiser'])) {
	$noncert_adverts_frm->delete();
}

// deleted selected promo codes
if(isset($_POST['delete_advertiser_payment_methods'])) {
  $ad_payment_methods_frm->delete();
}

// approve selected advertisers
if(isset($_POST['approve_advertiser'])) {
  $retail_customers_frm->approve_advertisers();
}

// approve selected advertisers
if(isset($_POST['change_approve_advertiser'])) {
	$retail_customers_frm->change_approval();
}

// apply selected review updates
if(isset($_POST['apply_review_changes'])) {
	$retail_customers_frm->apply_review_changes();
}

// select page output function
switch($_GET['mode']) {
case 'add';
  $page_content .= $retail_customers_frm->add();
break;
case 'printcsv';
  $page_content .= $retail_customers_frm->generate_csv();
break;
case 'addcheck';
  $adv_info_tbl->get_post_vars();
  $error_message = $retail_customers_frm->form_check();
  if (empty($error_message)) {
	$adv_info_tbl->insert();
	// get new order_id
	$sql_query = "SELECT
				id
			 FROM
				advertiser_info
			 ORDER BY id DESC
			 LIMIT 1
			 ;";
	$rows = $dbh->queryRow($sql_query);
	
	$new_advertiser_id = $rows['id'];
	$adv_info_tbl->insert_selected_categories($_POST['category_select'],$new_advertiser_id);
	$page_content .= create_warning_box('<center>New retail customer has been added</center>');
	$page_content .= $retail_customers_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $retail_customers_frm->add();
  } 
break;
case 'view';
  $page_content .= $retail_customers_lst->listing();
break;
case 'payment_problems';
  $page_content .= $retail_customers_lst->retail_customers_paymentprob();
break;
case 'pending_approval';
  $page_content .= $retail_customers_lst->retail_customers_pending_approval_listing();
break;
case 'change_pending_approval';
  $page_content .= $retail_customers_lst->retail_customers_change_pending_approval_listing();
break;
case 'edit';
  $adv_info_tbl->get_db_vars($_GET['cid']);
  $page_content .= $retail_customers_frm->edit();
break;
case 'editcheck';
  $adv_info_tbl->get_post_vars();
  $error_message = $retail_customers_frm->form_check();
  if (empty($error_message)) {
	$adv_info_tbl->update();
	$adv_info_tbl->insert_selected_categories($_POST['category_select'],$adv_info_tbl->id);
	$page_content .= create_warning_box('<center>Advertiser Updated</center>');
	$page_content .= $retail_customers_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $retail_customers_frm->edit();
  } 
break;
case 'newcustomers';
  $page_content .= $retail_customers_lst->retail_customers_listing_new();
break;
case 'certificateamount';
  $page_content .= $certificate_amount_lst->listing();
break;
case 'certificateamountnew';
  $page_content .= $certificate_amount_frm->add();
break;
case 'certificateamountnewcheck';
  $cert_amt_tbl->get_post_vars();
  $error_message = $certificate_amount_frm->form_check();
  if (empty($error_message)) {
	$cert_amt_tbl->insert();
	$page_content .= create_warning_box('<center>New certificate amount has been added</center>');
	$page_content .= $certificate_amount_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $certificate_amount_frm->add();
  } 
break;
case 'certificateamountedit';
  $cert_amt_tbl->get_db_vars($_GET['cid']);
  $page_content .= $certificate_amount_frm->edit();
break;
case 'certificateamounteditcheck';
  $cert_amt_tbl->get_post_vars();
  $error_message = $certificate_amount_frm->form_check();
  if (empty($error_message)) {
	$cert_amt_tbl->update();
	$page_content .= create_warning_box('<center>Certificate Amount Updated</center>');
	$page_content .= $certificate_amount_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $certificate_amount_frm->edit();
  } 
break;
case 'customerlevels';
  $page_content .= $advertiser_levels_lst->listing();
break;
case 'customerlevelsnew';
  $page_content .= $advertiser_levels_frm->add();
break;
case 'customerlevelsnewcheck';
  $adv_lvls_tbl->get_post_vars();
  $error_message = $advertiser_levels_frm->form_check();
  if (empty($error_message)) {
	$adv_lvls_tbl->insert();
	$page_content .= create_warning_box('<center>New customer level has been added</center>');
	$page_content .= $advertiser_levels_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $advertiser_levels_frm->add();
  } 
break;
case 'customerlevelsedit';
  $adv_lvls_tbl->get_db_vars($_GET['cid']);
  $page_content .= $advertiser_levels_frm->edit();
break;
case 'customerlevelseditcheck';
  $adv_lvls_tbl->get_post_vars();
  $error_message = $advertiser_levels_frm->form_check();
  if (empty($error_message)) {
	$adv_lvls_tbl->update();
	$page_content .= create_warning_box('<center>Customer Level Updated</center>');
	$page_content .= $advertiser_levels_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $advertiser_levels_frm->edit();
  } 
break;
case 'paymentmethods';
  $page_content .= $ad_payment_methods_lst->listing();
break;
case 'paymentmethodsnew';
  $page_content .= $ad_payment_methods_frm->add();
break;
case 'paymentmethodsnewcheck';
  $adv_pmt_mtds_tbl->get_post_vars();
  $error_message = $ad_payment_methods_frm->form_check();
  if (empty($error_message)) {
	$adv_pmt_mtds_tbl->insert();
	$page_content .= create_warning_box('<center>New payment method has been added</center>');
	$page_content .= $ad_payment_methods_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $ad_payment_methods_frm->add();
  } 
break;
case 'paymentmethodsedit';
  $adv_pmt_mtds_tbl->get_db_vars($_GET['cid']);
  $page_content .= $ad_payment_methods_frm->edit();
break;
case 'paymentmethodseditcheck';
  $adv_pmt_mtds_tbl->get_post_vars();
  $error_message = $ad_payment_methods_frm->form_check();
  if (empty($error_message)) {
	$adv_pmt_mtds_tbl->update();
	$page_content .= create_warning_box('<center>Payment Method Updated</center>');
	$page_content .= $ad_payment_methods_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $ad_payment_methods_frm->edit();
  } 
break;
case 'promocodes';
  $page_content .= $advert_promo_codes_lst->listing();
break;
case 'promocodesnew';
  $page_content .= $advert_promo_codes_frm->add();
break;
case 'promocodesnewcheck';
  $adv_pro_codes_tbl->get_post_vars();
  $error_message = $advert_promo_codes_frm->form_check();
  if (empty($error_message) && $adv_pro_codes_tbl->promo_code_chk($adv_pro_codes_tbl->promo_code) == 0) {
	$adv_pro_codes_tbl->insert();
	$page_content .= create_warning_box('<center>New promo code has been added</center>');
	$page_content .= $advert_promo_codes_lst->listing();
  } else {
	if ($adv_pro_codes_tbl->promo_code_chk($adv_pro_codes_tbl->promo_code) > 0) $error_message .= '<br>This promo code already exists.';
	$page_content .= create_warning_box($error_message);
	$page_content .= $advert_promo_codes_frm->add();
  } 
break;
case 'promocodesedit';
  $adv_pro_codes_tbl->get_db_vars($_GET['cid']);
  $page_content .= $advert_promo_codes_frm->edit();
break;
case 'promocodeseditcheck';
  $adv_pro_codes_tbl->get_post_vars();
  $error_message = $advert_promo_codes_frm->form_check();
  if (empty($error_message)) {
	$adv_pro_codes_tbl->update();
	$page_content .= create_warning_box('<center>Promo Code Updated</center>');
	$page_content .= $advert_promo_codes_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $advert_promo_codes_frm->edit();
  } 
break;
case 'reviews';
  $page_content .= $advert_reviews_lst->listing();
break;
case 'noncertadverts';
  $page_content .= $noncert_adverts_lst->listing();
break;
case 'editnoncert';
  $page_content .= $bus_tbl->get_db_vars($_GET['cid']);
  $page_content .= $noncert_adverts_frm->edit();
break;
case 'addnoncert';
  $page_content .= $noncert_adverts_frm->add();
break;
case 'editnoncertaddcheck';
  $bus_tbl->get_post_vars();
  $error_message = $noncert_adverts_frm->form_check();
  if (empty($error_message)) {
	$bus_tbl->insert();
	$page_content .= create_warning_box('<center>New Non-certificate Advertiser has been added</center>');
	$page_content .= $noncert_adverts_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $noncert_adverts_frm->add();
  } 
break;
case 'editnoncerteditcheck';
  $bus_tbl->get_post_vars();
  $error_message = $noncert_adverts_frm->form_check();
  if (empty($error_message)) {
	$bus_tbl->update();
	$page_content .= create_warning_box('<center>Non-Certificate Advertiser Updated</center>');
	$page_content .= $noncert_adverts_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $noncert_adverts_frm->edit();
  } 
break;
}

?>