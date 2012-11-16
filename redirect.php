<?PHP
// load application header
require('includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if HTTPS page load request is made redirect to HTTP
check_request_type();

// assign previous url link
assign_previous_url_val();

// 
function del_urlName($selUrl=""){
  global $dbh;
	
  if(!empty($selUrl)){
	$stmt = $dbh->prepare("DELETE FROM url_names WHERE id = '".$selUrl."';");
	$stmt->execute();
  }
}

// document redirects search engine friendly pages
if (isset($_SERVER['REDIRECT_URL'])) {

  $_SERVER['REDIRECT_URL'] = str_replace("/","",$_SERVER['REDIRECT_URL']);

  // check for multiple similar url names if found check for existing linked elements
  $sql_query = "SELECT
				  count(id) as cnt, type
			   FROM
				  url_names
			   WHERE
				  url_name = ?
			   GROUP BY type
			   HAVING count(*) > 1
			   ;";
  $values = array(
				  trim($_SERVER['REDIRECT_URL'])
				  );
  
  $stmt = $dbh->prepare($sql_query);					 
  $result = $stmt->execute($values);
  
  if ($result->numRows() > 1) {
  
	  while($cur_row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)){
	  
		  $sql_query = "SELECT
						  id
					   FROM
						  url_names
					   WHERE
						  url_name = ?
					   AND
						  type = ?
					   ;";
		  $values = array(
						  trim($_SERVER['REDIRECT_URL']),
						  $cur_row['type']
						  );
		  
		  $stmt = $dbh->prepare($sql_query);					 
		  $urls_result = $stmt->execute($values);
		  
		  while($cur_chk_row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {

			  // pull selected url_name data 
			  $url_nms_tbl->get_db_vars($cur_chk_row['id']);
		  
			  // check for page type
			  switch($cur_row['type']) {
			  case 'citiescate':
				  $ste_cty_cat_tbl->get_db_vars($url_nms_tbl->parent_id);
				  if($ste_cty_cat_tbl->id == '') {
					  del_urlName($cur_chk_row['id']);
				  }
//					echo $ste_cty_cat_tbl->id.' ';
			  break;
			  case 'state':
				  $stes_tbl->get_db_vars($url_nms_tbl->parent_id);
				  if($stes_tbl->id == '') {
					  del_urlName($cur_chk_row['id']);
				  }
			  break;
			  case 'city':
				  $cities_tbl->get_db_vars($url_nms_tbl->parent_id);
				  if($cities_tbl->id == '') {
					  del_urlName($cur_chk_row['id']);
				  }
			  break;
			  case 'zip':
				  $zip_cds_tbl->get_db_vars($url_nms_tbl->parent_id);
				  if($zip_cds_tbl->id == '') {
					  del_urlName($cur_chk_row['id']);
				  }
			  break;			
			  case 'category':
				  $cats_tbl->get_db_vars($url_nms_tbl->parent_id);
				  if($cats_tbl->id == '') {
					  del_urlName($cur_chk_row['id']);
				  }
			  break;
			  case 'page':
				  $pgs_tbl->get_db_vars($url_nms_tbl->parent_id);
				  if($pgs_tbl->id == '') {
					  del_urlName($cur_chk_row['id']);
				  }
			  break;
			  }
		  }
		  
	  }
  
  }
  
  // used for mobile page request redirects
  // set site view status
  if(!empty($_SESSION['browse'])){
	if($_SESSION['browse'] != 'normal'){
	  // Include the Tera-WURFL file
	  require_once(LIBS_DIR.'/Tera-WURFL/TeraWurfl.php');
	  // instantiate the Tera-WURFL object
	  $wurflObj = new TeraWurfl();
	   
	  // Get the capabilities of the current client.
	  $matched = $wurflObj->getDeviceCapabilitiesFromAgent();
	   
	  // include url names db class
		  
	  // see if this client is on a wireless device (or if they can't be identified)
	  if($wurflObj->getDeviceCapability("is_wireless_device")){
		$url_nms_tbl->url_name_search($_SERVER['REDIRECT_URL']);
		
		switch($url_nms_tbl->type) {
		case 'citiescate':
	  
		  $ste_cty_cat_tbl->get_db_vars($url_nms_tbl->parent_id);
		  
		  // assign page script
		  header("location: ".MOB_URL."cats/".$ste_cty_cat_tbl->category."/".$ste_cty_cat_tbl->city);
		  
		break;
		// load search friendly state page
		case 'state':
	  
		  $stes_tbl->get_db_vars($url_nms_tbl->parent_id);
		  
		  // assign page script
		  header("location: ".MOB_URL."state/".$stes_tbl->id);
		  
				  
		break;
		// load search friendly zip code page
		case 'city':
	  
		  $cities_tbl->get_db_vars($url_nms_tbl->parent_id);
		  
		  // assign page script
		  header("location: ".MOB_URL."cats/".$cities_tbl->id);
				  
		break;
		// load search friendly zip code page
		case 'zip':
	  
		  $zip_cds_tbl->get_db_vars($url_nms_tbl->parent_id);
	  
		  $cities_tbl->get_db_vars($zip_cds_tbl->city_id);
		  if($cities_tbl->id > 0){
			// assign page script
			header("location: ".MOB_URL."cats/".$cities_tbl->id);
		  }
				  
		break;
		// load search friendly content page
		case 'page':
	  
		  $pgs_tbl->get_db_vars($url_nms_tbl->parent_id);
		  
		  // assign page script
		  header("location: ".MOB_URL."page/".$pgs_tbl->id);
		  
		break;
		default:
		  header('Location: '.SITE_URL.'mobile/');
		break;
		}
	  }
	}
  }
  
  // include url names db class
	  
  $url_nms_tbl->url_name_search($_SERVER['REDIRECT_URL']);
  
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

	$cities_tbl->get_db_vars($zip_cds_tbl->city_id);
	if($cities_tbl->id > 0){
	  if ($cities_tbl->url_name != '') {
		  $url_nms_tbl->get_db_vars($cities_tbl->url_name);
  
		  $page_link = $url_nms_tbl->url_name . "/";
	  
	  } else {
		  $page_link = "sections/results.deal?city=".$cities_tbl->id;
	  }
	  header("Location: ".SITE_URL.$page_link." ");
	}

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
	
	// capture link that lead to 404 page
	$pageURL = 'http';
	 if ($_SERVER['SERVER_PORT'] == 443) {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }; 

	// do not track 404s for these file types
	$dontarr = array(
					 '.jpg',
					 '.gif',
					 '.png',
					 '.css',
					 '.js',
					 'ico'
					 );
	
	// check for illegal 404 capture
	$errors = 0;
	foreach($dontarr as $fileext) {
	  if (stristr($pageURL,$fileext)) {
		$errors++;
	  }
	}

	if($errors == 0) {
	  $sql_query = "INSERT INTO
					  fof_errors
				   (
					  link
				   )
				   VALUES
				   (
					  ?
				   );";
			   
	  $update_vals = array(
						  $pageURL
						  );
  
	  $stmt = $dbh->prepare($sql_query);
	  $stmt->execute($update_vals);
	  // load 404 page
	  header("HTTP/1.0 404 Not Found");
	  $pgs_tbl->get_db_vars(4);
  
	  $_GET['pid'] = $pgs_tbl->id;
		
	  // assign page script
	  $page_output->page_script = 'pages/pages.php';
	  
	  // load page
	  $page_output->proc_template();
	}
  break;
  }

}
?>