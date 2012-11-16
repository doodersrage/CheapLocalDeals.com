<?PHP

global $pgs_tbl,$url_nms_tbl, $dbh, $stes_tbl, $url_nms_tbl, $adv_info_tbl, $category_results_pg, $zip_cds_tbl, $cities_tbl, $url_nms_tbl, $ste_cty_cat_tbl;

// reset search session vars
unset($_SESSION['alpha_filter']);
unset($_SESSION['cur_results_search']);

if (!empty($_GET['city'])) {
  $_SESSION['city'] = $_GET['city'];
  unset($_SESSION['cur_zip']);
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

// set page globals
$page_description = '';
$page_header = '';
$header_text = '';

  // set city values
  $cities_tbl->get_db_vars($_SESSION['city']);
  
  // check for search friendly name if search friendly page has not been selected
  // if url name id is found 301 redirect to search friendly page
  if ($cities_tbl->url_name != '' && strstr($_SERVER["REQUEST_URI"],'results.deal')) {
	  $url_nms_tbl->get_db_vars($cities_tbl->url_name);
	  header( "HTTP/1.1 301 Moved Permanently" ); 	
	  header("Location: ".SITE_URL.$url_nms_tbl->url_name."/");
  }
  
  // category list green header
  $green_cat_list_head = '<div class="green_cat_list_head">'.htmlentities($cities_tbl->city.' Restaurants & Local Deals in '.$cities_tbl->city.', '.$cities_tbl->state).':</div>';
  
  // set page header -- only assign for static header data
  if ($cities_tbl->page_title != '') {
	  $page_header_title = $cities_tbl->page_title;
  } else {
	  $page_header_title = $cities_tbl->city.' Restaurant Coupons and Local Deals | '.$cities_tbl->state;
  }
  
  // set header keywords
  if ($cities_tbl->meta_keywords != '') {
	  $page_meta_keywords = $cities_tbl->meta_keywords;
  } else {
	  $sql_query = "SELECT
					  state
				   FROM
					  states
				   WHERE
					  acn = ?
				   LIMIT 1;";
  
	  $values = array(
					  $cities_tbl->state
					  );
	  
	  $stmt = $dbh->prepare($sql_query);					 
	  $result = $stmt->execute($values);
	  $row_sel_city = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
	  $result->free();
	  
	  $page_meta_keywords = $cities_tbl->city.' '.$row_sel_city['state'].' ('.$cities_tbl->state.') local deals restaurants shopping coupons directory businesses';
  }
  
  if ($cities_tbl->meta_description != '') {
	  $page_meta_description = $cities_tbl->meta_description;
  } else {
	  $page_meta_description = 'Find discounts and coupon offers from local businesses within '.$cities_tbl->city.', '.$cities_tbl->state.'.';
  }
  
  
  // set page content
  $page_header = $cities_tbl->page_header;
  $page_description = $cities_tbl->page_footer;
	  
  //		 set zip code views
  $page_count = get_results_page_hit_cnt();
  
  // set city values
  $cities_tbl->get_db_vars((int)$_GET['city']);
  
  // pull city zipcode list
  $zip_cds_tbl->city_id = $cities_tbl->id;
  $zip_array = $zip_cds_tbl->get_list();
  $zip_array = $zip_cds_tbl->fetchZipsInRadiusByZip($zip_array[0], $_SESSION['set_radius'], 100);
  
  // added for results page pop link
  $ste_cty_cat_tbl->city_category_search((int)$_GET['city'], 48);
  $url_nms_tbl->assign_parent_type_db_vars($ste_cty_cat_tbl->id , 'citiescate');
  $rest_pop_lnk = SITE_URL.$url_nms_tbl->url_name.'/';
  $ste_cty_cat_tbl->city_category_search((int)$_GET['city'], 32);
  $url_nms_tbl->assign_parent_type_db_vars($ste_cty_cat_tbl->id , 'citiescate');
  $auto_pop_lnk = SITE_URL.$url_nms_tbl->url_name.'/';
  $ste_cty_cat_tbl->city_category_search((int)$_GET['city'], 27);
  $url_nms_tbl->assign_parent_type_db_vars($ste_cty_cat_tbl->id , 'citiescate');
  $entertain_pop_lnk = SITE_URL.$url_nms_tbl->url_name.'/';
  $ste_cty_cat_tbl->city_category_search((int)$_GET['city'], 39);
  $url_nms_tbl->assign_parent_type_db_vars($ste_cty_cat_tbl->id , 'citiescate');
  $person_pop_lnk = SITE_URL.$url_nms_tbl->url_name.'/';
  
// load categories list
if (!class_exists('results_pg')) {
	require(CLASSES_DIR.'pages/results.php');
	$results_pg = new results_pg;
}

// get page listing content
$results_pg->zip_array = $zip_array;
$page_listing_text = $results_pg->list_categories();

// if no adverts are found set page to noindex
if ($results_pg->advert_cnt == 0) $no_index = 1;

$content_arr = array();

$content_arr['$rest_pop_lnk$'] = $rest_pop_lnk;
$content_arr['$auto_pop_lnk$'] = $auto_pop_lnk;
$content_arr['$entertain_pop_lnk$'] = $entertain_pop_lnk;
$content_arr['$person_pop_lnk$'] = $person_pop_lnk;

$content_arr['$cur_results_search$'] = (isset($_POST['cur_results_search']) ? $_POST['cur_results_search'] : '');
$content_arr['$fav_lnk$'] = SITE_URL.$results_pg->fav_lnk;
$content_arr['$page_count$'] = number_format($page_count);
$content_arr['$page_description$'] = ($api_load->status != 1 ? $page_description : '');
$content_arr['$view_all_link$'] = SITE_URL.$results_pg->view_all_link;
$content_arr['$page_listing_text$'] = $page_listing_text;
$content_arr['$green_cat_list_head$'] = $green_cat_list_head;
$content_arr['$page_header$'] = ($api_load->status != 1 ? $page_header : '');
$this->template_constants = $content_arr;

$this->page_header_title = $page_header_title;
$this->page_meta_description = $page_meta_description;
$this->page_meta_keywords = $page_meta_keywords;
$this->template_file = 'results.php';

?>