<?PHP
// load application header
require('includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if HTTPS page load request is made redirect to HTTP
check_request_type();

// assign previous url link
assign_previous_url_val();

// reset passed get vars
$_GET['sort'] = $_POST['sort'];
$_GET['city'] = $_POST['city'];

function getdomain($url) {
  $url = str_replace("http://", "", str_replace("https://", "", $url));
  $url = substr($url, 0, strpos($url, "/"));
  return $url;
}

$set_domain = getdomain($api_load->website);

// cleans inserted api customer url
$domain_start = strpos($api_load->website,$set_domain);
$domain_length = strlen($set_domain);
$dom_length_tot = $domain_start+$domain_length;
$web_string_lngth = strlen($api_load->website);
$dom_length_end = $web_string_lngth-$dom_length_tot;
$dom_dir_strt = substr($api_load->website, $dom_length_tot, $dom_length_end);

// cleans up requested url
$_POST['REDIRECT_URL'] = str_replace($dom_dir_strt,'',$_POST['REDIRECT_URL']);

if(!empty($_POST['REDIRECT_URL'])) {

  switch($_POST['REDIRECT_URL']) {
	case 'advertiser':
		
	// assign page vals
	$_GET['loc_id'] = $_POST['loc_id'];
	$_GET['alt_loc_id'] = $_POST['alt_loc_id'];
		
	// assign page script
	$page_output->page_script = 'pages/location_info.php';
	
	// load page
	$page_output->proc_template();
		
	break;
	case 'view-all':
  
	  $_GET['view'] = 'all';

	  // assign page script
	  $page_output->page_script = 'pages/category_results.php';
	  
	  // load page
	  $page_output->proc_template();
	break;
	case 'deals-of-the-month':
  
	  $_GET['view'] = 'all';
	  $_GET['display'] = 'dom';

	  // assign page script
	  $page_output->page_script = 'pages/category_results.php';
	  
	  // load page
	  $page_output->proc_template();
	break;
	default:
	$_POST['REDIRECT_URL'] = str_replace("/","",$_POST['REDIRECT_URL']);
		
	$url_nms_tbl->url_name_search($_POST['REDIRECT_URL']);
	
	switch($url_nms_tbl->type) {
	case 'citiescate':
  
	  $ste_cty_cat_tbl->get_db_vars($url_nms_tbl->parent_id);
  
	  $_GET['cat'] = $ste_cty_cat_tbl->category;
	  $_GET['city'] = $ste_cty_cat_tbl->city;
	  
	  // assign page script
	  $page_output->page_script = 'pages/category_results.php';
	  
	  // load page
	  $page_output->proc_template();
	  
	break;
	// load search friendly state page
	case 'state':
  
	  $stes_tbl->get_db_vars($url_nms_tbl->parent_id);
  
	  $_GET['state'] = $stes_tbl->id;
	  
	  // assign page script
	  $page_output->page_script = 'pages/state_browse.php';
	  
	  // load page
	  $page_output->proc_template();
			  
	break;
	// load search friendly zip code page
	case 'city':
  
	  $cities_tbl->get_db_vars($url_nms_tbl->parent_id);
  
	  $_GET['city'] = $cities_tbl->id;
	  
	  // assign page script
	  $page_output->page_script = 'pages/results.php';
	  
	  // load page
	  $page_output->proc_template();
			  
	break;
	// load search friendly zip code page
	case 'zip':
  
	  $zip_cds_tbl->get_db_vars($url_nms_tbl->parent_id);
  
	  $_GET['setzip'] = $zip_cds_tbl->zip;
	  
	  // assign page script
	  $page_output->page_script = 'pages/results.php';
	  
	  // load page
	  $page_output->proc_template();
			  
	break;
	// load search friendly category page
	case 'category':
  
	  $cats_tbl->get_db_vars($url_nms_tbl->parent_id);
  
	  $_GET['cat'] = $cats_tbl->id;
	  
	  // assign page script
	  $page_output->page_script = 'pages/category_results.php';
	  
	  // load page
	  $page_output->proc_template();
	  
	break;
	// load search friendly content page
	case 'page':
  
	  $pgs_tbl->get_db_vars($url_nms_tbl->parent_id);
  
	  $_GET['pid'] = $pgs_tbl->id;
	  
	  // assign page script
	  $page_output->page_script = 'pages/pages.php';
	  
	  // load page
	  $page_output->proc_template();
	  
	break;
	default:
	  
	  $_GET['pid'] = 4;
	  
	  // assign page script
	  $page_output->page_script = 'pages/pages.php';
	  
	  // load page
	  $page_output->proc_template();
  
	break;
	}
	break;
  }
  
} else {
  
  $cities_tbl->city_state_search($api_load->city,$api_load->state);
  $_GET['city'] = $cities_tbl->id;
  
  // assign page script
  $page_output->page_script = 'pages/results.php';
  
  // load page
  $page_output->proc_template();
  
}

?>