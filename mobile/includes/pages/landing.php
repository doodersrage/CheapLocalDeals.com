<?PHP
// this script writes the content for the sites landing page and handles search form submissions
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
				  header("Location: ".MOB_URL."cats/".$cities_tbl->id." ");
				} else {
				  $error_message = $err_msg;
				}
				
			} else {
				$error_message = $err_msg;
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
			
				header("Location: ".MOB_URL."cats/".$cities_tbl->id." ");
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
					
						$page_link = '<a class="found_cty_lnk" href="'.MOB_URL.'">'.$cities_tbl->city.', '.$cities_tbl->state.'</a><br/>';
						$city_list .= $page_link;
					}
					$error_message = '<strong>We found these cities.</strong><br/>'.$city_list;
				} else {
					$error_message = 'We are sorry but the city entered was not found.';
				}
			}
				
		}		
	}

$selTemp = 'land.php';
$selHedMetTitle = 'Cheap Local Deals Mobile | Restaurant Deals and More Savings on the Go!';
$selHedMetDesc = 'Never miss a great deal when your on the run with Cheap Local Deals Mobile! Save with restaurant deals, retail deals, personal care deals today!';
$selHedMetKW = '';
?>