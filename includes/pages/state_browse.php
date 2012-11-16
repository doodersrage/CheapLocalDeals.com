<?PHP

global $pgs_tbl, $category_results_pg, $dbh, $stes_tbl, $url_nms_tbl, $cities_tbl, $city_type;

// initiate city advert count
$city_advert_cnt = 0;

// check for old link and redirect if found
if (isset($_GET['state']) && isset($_GET['city'])) {
	if (is_string($_GET['state']) && is_string($_GET['city'])) {
		// pull states info from states table
		$sql_query = "SELECT
						id
					 FROM
						cities
					 WHERE
						state = ?
					 AND
						city = ?
					 LIMIT 1;";
		
		$update_vals = array(
							$_GET['state'],
							$_GET['city']
							);

		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($update_vals);
		$states_op = $result->fetchRow();
		$cities_tbl->get_db_vars($states_op['id']);

		// clear result set
		$result->free();	

		// check for search friendly name if search friendly page has not been selected
		// if url name id is found 301 redirect to search friendly page
		if ($cities_tbl->url_name != '') {
			$url_nms_tbl->get_db_vars($cities_tbl->url_name);
							
			// check for correct url name assignment
			header("HTTP/1.1 301 Moved Permanently"); 	
			header("Location: ".SITE_URL.$url_nms_tbl->url_name."/");
		}
			
	}
} elseif(isset($_GET['state'])) {
	if (is_string($_GET['state'])) {
		// pull states info from states table
		$sql_query = "SELECT
						id
					 FROM
						states
					 WHERE
						acn = ?
					 LIMIT 1;";
		
		$update_vals = array(
							$_GET['state']
							);

		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($update_vals);
		$states_op = $result->fetchRow();
		$stes_tbl->get_db_vars($states_op['id']);
			
		// check for search friendly name if search friendly page has not been selected
		// if url name id is found 301 redirect to search friendly page
		if ($stes_tbl->url_name != '') {
			$url_nms_tbl->get_db_vars($stes_tbl->url_name);
							
			// check for correct url name assignment
			header("HTTP/1.1 301 Moved Permanently"); 	
			header("Location: ".SITE_URL.$url_nms_tbl->url_name."/");
		}
		
		$stes_tbl->get_db_vars($_GET['state']);
		// added to check for set query string pointing to new state ids
		if ($stes_tbl->url_name != '') {
			$url_nms_tbl->get_db_vars($stes_tbl->url_name);
			$new_uri = "/".$url_nms_tbl->url_name."/";
			$current_uri = $_SERVER["REQUEST_URI"];
			// check for correct url name assignment
			if ($new_uri != $current_uri && strstr($_SERVER["REQUEST_URI"],'state-browse/')){
				header("HTTP/1.1 301 Moved Permanently"); 	
				header("Location: ".SITE_URL.$url_nms_tbl->url_name."/");
			}
		}
	}
}

// prints city list
function prnt_city_lsts($type_sel, $acn, $cur_state) {
	global $dbh, $city_type, $url_nms_tbl, $cities_tbl;

	$state_listings = '';
	
	$sql_values = array();

	// query list of cities within selected state
	$sql_query = "SELECT
					id
				 FROM
					cities
				 WHERE
					state = ?
				 AND
				 	type = ?
				 ;";
	
	$sql_values[] = $acn;
	$sql_values[] = $type_sel;
	
	$cities_arr = db_memc_str($sql_query,$sql_values);
	$city_cnt = count($cities_arr);
		
	// get city list count
	// city cnt half
	$city_cnt_half = ceil($city_cnt/5)+1;
	// reset cur city val
	$cur_city_cnt = 0;

	if($city_cnt > 0) {
	  $state_listings = '<table class="city_listing" summary="Find local deals in the '.$city_type[$type_sel].'(s) of '.$cur_state.'! Save on restaurants, auto-repairs, contractors, and more!">
							  <caption>'.$city_type[$type_sel].($type_sel < 3 ? '\'s' : '').' of '.$cur_state.'</caption>
							  <tbody>
							  <tr>
								  <td valign="top">
									  <ul class="state_list">'.LB;				
	  
	  foreach($cities_arr as $cur_city) {
  		
		  // set city id
		  $cityID = (count($cities_arr) == 1 ? $cities_arr['id'] : $cur_city['id']);
		
		  // pull current city info
		  $cities_tbl->get_db_vars($cityID);
		  
		  if (empty($_GET['city']) || $cur_city['city'] == $_GET['city']) {
			  
		  $cur_city_cnt++;
			  
		  // check for current state position
		  if ($cur_city_cnt == $city_cnt_half) {
			  $state_listings .= '</ul></td><td valign="top" align="left"><ul class="state_list">';
			  $cur_city_cnt = 0;
		  }
		  
		  // check for search friendly url name entry
		  if($cities_tbl->url_name > 0) {
			  $url_nms_tbl->get_db_vars($cities_tbl->url_name);
			  $link_name = $url_nms_tbl->url_name.'/';
		  } else {
			  $link_name = 'sections/results.deal?city='.$cityID;
		  }
		  
		  $city_advert_cnt = '';
				  
		  $state_listings .= '<li><a '.(!empty($_GET['city']) ? 'class="state_link"' : '').' href="'.SITE_URL.$link_name.'">'.$cities_tbl->city.'</a>'.$city_advert_cnt.LB;
		  
//		  $city_advert_cnt += $rows['rcount'];
		  
		  
		  $state_listings .= '</li>'.LB;
			  }
	  }
	  $state_listings .= '</ul>
					  </td>
				  </tr>
				  </tbody>
			  </table>'.LB;
	}

