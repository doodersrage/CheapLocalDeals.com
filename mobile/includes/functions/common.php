<?PHP

function mob_cust_log_redir() {
	global $shopping_cart_manage;

  if ($shopping_cart_manage->contents_count > 0) {
	  header("Location: ".MOB_SSL_URL."checkout");							
  } else {
	  header("Location: ".MOB_SSL_URL."manageacc");			
  }
  
}

// prints dynamic header for shopping options
function dynaHead(){
	global $shopping_cart_manage, $geo_data;

	$dyn_content = '<div class="head">
        <div class="headLinks">';

	if (isset($_SESSION['customer_logged_in']) || isset($_SESSION['advertiser_logged_in'])) {
		$dyn_content .= '<a href="'.MOB_SSL_URL.'?action=logOff" class="header-link">Sign Out</a>|';
		$dyn_content .= '<a href="'.MOB_SSL_URL.'manageacc" class="header-link">My Account</a>';
	} else {
		$dyn_content .= '<a href="'.MOB_SSL_URL.'login" class="header-link">Account Login</a>';
	}

	if (isset($shopping_cart_manage->contents_count)) {
		if ($shopping_cart_manage->contents_count > 0) {
			$dyn_content .= '<div class="cartHead"><div class="cartCnt">'.$shopping_cart_manage->contents_count.'</div> <a href="'.MOB_SSL_URL.'checkout"><img src="includes/template/images/shop.png" border="0" /> </a></div>';
		}
	}
		
	$dyn_content .= '</div>
    </div>
';

return $dyn_content;
}

// draw page footer
function crtFoot(){
	$foot = '<div class="foot">
        <div class="smallPad textCent boxCent"><a href="'.MOB_URL.'">Home</a> - <a href="'.MOB_URL.'aboutus">About Us</a> - <a href="'. MOB_URL.'privacy">Privacy Policy</a> - <a href="'.MOB_URL.'contactus">Contact Us</a> - <a href="'. MOB_URL.'faq">FAQ</a> - <a href="'.MOB_URL.'sitemap">Sitemap</a></div>
        <div class="smallPad textCent boxCent"> CheapLocalDeals.com &copy; 2008-'. date('Y').'.&nbsp; All Rights Reserved. </div>
        <div class="smallPad textCent boxCent"> Download Our App for: <a href="'.MOB_URL.'apps/CLD-Android.apk">Android</a> | iPhone </div>
        <div class="smallPad textCent boxCent"> <a href="http://www.customermagnetism.com/" target="_blank">search engine optimization</a> services provided by www.customermagnetism.com </div>
        <div class="smallPad textCent boxCent"><a href="'.MOB_URL.'?browse=normal">View Normal Site</a></div>
    </div>';
return $foot;
}

// prints state link buttons
function prntStates(){
  global $stes_tbl,$url_nms_tbl;
	
  $state_listings = '';
  // pull states info from states table
  $sql_query = "SELECT
				  id
			   FROM
				  states
			   ;";
  $state_list = db_memc_str($sql_query);
			    
  // jump through each state
  foreach($state_list as $sel_state) {
  
  // pull current state info
  $stes_tbl->get_db_vars($sel_state['id']);
  $id = $stes_tbl->id;
  $acn = $stes_tbl->acn;
  $cur_state = $stes_tbl->state;
  				  
  $state_listings .= '<a class="linkBtn" href="'.MOB_URL.'state/'.$sel_state['id'].'">'.$cur_state.'</a>'.LB;
  
  }
return $state_listings;
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

	if($city_cnt > 0) {
	  $state_listings = '<div class="header_txt">'.$city_type[$type_sel].'</div>'.LB;				
	  
	  if($city_cnt > 1) {
		  foreach($cities_arr as $cur_city) {
			  // pull current city info
			  $cities_tbl->get_db_vars($cur_city['id']);			  
			  $state_listings .= '<a class="linkBtn" href="'.MOB_URL.'cats/'.$cities_tbl->id.'">'.$cities_tbl->city.'</a>'.LB;
		  }
	  } else {
		  // pull current city info
		  $cities_tbl->get_db_vars($cities_arr['id']);			  
		  $state_listings .= '<a class="linkBtn" href="'.MOB_URL.'cats/'.$cities_tbl->id.'">'.$cities_tbl->city.'</a>'.LB;
	  }
	}

return $state_listings;
}

