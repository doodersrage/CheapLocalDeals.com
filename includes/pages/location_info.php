<?PHP

global $pgs_tbl,$url_nms_tbl, $dbh, $stes_tbl, $url_nms_tbl, $adv_info_tbl, $category_results_pg, $adv_ratings;

// load categories list
if (!class_exists('location_info_pg')) {
	require(CLASSES_DIR.'pages/location_info.php');
	$location_info_pg = new location_info_pg;
}

// build keywords list
$products_services = preg_replace('/[^a-zA-Z0-9]/', ' ', $adv_info_tbl->products_services);
	
$products_services_array = array();
$products_services_array_res = array();
$products_services_array = explode(' ',$products_services);
foreach($products_services_array as $products_services_part) {
	if (!empty($products_services_part)) {
		$products_services_array_res[] = $products_services_part;
	}
}

// put keywords list into string
$products_services = implode(' ',$products_services_array_res);

// disallowed title keywords list
$disallowed_words = array(
						'i',
						'and',
						'of',
						'the',
						'then',
						'or',
						'a',
						'in',
						'where',
						'how',
						'to'
						);

// build title keyword extension
$title_keywords = array();
$index_cnt = 0;
for($i = 0;$i <= 6;$i++) {
	if(!empty($products_services_array_res[$index_cnt])) {
		if(array_search(strtolower($products_services_array_res[$index_cnt]), $disallowed_words) > 0) {
			$i--;
		} else {
			$title_keywords[] = $products_services_array_res[$index_cnt];
		}
	}
	$index_cnt++;
}

// put title keywords into string
$title_keywords_string = implode(' ',$title_keywords);

// remove ignored punctuation
$title_keywords_strong = str_replace(array(',','.',';','?','[',']','(',')','@','/','*','<','>'),'',$title_keywords_string);

// set page header -- only assign for static header data
$page_header_title = $adv_info_tbl->company_name.' - '.$title_keywords_string.' - CheapLocalDeals.com';
$page_meta_description = preg_replace('/[^a-zA-Z0-9]/', ' ', $adv_info_tbl->company_name).'. '.$products_services.'.';
$page_meta_keywords = strtolower(str_replace('"', "'", $adv_info_tbl->company_name).' '.$products_services);

// load mid page template
if ($adv_info_tbl->id > 0) {
  $mid_temp = 'page_sects/location_info_fnd.php';
} else {
  $mid_temp = 'page_sects/location_info_ntfnd.php';
}

// start output buffer
ob_start();
  
  require(TEMPLATE_DIR.$mid_temp);
	  
  // capture outpur buffer to variable
  $mid_temp_content = ob_get_contents();

// close output buffer
ob_end_clean();

require(CLASSES_DIR.'sections/advert_reviews.php');
$advert_reviews_pg = new advert_reviews_pg;

$adv_ratings->loc_id = $_GET['loc_id'];
$adv_ratings->alt_loc_id = $_GET['alt_loc_id'];

$content_arr = array();
$content_arr['$mid_temp_content$'] = $mid_temp_content;
$content_arr['$bc$'] = $location_info_pg->bc;
$content_arr['$assign_image$'] = $location_info_pg->assign_image();
$content_arr['$print_website_lnk$'] = $location_info_pg->print_website_lnk();
$content_arr['$company_name$'] = $adv_info_tbl->company_name;
$content_arr['$print_address$'] = $location_info_pg->print_address();
$content_arr['$get_rating$'] = $adv_ratings->get_rating();
$content_arr['$social_linking$'] = $social_linking;
$content_arr['$print_products_services$'] = $location_info_pg->print_products_services();
$content_arr['$payment_methods_display$'] = $location_info_pg->payment_methods_display();
$content_arr['$build_certificate_form$'] = $location_info_pg->build_certificate_form();
$content_arr['$print_hours_of_operation$'] = $location_info_pg->print_hours_of_operation();
$content_arr['$print_description$'] = $location_info_pg->print_description();
$content_arr['$advert_reviews_pg$'] = $advert_reviews_pg->draw_form();
$content_arr['$api_user_info$'] = $location_info_pg->get_api_user_info();
$this->template_constants = $content_arr;

// set page header -- only assign for static header data
$this->page_header_title = $page_header_title;
$this->page_meta_description = $page_meta_description;
$this->page_meta_keywords = $page_meta_keywords;
$this->template_file = 'location_info.php';

?>