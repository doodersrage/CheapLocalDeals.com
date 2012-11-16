<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// load account login page class
if (!class_exists('account_login_pg')) {
	require(CLASSES_DIR.'pages/account_login.php');
	$account_login_pg = new account_login_pg;
}

if ($_SESSION['advertiser_logged_in'] == 1) {

	// load current advertiser data
	$adv_info_tbl->get_db_vars($_SESSION['advertiser_id']);

	// check for advertiser level assignment
	if(empty($adv_info_tbl->customer_level)) header("Location: ".SITE_SSL_URL."create_account_user_level_select.deal ");
	
	// load advertiser level data
	$adv_lvls_tbl->get_db_vars($adv_info_tbl->customer_level);
	
	$page_output = '';
	
	$session_timeout_secs = SESSION_TIMEOUT * 60;
	
	// output page
	$page_output .= '<script type="text/javascript">var num = \''.$session_timeout_secs.'\';</script><script type="text/javascript" src="'.CONNECTION_TYPE.'advertiser_admin/js/advert_admin.js"></script>
	<script type="text/javascript" src="'.CONNECTION_TYPE.'advertiser_admin/js/main.js"></script>
	<script type="text/javascript" src="'.CONNECTION_TYPE.'advertiser_admin/js/cert_settings.js"></script>
	<script type="text/javascript" src="'.CONNECTION_TYPE.'advertiser_admin/js/reviews.js"></script>';
	
	if($_SESSION['just_logged_in'] == 1 || !empty($_SESSION['new_advert_mess'])) {
		
		// checks for correct payment data entry 
		if ($adv_info_tbl->customer_level != 3) {
			if(empty($adv_info_tbl->payment_method)) {
				$error = 'WARNING! You have not selected a valid payment method. Your listing will not be displayed until your payment method has been configured.';
			}	
		}
		
//		$page_output .= create_warning_box('Welcome '.$adv_info_tbl->company_name.'.<br />');
		unset($_SESSION['just_logged_in']);
		unset($_SESSION['new_advert_mess']);
	}
	
	$page_output .= '<table width="100%" border="0" align="center">';
	$page_output .= '<tr><td style="text-align: center; display: block; margin: 10px;">'.($adv_info_tbl->approved == 1 ? '<b>Your account has been approved.</b>' : '<b>Your approval is pending - please allow up to 2 business days. Thank you.</b>' ).'</td></tr>';
	$page_output .= '<tr><td><div class="cart_header_border"><div class="cart_header">Manage '.$adv_info_tbl->company_name.' Advertisement Account</div></div></td></tr>';
	
	if (!empty($error)) {
	  $page_output .= '<tr><td align="center">'.$error.'</td></tr>';
	}
	$page_output .= '<tr>';
	$page_output .= '<td>';

    $page_output .= '<div id="container-2">
            <ul>
                <li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-1" class="fragment-1"><span>Account Settings</span></a></li>
                <li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-2" class="fragment-2"><span>Certificate Settings</span></a></li>
                <li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-3" class="fragment-3"><span>Reviews</span></a></li>
			</ul>
            <div id="fragment-1"><div id="container-3">
	<ul>
		<li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-1a" class="fragment-1a"><span>Image</span></a></li>
		<li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-1b" class="fragment-1b"><span>Address</span></a></li>
		<li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-1c" class="fragment-1c"><span>Business Info</span></a></li>';
	if($adv_info_tbl->allow_mult_loc == 1) {			
	   $page_output .= '<li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-1d" class="fragment-1d"><span>Alternate Locations</span></a></li>';
	}
$page_output .= '<li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-1e" class="fragment-1e"><span>Password</span></a></li>
			</ul>
            <div id="fragment-1a">';
$page_output .= '</div>
            <div id="fragment-1b">';
$page_output .= '</div>
            <div id="fragment-1c">';
$page_output .= '</div>
            <div id="fragment-1d">';
$page_output .= '</div>
            <div id="fragment-1e">';
$page_output .= '</div>

			</div>';
								
			if ($adv_info_tbl->account_enabled == 1) {
			  $page_output .= '<div id="disable_lnk"><a class="admin_menu" onclick="disable(0)" href="javascript:void(0);">Pause Ad (hide ad from listings)</a></div>';
			} else {
			  $page_output .= '<div id="disable_lnk"><a class="admin_menu" onclick="disable(1)" href="javascript:void(0);">Resume Ad (show ad in listings)</a></div>';
			}
			// display level select if set to free account or level not set
			 $days_to_renewal = ceil((strtotime($adv_info_tbl->customer_level_exp) - strtotime(date("Y-m-d"))) / 60 / 60 / 24);
			 
			if ($adv_lvls_tbl->level_renewal_cost == 0) {
				$page_output .= '<a class="admin_menu" href="'.SITE_SSL_URL.'advertiser_admin/create_account_user_level_select.deal">Upgrade To Premium Ad Level</a><br />';
			} elseif($days_to_renewal <= 7 && $adv_lvls_tbl->level_renewal_cost > 0 && $adv_info_tbl->customer_level_renew == 0) {
				$page_output .= '<a id="renew_link" class="admin_menu" href="javascript:void(0)" onclick="renew_advert(1)">Renew Current Advertising Level</a><br />';
			} elseif($days_to_renewal <= 7 && $adv_lvls_tbl->level_renewal_cost > 0 && $adv_info_tbl->customer_level_renew == 1) {
				$page_output .= '<a id="renew_link class="admin_menu" href="javascript:void(0)" onclick="renew_advert(0)">Cancel Renewal of Current Advertising Level</a><br />';
			}
			if ($adv_lvls_tbl->level_renewal_cost > 0) {
//				$page_output .= '<a class="admin_menu" href="'.SITE_SSL_URL.'advertiser_admin/advertiser_payment_options.deal">Edit Payment Options</a><span class="menu_help">Change your payment information</span><br />';
				$page_output .= '<a class="admin_menu" href="'.SITE_SSL_URL.'advertiser_admin/advertiser_payment_options.deal">Edit Payment Options</a><br />';
			}

		$page_output .= '<script type="text/javascript">
			jQuery(function() {
				load_frag1a();
			});
			</script>
		</div>
	
            <div id="fragment-2">
			  <div id="container-4">
			  <ul>';
//		$page_output .= '<li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-2a" class="fragment-2a"><span>Settings</span></a></li>';
		$page_output .= '<li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-2b" class="fragment-2b"><span>Unused</span></a></li>
				<li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-2c" class="fragment-2c"><span>Used</span></a></li>
				<li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-2d" class="fragment-2d"><span>Validate</span></a></li>
			</ul>
					  <div id="fragment-2a">';
		  $page_output .= '</div>
					  <div id="fragment-2b">';
		  $page_output .= '</div>
					  <div id="fragment-2c">';
		  $page_output .= '</div>
					  <div id="fragment-2d">';
		  $page_output .= '</div>
					  </div>
			</div>
			
            <div id="fragment-3">
			  <div id="container-5">
			  <ul>
				<li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-3a" class="fragment-3a"><span>Awaiting Approval</span></a></li>
				<li><a href="'.SITE_SSL_URL.'advertiser_admin/#fragment-3b" class="fragment-3b"><span>Approved</span></a></li>
			</ul>
					  <div id="fragment-3a">';
		  $page_output .= '</div>
					  <div id="fragment-3b">';
		  $page_output .= '</div>
					  </div>
			</div>
			
        </div>';
	
	$page_output .= '</td>';
	$page_output .= '</tr>';
	$page_output .= '</table>';

} else {
	header("Location: ".SITE_SSL_URL."advertiser_admin/advertiser_login.deal");			
}

// page options set
$enable_tabs_lib = 1;

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Manage Account';
$page_meta_description = 'Manage Account';
$page_meta_keywords = 'Manage Account';

// assign header constant
$prnt_header = new prnt_header();
$prnt_header->page_header_title = $page_header_title;
$prnt_header->page_meta_description = $page_meta_description;
$prnt_header->page_meta_keywords = $page_meta_keywords;
$prnt_header->enable_tabs_lib = $enable_tabs_lib;
define('PAGE_HEADER',$prnt_header->print_page_header());

// start output buffer
ob_start();

	// load template
	require(TEMPLATE_DIR.'blank-wobox.php');
	
	$html = ob_get_contents();
	
ob_end_clean();

print_page($html);
?>