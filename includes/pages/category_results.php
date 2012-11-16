<?PHP

global $pgs_tbl, $category_results_pg, $dbh, $stes_tbl, $url_nms_tbl, $cats_tbl, $url_nms_tbl, $stes_tbl, $zip_cds_tbl, $cities_tbl, $geo_data, $ste_cty_cat_tbl;

// page constants
$error_message = '';

// resets search results
if(isset($_GET['search'])) {
	if($_GET['search'] == 'reset') {
		unset($_SESSION['alpha_filter']);
	}
}

// set radius data
if(empty($_SESSION['set_radius'])) {
	$_SESSION['set_radius'] = DEF_MIN_RADIUS;
}

// set radius data
if(!empty($_POST['per_page_radius'])) {
	$_SESSION['set_radius'] = $_POST['per_page_radius'];
}

// write correct radius post var
$_POST['set_radius'] = $_SESSION['set_radius'];

// set results per page 
if(!empty($_POST['per_page_settings'])) {
	$_SESSION['set_per_page_res'] = $_POST['per_page_settings'];
} elseif(empty($_SESSION['set_per_page_res'])) {
	$_SESSION['set_per_page_res'] = LISTING_PAGE_RESULTS_CNT;
}

// write correct radius post var
$_POST['per_page_settings'] = $_SESSION['set_per_page_res'];

// if zip post value were set reset zip session value
if(!empty($_POST['zip_entry'])) {
	$_SESSION['cur_zip'] = $_POST['zip_entry'];
}

if(empty($_SESSION['cur_zip'])) {
	$_SESSION['cur_zip'] = (!empty($_POST['zip_entry']) ? $_POST['zip_entry'] : '' );
}
if(empty($_GET['city'])) {
	$_GET['city'] = $geo_data->cityid;
}

// set post var befor cache assignment
if(!empty($_SESSION['cur_zip']) && empty($_GET['city'])) {
	$_POST['zip_entry'] = $_SESSION['cur_zip'];
}

// check for zip code submission
if (!empty($_POST['zip_entry'])) {

	// checked if submitted zip code exists within database
	$zip_cds_tbl->search($_POST['zip_entry']);
	
	if ($zip_cds_tbl->id > 0) {
		$_SESSION['cur_zip'] = $_POST['zip_entry'];
	} else {
		$error_message = create_warning_box('We are sorry but the zipcode you were searching for was not found.');
	}

} 

// check for drop down category selection
if(!empty($_POST['category'])) {
	if ($_POST['category'] == 'all') {
		header("Location: ".SITE_URL."view-all-results/");
	} else {
		$set_cat = (int)$_POST['category'];
		$_GET['cat'] = (int)$_POST['category'];
	}
} else {
	$set_cat = (isset($_GET['cat']) ? (int)$_GET['cat'] : '');
}

// load categories list
if (!class_exists('category_results_pg')) {
	require(CLASSES_DIR.'pages/category_results.php');
	$category_results_pg = new category_results_pg;
}

// set cat variables
$cats_tbl->get_db_vars($set_cat);