return $state_listings;
}

// pull states info from states table
$sql_query = "SELECT
				id
			 FROM
				states
			 ;";
$states_op = db_memc_str($sql_query);

// set state array
$state_list = $states_op;

// listing output
	$state_listings = '<div class="banner_box"><a href="http://www.localmarketingexpo.com/" target="_blank"><img src="'.CONNECTION_TYPE.'images/LMX-banner.jpg" alt="Local Marketing Expo - LMX" height="90" width="728"/></a></div>
	<div class="banner_box"><img src="'.CONNECTION_TYPE.'images/75off.jpg" alt="save up to 75 percent off gift certificates" height="49" width="900"/></div>
<table class="tbl_page_content" cellspacing="0" cellpadding="0">
  <tr>
	<td colspan="2" class="search_area"><div class="search_form">
		<form name="zip_search" action="'.SITE_URL.'" method="post">
		  <table border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td><input id="search_box" name="search_box" type="text" />
				<center>
				  <!--<a href="'. SITE_URL.'state-browse/" class="browse_state_lnk">Browse Local Deals By State</a>-->
				</center>
				<div class="suggestionsBox" id="suggestions" style="display: none;">
				  <div class="suggestionList" id="autoSuggestionsList"></div>
				</div></td>
			  <td valign="top"><input name="image" type="image" id="search_button" src="'.  STD_TEMPLATE_DIR . 'images/search_button.png" /></td>
			</tr>
		  </table>
		</form>
	  </div>
	  <img src="'.CONNECTION_TYPE.STD_TEMPLATE_DIR.'images/header_search01.jpg" alt="header search image" name="bbb_logo" width="860" height="300" border="0" usemap="#bbb_logoMap" class="search_img" />
	  <map name="bbb_logoMap" id="bbb_logoMap">
		<area shape="rect" coords="144,227,354,273" alt="Get more cheap local deals with an account today!" href="https://www.cheaplocaldeals.com/customer_admin/create_account.deal" />
	  </map>
	  </td>
  </tr>
  </table>
  <script type="text/javascript" src="includes/js/landing.js"></script>
  ';
	$state_listings .= '<table border="0" align="center" class="advertiser_form">';
	$state_listings .= '<tr><th class="frn_header">Browse Listings By State</th></tr>';
	$state_listings .= '<tr><td align="left"><table width="100%"><tr><td valign="top" align="left">
					<ul class="state_list">'.LB;
					
	// get state list count
	$state_cnt = count($state_list);
	// state cnt half
	$state_cnt_half = ceil($state_cnt/4);

	// reset cur state count val
	$cur_state_cnt = 0;
	
	// jump through each state
	foreach($state_list as $sel_state) {
	
		// pull current state info
		$stes_tbl->get_db_vars($sel_state['id']);
		$id = $stes_tbl->id;
		$acn = $stes_tbl->acn;
		$cur_state = $stes_tbl->state;
		
		// check for search friendly url name entry
		if($stes_tbl->url_name > 0) {
			$url_nms_tbl->get_db_vars($stes_tbl->url_name);
			$link_name = $url_nms_tbl->url_name.'/';
		} else {
			$link_name = 'state-browse/?state='.$sel_state['id'];
		}
	
		// print city list if state is selected
		if (empty($_GET['state']) || $id == $_GET['state']) {
					
//		$sql_query = "SELECT
//						count(*) as rcount
//					 FROM
//						advertiser_info ai LEFT JOIN advertiser_alt_locations aal ON ai.id = aal.advertiser_id
//					 WHERE
//						ai.account_enabled = 1 
//						AND ai.approved = 1 
//						AND ai.update_approval = 1 
//						AND (ai.state = '".$acn."' OR aal.state = '".$acn."')
//					  LIMIT 1;";
//
//		$rows = $dbh->queryRow($sql_query);
//		$state_listings .= '<li><a '.(!empty($_GET['state']) ? 'class="state_link"' : '').' href="'.SITE_URL.$link_name.'">'.$cur_state.'</a> (<strong>'.$rows['rcount'].'</strong>)'.LB;
		$state_listings .= '<li><a '.(!empty($_GET['state']) ? 'class="state_link"' : '').' href="'.SITE_URL.$link_name.'">'.$cur_state.'</a> '.LB;
		
		if(!empty($_GET['state']) && $_GET['state'] == $id) {
			// print header data
			if($stes_tbl->page_title != '' || $stes_tbl->page_title != NULL) {
				$page_header_title = $stes_tbl->page_title;
			} else {
				$page_header_title = $cur_state.' Restaurant Coupons and Local Deals in '.$acn.' - CheapLocalDeals.com';
			}
			if($stes_tbl->meta_description != '' || $stes_tbl->meta_description != NULL) {
				$page_meta_description = $stes_tbl->meta_description;
			} else {
				$page_meta_description = 'Find local restaurants deals in your area by state. '.$cur_state;
			}
			if($stes_tbl->meta_keywords != '' || $stes_tbl->meta_keywords != NULL) {
				$page_meta_keywords = $stes_tbl->meta_keywords;
			} else {
				$page_meta_keywords = strtolower('local deals by state,select state to find local deals,local deals,state selected local deals '.$cur_state.','.$cur_state.' restaurants deals,'.$cur_state.' restaurants,'.$cur_state.' deals,'.$cur_state.' savings');
			}
			
			// assign header and footer content
			$page_header = $stes_tbl->page_header;
			$page_footer = $stes_tbl->page_footer;
			
		}
		
		// update cur state count val
		$cur_state_cnt++;
		
		// check for current state position
		if ($cur_state_cnt == $state_cnt_half) {
			$state_listings .= '</ul></td><td valign="top" align="left"><ul class="state_list">';
			$cur_state_cnt = 0;
		}
		
		// print city list if state is selected
		if (!empty($_GET['state']) && $id == $_GET['state']) {
			
			// insert new list processor here
			$city_type = array_reverse($city_type, true);
			foreach($city_type as $id => $value) {
				$state_listings .= prnt_city_lsts($id, $acn, $cur_state);
			}
			
		}
		
		$state_listings .= '</li>'.LB;
		}
	}
	
	$state_listings .= '</ul>
					</td>
					</tr>
					</table>
					</td>
					</tr>';
	$state_listings .= '</table>'.LB;

