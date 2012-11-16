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

// page options set
$enable_tabs_lib = 1;

// make sure customer is logged in
if ($_SESSION['customer_logged_in'] == 1) {
	$page_output = '<table width="100%" border="0" align="center">';
	$page_output .= '<tr><td><div class="cart_header_border"><div class="cart_header">Manage Account</div></div></td></tr>';

	$page_output .= '<tr>
						<td class="frn_con">';
				

	// output page
	$page_output .= '<script type="text/javascript" src="'.CONNECTION_TYPE.'customer_admin/js/customer_admin.js"></script>
		<link rel="stylesheet" href="'.CONNECTION_TYPE.'includes/libs/jquery.tabs/jquery.tabs.css" type="text/css" media="print, projection, screen">

        <div id="container-2">
            <ul>
                <li><a href="'.SITE_SSL_URL.'customer_admin/#fragment-1" class="fragment-1"><span>Account Information</span></a></li>
                <li><a href="'.SITE_SSL_URL.'customer_admin/#fragment-2" class="fragment-2"><span>Certificates and Orders</span></a></li>
                <li><a href="'.SITE_SSL_URL.'customer_admin/#fragment-3" class="fragment-3"><span>Account Balance</span></a></li>
            </ul>
			<script type="text/javascript">
			jQuery(function() {
				load_frag1a();
			});
			</script>
            <div id="fragment-1" class="tab_box_brdr">
				
				<div id="container-3">
				<ul>
					<li><a href="'.SITE_SSL_URL.'customer_admin/#fragment-1a" class="fragment-1a"><span>Update Billing Address</span></a></li>
					<li><a href="'.SITE_SSL_URL.'customer_admin/#fragment-1b" class="fragment-1b"><span>Change Password</span></a></li>
				</ul>
				<script type="text/javascript">
				</script>
				<div id="fragment-1a">
				</div>
				<div id="fragment-1b">
				</div>
				</div>
				
			</div>
            <div id="fragment-2" class="tab_box_brdr">
				
				<div id="container-4">
				<ul>
					<li><a href="'.SITE_SSL_URL.'customer_admin/#fragment-2a" class="fragment-2a"><span>View Previous Orders</span></a></li>
                	<li><a href="'.SITE_SSL_URL.'customer_admin/#fragment-2b" class="fragment-2b"><span>Print Purchased Certificates</span></a></li>
				</ul>
				<script type="text/javascript">
				jQuery(function() {
					load_frag2a();
				});
				</script>
				<script type="text/javascript">
				</script>
				<div id="fragment-2a">
				</div>
				<div id="fragment-2b">
				</div>
				</div>
			
			</div>
            <div id="fragment-3" class="tab_box_brdr">
				
				<div id="container-5">
				<ul>
					<li><a href="'.SITE_SSL_URL.'customer_admin/#fragment-3a" class="fragment-3a"><span>Current Account Balance</span></a></li>
                	<li><a href="'.SITE_SSL_URL.'customer_admin/#fragment-3b" class="fragment-3b"><span>Add Funds</span></a></li>
				</ul>
				<script type="text/javascript">
				jQuery(function() {
					load_frag3a();
				});
				</script>
				<script type="text/javascript">
				</script>
				<div id="fragment-3a">
				</div>
				<div id="fragment-3b">
				</div>
				</div>

			</div>
        </div>';
							
$page_output .= '</td>
					  </tr>
					</table>
					';
// if customer is not logged in redirect to login page
} else {
	header("Location: ".SITE_SSL_URL."customer_admin/account_login.deal");			
}

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