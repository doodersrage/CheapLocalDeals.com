<?PHP

//$_POST = trim($_POST);

// load application top
require('../includes/application_top.php');

// include csv classes
require_once LIBS_DIR.'csv-utils/Csv/Reader.php';
require_once LIBS_DIR.'csv-utils/Csv/Writer.php';
require_once LIBS_DIR.'csv-utils/Csv/Sniffer.php';
require_once LIBS_DIR.'csv-utils/Csv/Dialect.php';

// include link name check function
require(SITE_ADMIN_FUNCTIONS_DIR.'link_check.php');

if (!class_exists('admin_users_table')) {
  // include admin_users_table class
  require(CLASSES_DIR.'tables/admin_users.php');
  $admin_users_table = new admin_users_table;
}

// check admin login status
if($admin_users_table->user_login_session_check() == 0) {
	
  // added to check for logged in session cookie settings
  if($_COOKIE['keep_logged_in'] == 1) {
	$_POST['username'] = $_COOKIE['username'];
	$_POST['password'] = $_COOKIE['password'];
  }
	
  // start output buffer
  ob_start();
  
  require(SITE_ADMIN_FUNCTIONS_DIR.'login_form.php');
  $page_content = login_form();

  // write output to constant
  define('ADMIN_PAGE_CONTENT',$page_content);

  // write left nav to constant
  define('LEFT_NAV','');
  
  // assign header constant
  $prnt_header = new prnt_header();
  $prnt_header->page_header_title = 'CheapLocalDeals.com - Admin Login';
  $prnt_header->page_meta_description = '';
  $prnt_header->page_meta_keywords = '';
  $prnt_header->admin = 1;
  
} else {
	  
  // set post to session variables
//  trim($_POST);
  if(isset($_POST['state_fltr'])) $_SESSION['state_fltr'] = $_POST['state_fltr'];
	
  // set previous page cookie in case user is logged out
  $expire=time()+60*60*24*30;
  setcookie("previous_page", curPageURL(), $expire);
  
  // start output buffer
  ob_start();
  
  // load page layout functions
  require(SITE_ADMIN_FUNCTIONS_DIR.'layout.php');
  
  if(isset($_GET['sect'])) {
	// load page based on set variables
	switch ($_GET['sect']) {
	case 'email':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['Email'] == 1) {
		require('sections/email.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
		  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	case 'admin_users':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['Admin Users'] == 1) {
		require('sections/admin_users.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
	  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	case 'orders':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['Orders'] == 1) {
		require('sections/orders.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
		  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// site settings
	case 'settings':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['Settings'] == 1) {
	
		// load settings page class
		require(SITE_ADMIN_CLASSES_DIR.'settings.php');
		$settings_page = new settings;
		
		// write page header
		$page_content = page_header('Settings');
		
		// update settings val
		if ($_POST['setting_submit'] == 1) {
		  $settings_page->update_settings();
		}
		
		// load selected settings if settings id is set
		if (isset($_GET['id'])) {
		  $page_content .= $settings_page->load_selected_settings($_GET['id']);
		}
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
		  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// categories admin
	case 'categories':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['Categories'] == 1) {
		require('sections/categories.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
		  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// citiescategories admin
	case 'citiescategories':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['CitiesCategories'] == 1) {
		require('sections/citiescategories.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
		  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// zip codes admin
	case 'zipcodes':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['Zip Codes'] == 1) {
		require('sections/zipcodes.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
		  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// states admin
	case 'states':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['States'] == 1) {
		require('sections/states.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
	  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// states admin
	case 'cities':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['Cities'] == 1) {
		require('sections/cities.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
	  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// retail customer admin
	case 'retcustomer':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['Advertisers'] == 1) {
		require('sections/retcustomer.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
	  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// regular customer admin
	case 'regcustomer':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['Customers'] == 1) {
		require('sections/regcustomers.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
	  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	case 'pages':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['Pages'] == 1) {
		require('sections/pages.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
	  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	case 'sessions':
	
	  // load sessions pages
	  require(SITE_ADMIN_CLASSES_DIR.'listings/sessions.php');
	  $sessions_info_lst = new sessions_info_lst;
  
	  // write page header
	  $page_content = page_header('Sessions');
	  
	  // delete selected pages
	  if ($_POST['delete_selected'] == 1) {
		$sessions_info_lst->delete();
	  }
  
	  // select page output function
	  switch ($_GET['mode']) {
	  // view sessions list
	  case 'view':
		$page_content .= $sessions_info_lst->listing();
	  break;
	  }
	  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	case 'createcertificate':
	  
	  // added to allow user lockout
	  if ($_SESSION['allowed_access']['CreateCertificate'] == 1) {
		require('sections/createcertificate.php');
	  } else {
		$page_content = ADMIN_PERMISSION_DENIED;
	  }
	  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// retail customer admin
	case 'retcustomerbackup':
  
	  require('sections/retcustomerbackup.php');
  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// 404 admin
	case '404':
  
	  // load zipcodes page
	  require(SITE_ADMIN_CLASSES_DIR.'listings/404s.php');
	  $error_pages_rs_page = new error_pages_rs;
  
	  // write page header
	  $page_content = page_header('404 Page Hits');
	  
	  // select page output function
	  switch($_GET['mode']) {
	  // view zip code listing
	  case 'view';
		$page_content .= $error_pages_rs_page->listing();
	  break;
	  }
		  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// page hits admin
	case 'page_hits':
  
	  // load zipcodes page
	  require(SITE_ADMIN_CLASSES_DIR.'listings/page_hits.php');
	  $page_hits_lst = new page_hits_lst;
  
	  // write page header
	  $page_content = page_header('Page Hits');
	  
	  // select page output function
	  switch($_GET['mode']) {
	  // view zip code listing
	  case 'view';
		$page_content .= $page_hits_lst->listing();
	  break;
	  }
		  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	// page hits admin
	case 'apiaccess':
  
	  require('sections/api_access.php');
  
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT',$page_content);
	  
	break;
	default:
	  // write output to constant
	  define('ADMIN_PAGE_CONTENT','');
	break;
	}
  } else {
	// write output to constant
	define('ADMIN_PAGE_CONTENT','<table border="0" align="center" cellpadding="5" cellspacing="0" class="def_menu">
<tr>
  <th colspan="4" align="center"><strong>Common Functions</strong></th>
</tr>
<tr>
  <td align="center"><a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&amp;mode=pending_approval">Advertisers Pending Approval</a></td>
  <td align="center"><a href="'.SITE_ADMIN_SSL_URL.'?sect=regcustomer&amp;mode=view">View Existing Customers</a></td>
  <td align="center"><a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&amp;mode=newcustomers">New Advertisers</a></td>
  <td align="center"><a href="'.SITE_ADMIN_SSL_URL.'?sect=orders&amp;mode=recent_orders">Recent Orders</a></td>
</tr>
<tr>
  <td align="center"><a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&amp;mode=view">View Existing Advertisers</a></td>
  <td align="center"><a href="'.SITE_ADMIN_SSL_URL.'?sect=pages&amp;mode=view">View Existing Pages</a></td>
  <td align="center"><a href="'.SITE_ADMIN_SSL_URL.'?sect=regcustomer&amp;mode=newcustomers">New Customers</a></td>
  <td align="center"><a href="'.SITE_ADMIN_SSL_URL.'?sect=orders&amp;mode=all_orders">All Orders</a></td>
</tr>
<tr>
  <td align="center"><a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomer&amp;mode=reviews">Advertiser Reviews</a></td>
  <td align="center"><a href="'.SITE_ADMIN_SSL_URL.'?sect=sessions&amp;mode=view">Active Sessions</a></td>
  <td align="center"><a href="'.SITE_ADMIN_SSL_URL.'?sect=orders&amp;mode=processed_advertiser_mems">Processed Advertiser Memberships</a></td>
  <td align="center">&nbsp;</td>
</tr>
</table>');
  }
  
  // load page left nav
  require(SITE_ADMIN_FUNCTIONS_DIR.'left_nav.php');
  // write left nav to constant
  define('LEFT_NAV',prnt_left_nav());
  
  // assign header constant
  $prnt_header = new prnt_header();
  $prnt_header->page_header_title = 'CheapLocalDeals.com - Admin';
  $prnt_header->page_meta_description = '';
  $prnt_header->page_meta_keywords = '';
  $prnt_header->admin = 1;
  
}
define('PAGE_HEADER',$prnt_header->print_page_header());

define('PRINT_PAGE',1);

// clear output buffer
ob_end_clean();

// start output buffer
ob_start();

// load template
require(TEMPLATE_ADMIN_DIR.'index.php');

$html = ob_get_contents();

ob_end_clean();

print_page($html);
?>