if(empty($_GET['city'])) {
	// check for search friendly name if search friendly page has not been selected
	// if url name id is found 301 redirect to search friendly page
	$cities_tbl->get_db_vars($zip_cds_tbl->city_id);
	if ($cats_tbl->url_name != '') {
		$url_nms_tbl->get_db_vars($cats_tbl->url_name);
		
		$new_uri = "/".$url_nms_tbl->url_name."/";
		$current_uri = $_SERVER["REQUEST_URI"];
			
		// check for correct url name assignment
		if ($new_uri != $current_uri && !strstr($_SERVER["REQUEST_URI"],'category_results.php') && empty($_GET['alpha']) && empty($_GET['sort'])){
			if (empty($_POST['category'])) header("HTTP/1.1 301 Moved Permanently"); 	
			header("Location: ".SITE_URL.$url_nms_tbl->url_name."/");
		}
	}
	
	// update category views on page load
	$cats_tbl->update_category_views();
	$zip_cds_tbl->search($category_results_pg->zip);
	$sql_query = "SELECT
					id
				 FROM
					states
				 WHERE
					acn = '".$cities_tbl->state."'
				 LIMIT 1 ;";
	$rows = db_memc_str($sql_query);
	$stes_tbl->get_db_vars($rows['id']);
	
	// set page header -- only assign for static header data
	if ($cats_tbl->header_title != '') {
		$page_header_title = $cats_tbl->header_title;
	} else {
		$page_header_title = 'Restaurant Coupons and Local Deals - '.$cats_tbl->category_name.' in '.$cities_tbl->city.' '.$stes_tbl->state.' ('.$stes_tbl->acn.')';
	}
	
	if ($cats_tbl->meta_description != '') {
		$page_meta_description = $cats_tbl->meta_description;
	} else {
		$page_meta_description = DEF_PAGE_META_DESC;
	}
	
	if ($cats_tbl->meta_keywords != '') {
		$page_meta_keywords = $cats_tbl->meta_keywords;
	} else {
		$page_meta_keywords = DEF_PAGE_META_KEYWORDS;
	}
	
	$no_index = 1;
	$page_content_header = $cats_tbl->header_val;
	$page_content_footer = $cats_tbl->footer;
	$page_content_listing = $category_results_pg->get_listing();

// pull city category info
} elseif(!empty($_GET['city'])) {
	
	// set session var for use elsewhere
	$_SESSION['city'] = $_GET['city'];
	
	// pull city cat details
	$ste_cty_cat_tbl->city_search($_GET['city'],(isset($_GET['cat']) ? $_GET['cat'] : ''));
	
//echo $_GET['city'].' '.$_GET['cat'];
//		echo $ste_cty_cat_tbl->id;
	
	// check for search friendly name if search friendly page has not been selected
	// if url name id is found 301 redirect to search friendly page
	if ($ste_cty_cat_tbl->url_name != '') {
		$url_nms_tbl->get_db_vars($ste_cty_cat_tbl->url_name);
		
		$new_uri = "/".$url_nms_tbl->url_name."/";
		$current_uri = $_SERVER["REQUEST_URI"];
			
		// check for correct url name assignment
		if ($new_uri != $current_uri && !strstr($_SERVER["REQUEST_URI"],'category_results.php') && empty($_GET['alpha']) && empty($_GET['sort'])){
			if (empty($_POST['category'])) header("HTTP/1.1 301 Moved Permanently"); 	
			header("Location: ".SITE_URL.$url_nms_tbl->url_name."/");
		}
	}
	
	if (strpos($_SERVER["REQUEST_URI"],'view-all-results') > 0 || strpos($_SERVER["REQUEST_URI"],'deals-of-the-month') > 0) {
		$cities_tbl->get_db_vars($_GET['city']);
		$sql_query = "SELECT
						id
					 FROM
						states
					 WHERE
						acn = '".$cities_tbl->state."'
					 LIMIT 1 ;";
		$rows = db_memc_str($sql_query);
		$stes_tbl->get_db_vars($rows['id']);
	} else {
		$cities_tbl->get_db_vars($ste_cty_cat_tbl->city);
		$stes_tbl->get_db_vars($ste_cty_cat_tbl->state);
		$cats_tbl->get_db_vars($ste_cty_cat_tbl->category);
	}
	
	// set page header -- only assign for static header data
	if ($ste_cty_cat_tbl->page_title != '') {
		$page_header_title = $ste_cty_cat_tbl->page_title;
	} else {
		if (strpos($_SERVER["REQUEST_URI"],'view-all-results') > 0) {
			$page_header_title = 'View All deals in '.$cities_tbl->city.' '.$stes_tbl->state.' ('.$stes_tbl->acn.') - Discounts, Coupons, Deals';
		} elseif(strpos($_SERVER["REQUEST_URI"],'deals-of-the-month') > 0) {
			$page_header_title = 'Deals of the month in '.$cities_tbl->city.' '.$stes_tbl->state.' ('.$stes_tbl->acn.') - Discounts, Coupons, Deals';
		} else {
			$page_header_title =  'Restaurant Coupons and Local Deals - '.$cats_tbl->category_name.' in '.$cities_tbl->city.' '.$stes_tbl->state.' ('.$stes_tbl->acn.')';
		}
	}
	
	if ($ste_cty_cat_tbl->meta_description != '') {
		$page_meta_description = $ste_cty_cat_tbl->meta_description;
	} else {
		$page_meta_description = DEF_PAGE_META_DESC;
	}
	
	if ($ste_cty_cat_tbl->meta_keywords != '') {
		$page_meta_keywords = $ste_cty_cat_tbl->meta_keywords;
	} else {
		$page_meta_keywords = DEF_PAGE_META_KEYWORDS;
	}
	
	// assign page content values
	$page_content_header = $ste_cty_cat_tbl->page_header;
	$page_content_footer = $ste_cty_cat_tbl->page_footer;
	$page_content_listing = $category_results_pg->get_listing();
	
	// if no adverts are found set page to noindex
//	if ($category_results_pg->advert_cnt == 0) $no_index = 1;
	
}

$content_arr['$error_message$'] = $error_message;
$content_arr['$page_content_header$'] = $page_content_header;
$content_arr['$bc$'] = $category_results_pg->bc;
$content_arr['$page_content_listing$'] = $page_content_listing;
$content_arr['$page_content_footer$'] = $page_content_footer;
$this->template_constants = $content_arr;

// assign header constant
$this->page_header_title = $page_header_title;
$this->page_meta_description = $page_meta_description;
$this->page_meta_keywords = $page_meta_keywords;
$this->template_file = 'category_results.php';

?>