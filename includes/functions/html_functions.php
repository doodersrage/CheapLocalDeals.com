<?PHP

// prints anchor link
function print_link($domain = '',$link = '',$link_text = '',$id = '',$class = '',$extra = '',$search_friendly = true) {
	
	$link_text = '<a href="'.$domain.$link.'"'.(!empty($id) ? ' id="'.$id.'"' : '').(!empty($class) ? ' class="'.$class.'"' : '').(!empty($extra) ? ' '.$extra : '').'>'.$link_text.'</a>';
	
return $link_text;
}

// clears output buffer
function clear_ob() {


}

$last_child_id = '';
// category drop down menu
function category_drop_down($selected_id = '') {
		global $dbh, $last_child_id;
	
	$parent_drop_down = '<select name="category" id="parent_cat_dd">'.LB;
	
	$sql_query = "SELECT
				id,
				category_name,
				parent_category_id
			 FROM
				categories
			 WHERE
				zip_id is NULL
			 AND
				parent_category_id = 0
			 ORDER BY sort_order ASC, category_name ASC
			 ;";
	$rows = db_memc_str($sql_query);
	
	foreach ($rows as $categories) {
	$ind = '--';
	
	// reset last child id
	$last_child_id = '';
	
	// draw child drop down
	$parent_drop_down_child = parent_dd_child_chk($categories['id'],$ind,$selected_id);
	
	// draw parent drop down
	$parent_drop_down_parent = '<option value="'.(!empty($last_child_id) ? $last_child_id : $categories['id']).'" '.($selected_id == $categories['id'] ? 'selected="selected" ' : '').'>'.$categories['category_name'].'</option>'.LB;
	
	$parent_drop_down .= $parent_drop_down_parent . $parent_drop_down_child;
	}
	
	$parent_drop_down .= '</select>'.LB;
	
return $parent_drop_down;
}

// check for child categories
function parent_dd_child_chk($cid,$ind,$selected_id = '') {
		global $dbh, $last_child_id;
		
	$sql_query = "SELECT
				id,
				category_name,
				parent_category_id
			 FROM
				categories
			 WHERE
				zip_id is NULL
			 AND
				parent_category_id = '".$cid."'
			 ORDER BY sort_order ASC, category_name ASC
			 ;";
	$rows = db_memc_str($sql_query);
	
	foreach ($rows as $categories) {
	$parent_drop_down .= '<option value="'.$categories['id'].'" '.($selected_id == $categories['id'] ? 'selected="selected" ' : '').'>'.$ind.' '.$categories['category_name'].'</option>'.LB;
	
	$parent_drop_down .= parent_dd_child_chk($categories['id'],$ind.'--');
	
	$last_child_id = $categories['id'];
	}
	
return $parent_drop_down;
}

// builds the certificate form used on the category listing and location info page
function draw_cert_form($value_drop_down,$advertiser_id,$default_req,$quantity_dd,$cert_req_id) {
  
  $location_info_form = '
  		<div class="certificate_submit_frm" align="center">
		   <div class="certificate_frm_top">&nbsp;</div>
			<form action="'.OVERRIDE_SITE_URL.'checkout/" method="post" name="coupon_to_cart" id="cert_frm_'.$advertiser_id.'">
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
			</div>
		  <div class="certificate_frm_bottom">&nbsp;</div> ';
	
return $location_info_form;
}

// writes certificate agreement string
function set_cert_agreement_str($requirement_type,$requirement_value) {

  switch($requirement_type) {
  case 1:
	  $requirement_value = 'Valid Toward ' . $requirement_value;
  break;
  case 2:
	  $requirement_value = 'Valid with minimum spend of $' . $requirement_value;
  break;
  case 3:
	  $requirement_value = 'Valid with '.$requirement_value;
  break;
  }

return $requirement_value;
}

