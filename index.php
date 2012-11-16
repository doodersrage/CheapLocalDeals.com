<?PHP
// load application header
require('includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// set site view status
if(!empty($_SESSION['browse'])){
  if($_SESSION['browse'] != 'normal'){
	// Include the Tera-WURFL file
	require_once(LIBS_DIR.'/Tera-WURFL/TeraWurfl.php');
	// instantiate the Tera-WURFL object
	$wurflObj = new TeraWurfl();
	 
	// Get the capabilities of the current client.
	$matched = $wurflObj->getDeviceCapabilitiesFromAgent();
	 
	// see if this client is on a wireless device (or if they can't be identified)
	if($wurflObj->getDeviceCapability("is_wireless_device")){
	   switch($_SERVER["REQUEST_URI"]){
		case '/':
		  header('Location: '.MOB_URL);
		break;
		case '/contact_us.deal':
		  header('Location: '.MOB_URL.'contactus');
		break;
		case '/sitemap.html':
		  header('Location: '.MOB_URL.'sitemap');
		break;
		case '/account_login_page.deal':
		  header('Location: '.MOB_URL.'login');
		break;
		default:
		  header('Location: '.MOB_URL);
		break;
	}
   }
  }
}

// if HTTPS page load request is made redirect to HTTP
if($_SERVER["REQUEST_URI"] != '/account_login_page.deal') check_request_type();

switch($_SERVER["REQUEST_URI"]){
  case '/':
	$page_output->page_script = 'pages/landing.php';
  break;
  case '/contact_us.deal':
	$page_output->page_script = 'pages/contact_us.php';
  break;
  case '/sitemap.html':
	$page_output->page_script = 'pages/sitemap.php';
  break;
  case '/account_login_page.deal':
	$page_output->page_script = 'pages/account_login_page.php';
  break;
  case '/logoff.deal':
	if(isset($_COOKIE['keep_logged_in'])) {
	  setcookie("keep_logged_in", "", time()-3600);
	  setcookie("username", "", time()-3600);
	  setcookie("password", "", time()-3600);
	}
	$page_output->page_script = 'pages/logoff.php';
  break;
}
// load page
$page_output->proc_template();

?>