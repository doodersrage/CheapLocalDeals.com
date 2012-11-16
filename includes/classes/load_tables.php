<?PHP

// this document loads all available table classes and makes them available to other portions of the code

// include google maps class
require(CLASSES_DIR.'googmap.php');

// this document loads table classes
if (!class_exists('cats_tbl')) {
	// include categories db class
	require(CLASSES_DIR.'tables/cats_tbl.php');
	$cats_tbl = new cats_tbl;
}

// loads cert_amt_tbl class for modifying available certificate amounts
if (!class_exists('cert_amt_tbl')) {
	// include cert_amt_tbl class
	require(CLASSES_DIR.'tables/cert_amt_tbl.php');
	$cert_amt_tbl = new cert_amt_tbl;
}

// loads adv_lvls_tbl class for interacting with advertiser level data
if (!class_exists('adv_lvls_tbl')) {
	// include adv_lvls_tbl db class
	require(CLASSES_DIR.'tables/adv_lvls_tbl.php');
	$adv_lvls_tbl = new adv_lvls_tbl;
}

// loads customers_table class for inserting, loading, editing customer data
if (!class_exists('customers_table')) {
	// include customers_table db class
	require(CLASSES_DIR.'tables/customers_table.php');
	$customer_info_table = new customers_table;
}

// loads adv_info_tbl class for storing/loading advertiser information
if (!class_exists('adv_info_tbl')) {
	// include adv_info_tbl db class
	require(CLASSES_DIR.'tables/adv_info_tbl.php');
	$adv_info_tbl = new adv_info_tbl;
}

// loads adv_pmt_mtds_tbl class for adding, viewing, modifying advertiser payment method options
if (!class_exists('adv_pmt_mtds_tbl')) {
	// include adv_pmt_mtds_tbl db class
	require(CLASSES_DIR.'tables/adv_pmt_mtds_tbl.php');
	$adv_pmt_mtds_tbl = new adv_pmt_mtds_tbl;
}

// loads the pgs_tbl class for writing and loading flat pages
if (!class_exists('pgs_tbl')) {
	// include pgs_tbl db class
	require(CLASSES_DIR.'tables/pgs_tbl.php');
	$pgs_tbl = new pgs_tbl;
}

// loads the url_nms_tbl class for storing and interacting with search friendly names
if (!class_exists('url_nms_tbl')) {
	// include url names db class
	require(CLASSES_DIR.'tables/url_nms_tbl.php');
	$url_nms_tbl = new url_nms_tbl;
}

// loads the zip_cds_tbl class for doing nothing really
if (!class_exists('zip_cds_tbl')) {
	// include zip_cds_tbl db class
	require(CLASSES_DIR.'tables/zip_cds_tbl.php');
	$zip_cds_tbl = new zip_cds_tbl;
}

// loads the odrs_tbl class for holding order data
if (!class_exists('odrs_tbl')) {
	// include odrs_tbl db class
	require(CLASSES_DIR.'tables/odrs_tbl.php');
	$odrs_tbl = new odrs_tbl;
}

// loads the odr_itms_tbl class for handling items within an order
if (!class_exists('odr_itms_tbl')) {
	// include odr_itms_tbl db class
	require(CLASSES_DIR.'tables/odr_itms_tbl.php');
	$odr_itms_tbl = new odr_itms_tbl;
}

// loads the cert_odrs_tbl class for handling customer ordered certificates
if (!class_exists('cert_odrs_tbl')) {
	// include cert_odrs_tbl db class
	require(CLASSES_DIR.'tables/cert_odrs_tbl.php');
	$cert_odrs_tbl = new cert_odrs_tbl;
}

// loads the memb_proc_tbl class for use processing advertiser membership payments
if (!class_exists('memb_proc_tbl')) {
	// include memb_proc_tbl db class
	require(CLASSES_DIR.'tables/memb_proc_tbl.php');
	$memb_proc_tbl = new memb_proc_tbl;
}

// loads the memb_proc_fld_tbl class for use processing advertiser membership payments
if (!class_exists('memb_proc_fld_tbl')) {
	// include memb_proc_tbl db class
	require(CLASSES_DIR.'tables/memb_proc_fld_tbl.php');
	$memb_proc_fld_tbl = new memb_proc_fld_tbl;
}

// loads the aff_usrs_tbl class for use with the affiliates sub system
if (!class_exists('aff_usrs_tbl')) {
	// include aff_usrs_tbl db class
	require(CLASSES_DIR.'tables/aff_usrs_tbl.php');
	$aff_usrs_tbl = new aff_usrs_tbl;
}