// build page
$page_output = (isset($page_header) ? '<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
		  <td id="header_content">'.$page_header.'</td>
		</tr>
	  </table>' : '');
$page_output .= '<div class="page_header_foot_content">'.(isset($page_footer) ? $page_footer : '').'</div>';
$page_output .= $state_listings;
$page_output .= '<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
		  <td id="footer_content"></td>
		</tr>
	  </table>
	  <script type="text/javascript" src="'. OVERRIDE_SITE_URL.'js_load.deal?js_doc='.urlencode('includes/js/results.js') . '"></script>';

// if no adverts are found set page to noindex
if ($city_advert_cnt == 0) $no_index = 1;

if (empty($page_header_title)) $page_header_title = 'Browse local restaurant local deals by state.';
if (empty($page_meta_description)) $page_meta_description = 'Save at your favorite local restaurants. Why pay full price when you can pay less?';
if (empty($page_meta_keywords)) $page_meta_keywords = 'local deals,state browse local deals,restaurant deals,save money';

$content_arr = array();
$content_arr['$page_output$'] = $page_output;
$this->template_constants = $content_arr;

// set page header -- only assign for static header data
$this->page_header_title = $page_header_title;
$this->page_meta_description = $page_meta_description;
$this->page_meta_keywords = $page_meta_keywords;
$this->template_file = 'state_browse.php';

?>