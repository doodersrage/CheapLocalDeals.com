<?PHP

global $pgs_tbl,$url_nms_tbl, $dbh, $stes_tbl, $url_nms_tbl, $adv_info_tbl, $category_results_pg;

// load categories list
if (!class_exists('location_info_pg')) {
	require(CLASSES_DIR.'pages/location_info.php');
	$location_info_pg = new location_info_pg;
}

require(CLASSES_DIR.'pages/reviews.php');
$advert_reviews_page = new advert_reviews_page;
$advert_reviews_page->address = $location_info_pg->print_address();
$page_output = '<script type="text/javascript" src="includes/js/loc_inf_advert_info.js"></script>';
$page_output .= $advert_reviews_page->draw_form();

$content_arr = array();
$content_arr['$page_output$'] = $page_output;
$this->template_constants = $content_arr;

$this->page_header_title = $adv_info_tbl->company_name.' - Reviews';
$this->page_meta_description = $adv_info_tbl->company_name.' Reviews';
$this->page_meta_keywords = $adv_info_tbl->company_name.' Reviews';
$this->template_file = 'blank-new.php';

?>