// loads the stes_tbl class
if (!class_exists('stes_tbl')) {
	// include stes_tbl db class
	require(CLASSES_DIR.'tables/stes_tbl.php');
	$stes_tbl = new stes_tbl;
}

// loads the cities_tbl class
if (!class_exists('cities_tbl')) {
	// include cities_tbl db class
	require(CLASSES_DIR.'tables/cities_tbl.php');
	$cities_tbl = new cities_tbl;
}

// loads the ste_cty_cat_tbl class
if (!class_exists('ste_cty_cat_tbl')) {
	// include ste_cty_cat_tbl db class
	require(CLASSES_DIR.'tables/ste_cty_cat_tbl.php');
	$ste_cty_cat_tbl = new ste_cty_cat_tbl;
}

// loads the cust_pmt_mtds_tbl class
if (!class_exists('cust_pmt_mtds_tbl')) {
	// include cust_pmt_mtds_tbl db class
	require(CLASSES_DIR.'tables/cust_pmt_mtds_tbl.php');
	$cust_pmt_mtds_tbl = new cust_pmt_mtds_tbl;
}

// loads the adv_info_bu_tbl class
if (!class_exists('adv_info_bu_tbl')) {
	// include adv_info_bu_tbl db class
	require(CLASSES_DIR.'tables/adv_info_bu_tbl.php');
	$adv_info_bu_tbl = new adv_info_bu_tbl;
}

// loads the pp_pmts_tbl class
if (!class_exists('pp_pmts_tbl')) {
	// include pp_pmts_tbl db class
	require(CLASSES_DIR.'tables/pp_pmts_tbl.php');
	$pp_pmts_tbl = new pp_pmts_tbl;
}

// added 9/10/2009 as requested by Cynar to add in advertiser promo codes
// loads the adv_pro_codes_tbl class
if (!class_exists('adv_pro_codes_tbl')) {
	// include adv_pro_codes_tbl db class
	require(CLASSES_DIR.'tables/adv_pro_codes_tbl.php');
	$adv_pro_codes_tbl = new adv_pro_codes_tbl;
}

// added 9/10/2009 as requested by Cynar to add in customer promo codes
// loads the cust_promo_cds_tbl class
if (!class_exists('cust_promo_cds_tbl')) {
	// include cust_promo_cds_tbl db class
	require(CLASSES_DIR.'tables/cust_promo_cds_tbl.php');
	$cust_promo_cds_tbl = new cust_promo_cds_tbl;
}

// added 9/15/2009 added to allow advertisers multiple locations entry
// loads the advertiser_alt_locations class
if (!class_exists('adv_alt_loc_tbl')) {
	// include adv_alt_loc_tbl db class
	require(CLASSES_DIR.'tables/adv_alt_loc_tbl.php');
	$adv_alt_loc_tbl = new adv_alt_loc_tbl;
}

// added 9/23/2009 added to enable reviews of advertisers
// loads the advertiser_reviews class
if (!class_exists('adv_rvws_tbl')) {
	// include adv_alt_loc_tbl db class
	require(CLASSES_DIR.'tables/adv_rvws_tbl.php');
	$adv_rvws_tbl = new adv_rvws_tbl;
}

// added 10/15/2009 added to display non-certificate offering advertisers
// loads the bus_tbl class
if (!class_exists('bus_tbl')) {
	// include bus_tbl db class
	require(CLASSES_DIR.'tables/bus_tbl.php');
	$bus_tbl = new bus_tbl;
}

// added 10/22/2009 added for handling customer coupon entries
// loads the customer_coupons class
if (!class_exists('cust_cpns_tbl')) {
	// include customer_coupons db class
	require(CLASSES_DIR.'tables/cust_cpns_tbl.php');
	$cust_cpns_tbl = new cust_cpns_tbl;
}

// added 12/07/2009 added for handling api_access entries
// loads the api_access class
if (!class_exists('api_acc_tbl')) {
	// include api_access db class
	require(CLASSES_DIR.'tables/api_acc_tbl.php');
	$api_acc_tbl = new api_acc_tbl;
}

// added 3/11/2010 added for handling cust_ref_info_tbl
// loads the api_access class
if (!class_exists('cust_ref_info_tbl')) {
	// include api_access db class
	require(CLASSES_DIR.'tables/cust_ref_info_tbl.php');
	$cust_ref_info_tbl = new cust_ref_info_tbl;
}

// added 7/1/2010 added for handling api_ref_track_tbl
// loads the api_ref_track_tbl class
if (!class_exists('api_ref_track_tbl')) {
	// include api_ref_track_tbl db class
	require(CLASSES_DIR.'tables/api_ref_track.php');
	$api_ref_track_tbl = new api_ref_track_tbl;
}

?>