// draws page warning box
function create_warning_box($message,$title = '') {
	$warning_box = '<script type="text/javascript">
	jQuery(function(){
			
		jQuery(\'.error\').css(\'margin-top\',150+\'px\');
		jQuery(\'.error\').css(\'margin-left\',325+\'px\');
			
		jQuery(".close_error").click(function() {
			jQuery(\'.error\').hide("medium");
			jQuery(\'#TB_overlay\').fadeOut("medium");
			jQuery(\'#TB_overlay\').remove();
			jQuery(\'.error\').remove();
		});
		
		jQuery("#TB_overlay").addClass("TB_overlayBG");
		jQuery(\'.error\').show("medium");
		jQuery(\'.error\').draggable();
	
	});

    var name = ".error";
    var menuYloc = null;
	
    jQuery(function(){
        menuYloc = parseInt(jQuery(name).css("top").substring(0,jQuery(name).css("top").indexOf("px")))
        jQuery(window).scroll(function () { 
			update_error_loc();
        });
		
		update_error_loc();
    
	}); 
	
	function update_error_loc() {
	  var offset = menuYloc+jQuery(document).scrollTop()+"px";
	  jQuery(name).animate({top:offset},{duration:500,queue:false});
	}

	</script><div id=\'TB_overlay\'></div><div class="error"><div class="error_header">'.(empty($title) ? 'Notice!' : $title).'</div><div class="error_text">'.$message.'</div><div class="close_error">CLOSE</div></div>';
	
return $warning_box;
}

// print select location form
function prnt_loc_form(){
  global $geo_data;
  
  $loc_frm = '<form name="state_set" target="" onSubmit="return false;">
  			<table>
  				<tr><td align="right">State:</td><td><select name="state_sel" id="state_sel" onchange="st_state()">'.gen_state_dd($geo_data->region).'</select></td></tr>
				<tr><td align="right">City:</td><td id="city_st_dd"><select name="city_sel" id="city_sel">'.gen_city_dd($geo_data->region,$geo_data->city).'</select></td></tr>
				<tr><td align="center" colspan="2"><input type="button" name="Save" value="Save" onclick="st_loc()"></td></tr>
			</table>
			</form>';
			
return $loc_frm;
}

// draws dynamic header including shopping cart links
function draw_dynamic_header_area() {
	global $shopping_cart_manage, $geo_data;

	$dyn_content = '<div id="signUpFrmAr">';
//	// if user lands on home page and the email cookie is not set within their browser prompt them for location and email input
//	if($_SERVER["REQUEST_URI"] == '/' && !isset($_COOKIE['email_address'])) {
//		$dyn_content .= create_warning_box(prnt_loc_form(),'Select Your Location');
//	}

	$dyn_content .= '</div>';
	$dyn_content .= '<script type="text/javascript" src="includes/js/login_pop.js"></script>';

	if (isset($_SESSION['customer_logged_in']) || isset($_SESSION['advertiser_logged_in'])) {
		$dyn_content .= '<a href="'.SITE_URL.'logoff.deal" class="header-link">Sign Out</a>|';
	} else {
		$dyn_content .= '<a href="javascript: void(0)" onclick="lnchMnFrm()" class="header-link">Account Login</a>';
	}
	if (isset($_SESSION['customer_logged_in'])) {
		$dyn_content .= '<a href="'.SITE_SSL_URL . 'customer_admin/" class="header-link">My Account</a>';
	}
	if (isset($_SESSION['advertiser_logged_in'])) {
		$dyn_content .= '<a href="'.SITE_SSL_URL . 'advertiser_admin/" class="header-link">My Account</a>';
	}

	if (isset($shopping_cart_manage->contents_count)) {
		if ($shopping_cart_manage->contents_count > 0) {
			$dyn_content .= '<br /><a href="'.SITE_SSL_URL.'checkout/"><img src="images/shop.gif" border="0" /></a><a href="'.SITE_SSL_URL.'checkout/" class="shop_cart_lnk"> Shopping Cart</a> Items: <font color="#57844D"><strong>'.$shopping_cart_manage->contents_count.'</strong></font> Total: <font color="#57844D"><strong>$'.$shopping_cart_manage->sub_total.'</strong></font> Savings: <font color="#FF0000"><strong>$'.$shopping_cart_manage->savings.'</strong></font>';
		}
	}

return $dyn_content;
}

?>