// list categories
function list_categories($parentCat = '') {
	global $dbh, $cats_tbl;
	
	// set memcached id value
	$listKey = md5('CID'.$_GET['cid'].'CCID'.$parentCat);
	$list_cat = str_memc($listKey);
	if(empty($list_cat)){
	  $sql_query = "SELECT
					  id
				  FROM
					  categories
				  WHERE
					  parent_category_id = ".(!empty($parentCat) && $parentCat != 'dom' ? $parentCat : 0)." 
				  AND
					  disabled IS NULL
				  ORDER BY category_name ASC;";
	  
	  // store cache results in memcached
	  $rows = db_memc_str($sql_query);
			  
	  $list_cat = '';
	  
	  foreach ($rows as $categories) {
	  	  $cats_tbl->get_db_vars($categories['id']);
		  $list_cat .= '<a class="linkBtn" href="'.MOB_URL.'cats/'.$_GET['cid'].'/'.$categories['id'].'">'.htmlspecialchars($cats_tbl->category_name).'</a>';
	  
	  }
	  
	  // store final value in memcached
	  str_memc($listKey, $list_cat);
	}
	
return $list_cat;
}

// builds the certificate form used on the category listing and location info page
function draw_mob_cert_form($value_drop_down,$advertiser_id,$default_req,$quantity_dd,$cert_req_id) {
  
  $location_info_form = '
  		<div class="certificate_submit_frm" align="center">
		   <div class="certificate_frm_top">&nbsp;</div>
			<form action="'.MOB_SSL_URL.'checkout" method="post" name="coupon_to_cart" id="cert_frm_'.$advertiser_id.'">
			  <div class="cert_frm_top">
			  	<strong>Value:</strong>
				<div align="center">
				  '.$value_drop_down.'
				</div>
			  </div>
			  <div class="cert_frm_mid">
				<strong>Requirements:</strong>
				<div class="cert_requirements" id="'.$cert_req_id.'">
				  '.$default_req.'
				</div>
			  </div>
			  <div class="cert_frm_bot">
				<div class="cert_frm_bot_left">
				  <strong>Quantity:</strong>
					'.$quantity_dd.'
				  <input name="action" type="hidden" value="add" />
				</div>
				<div class="cert_frm_bot_right">
				  <input name="advertiser_id" type="hidden" value="'.$advertiser_id.'" />
				  <input src="'.OVERRIDE_SITE_URL.'images/addtocart.gif" class="add_cart_img" value="Add to Cart" name="B1" type="image"/>
				</div>
			  </div>
			  </form>
			<div class="certificate_frm_bottom">&nbsp;</div></div>
		   ';
	
return $location_info_form;
}

// count of items within category
function category_count($category_id) {
	global $dbh, $adv_info_tbl, $cities_tbl, $zip_cds_tbl;
		
	// set city values
	$cities_tbl->get_db_vars((int)$_GET['cid']);
	
	// pull city zipcode list
	$zip_cds_tbl->city_id = $cities_tbl->id;
	$zip_array = $zip_cds_tbl->get_list();
	$zip_array = $zip_cds_tbl->fetchZipsInRadiusByZip($zip_array[0], $_SESSION['set_radius'], 100);
	$zip_string = implode(', ',$zip_array);
	
	// reset category count
	$category_count = '';
	
	// reset selected cat array
	$selected_cats = array();

	$sql_query = "SELECT
					ai.id
				 FROM
					advertiser_info ai LEFT JOIN advertiser_alt_locations aal ON ai.id = aal.advertiser_id 
					RIGHT JOIN advertiser_categories ac ON ac.advertiser_id = ai.id
				 WHERE
					ac.category_id = '".$category_id."'
				AND ai.account_enabled = 1 
				AND ai.approved = 1 
				AND ai.update_approval = 1 
				AND (ai.zip IN (".$zip_string.") 
				OR aal.zip IN (".$zip_string."));";
//	echo $sql_query;
	// store cache results in memcached
	$rows = db_memc_str($sql_query);
	
	// added to check for payment data entry
	foreach($rows as $cur_row) {
		// added to check level selected and if payment info has been entered
		$adv_info_tbl->get_db_vars($cur_row['id']);
		if ($adv_info_tbl->customer_level != 3) {
		  if(!empty($adv_info_tbl->payment_method)) {
			$selected_cats[] = $cur_row['id'];
		  }
		} else {
			$selected_cats[] = $cur_row['id'];
		}
	}
	
	// get selected category count
	$category_count = count($selected_cats);

return $category_count;
}

?>