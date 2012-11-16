<?PHP

global $geo_data, $zip_cds_tbl, $url_nms_tbl, $dbh, $cities_tbl, $pgs_tbl, $stes_tbl;

// global vars
$error_message = '';

// reset search session vars
unset($_SESSION['alpha_filter']);

//// assign zip val
//if(empty($_SESSION['cur_zip'])) $_SESSION['cur_zip'] = $geo_data->postcode;
//
//	$postcode = $_SESSION['cur_zip'];
	
	if (isset($_POST['search_box'])) {
		
		if(is_numeric($_POST['search_box']) === true) {
			$zip_cds_tbl->search((int)$_POST['search_box']);
			
			if ($zip_cds_tbl->id > 0) {
				$err_msg = 'We are sorry but the zipcode you were searching for was not found.';
				// check for set page url
				$cities_tbl->get_db_vars($zip_cds_tbl->city_id);
				if($cities_tbl->id > 0){
				  if ($cities_tbl->url_name != '') {
					  $url_nms_tbl->get_db_vars($cities_tbl->url_name);
			  
					  $page_link = $url_nms_tbl->url_name . "/";
				  
				  } else {
					  $page_link = "sections/results.deal?city=".$cities_tbl->id;
				  }
				  header("Location: ".SITE_URL.$page_link." ");
				} else {
				  $error_message = create_warning_box($err_msg);
				}
				
			} else {
				$error_message = create_warning_box($err_msg);
			}
		// if not numeric check by city and state
		} else {
			$location_data = explode(',',$_POST['search_box']);
			$sql_query = "SELECT
							id
						 FROM
							cities
						 WHERE
							LOWER(state) = LOWER('".(!empty($location_data[1]) ? trim($location_data[1]) : '')."')
						 AND
							LOWER(city) = LOWER('".(!empty($location_data[0]) ? trim($location_data[0]) : '')."')
						 LIMIT 1;";
			$rows = db_memc_str($sql_query);
			
			if ($rows['id'] > 0) {
		
				$cities_tbl->get_db_vars($rows['id']);
			
				if ($cities_tbl->url_name != '') {
					$url_nms_tbl->get_db_vars($cities_tbl->url_name);
			
					$page_link = $url_nms_tbl->url_name . "/";
				
				} else {
					$page_link = "sections/results.deal?city=".$cities_tbl->id;
				}
				header("Location: ".SITE_URL.$page_link." ");
			} else {
				$sql_query = "SELECT
								id
							 FROM
								cities
							 WHERE
								LOWER(city) = LOWER('".trim($location_data[0])."')
							 ;";
				$rows = db_memc_str($sql_query);
				
				if (count($rows) > 0) {
					$city_list = '';
					foreach($rows as $cur_row) {
						$cities_tbl->get_db_vars($cur_row['id']);
					
						if ($cities_tbl->url_name != '') {
							$url_nms_tbl->get_db_vars($cities_tbl->url_name);
					
							$page_link = $url_nms_tbl->url_name . "/";
						
						} else {
							$page_link = "sections/results.deal?city=".$cities_tbl->id;
						}
						$page_link = '<a class="found_cty_lnk" href="'.SITE_URL.$page_link.'">'.$cities_tbl->city.', '.$cities_tbl->state.'</a><br/>';
						$city_list .= $page_link;
					}
					$error_message = create_warning_box('<strong>We found these cities.</strong><br/>'.$city_list);
				} else {
					$error_message = create_warning_box('We are sorry but the city entered was not found.');
				}
			}
				
		}		
	}

// load page content
$pgs_tbl->get_db_vars(5);

$content_arr = array();
$content_arr['$error_message$'] = $error_message;
$content_arr['$header_content$'] = $pgs_tbl->header_content;
$content_arr['$footer_content$'] = $pgs_tbl->footer_content;
$this->template_constants = $content_arr;

// set page header -- only assign for static header data
$this->page_header_title = $pgs_tbl->header_title;
$this->page_meta_description = $pgs_tbl->meta_description;
$this->page_meta_keywords = $pgs_tbl->meta_keywords;
$this->footer_js = '<script type="text/javascript" src="includes/js/landing.js"></script>';
$this->template_file = 'landing.php';

?>