<?PHP
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
// load application header
require('includes/app_top.php');

// set radius data
if(empty($_SESSION['set_radius'])) {
  $_SESSION['set_radius'] = DEF_MIN_RADIUS;
}

if(!strstr($_SERVER["SERVER_NAME"],'www.')) header("Location: ".MOB_URL);

// get current page request value
$curpage = curPageURL();

// check and load requesting page
if(!empty($_GET['action'])){
	// display selected site sections
	switch($_GET['action']){
		case 'checkSuccess':
		  require(MOB_PAGE.'checkSuccess.php');
		break;
		case 'ordPrevPay':
		  require(MOB_PAGE.'ordPrevPay.php');
		break;
		case 'adview':
		  require(MOB_PAGE.'adView.php');
		break;
		case 'logOff':
		  require(MOB_PAGE.'logoff.php');
		break;
		case 'userLogin':
		  require(MOB_PAGE.'userLogin.php');
		break;
		case 'createAcc':
		  require(MOB_PAGE.'createAcc.php');
		break;
		case 'forgPass':
		  require(MOB_PAGE.'forgPass.php');
		break;
		case 'manageAcc':
		  require(MOB_PAGE.'custAdmin/index.php');
		break;
		case 'checkOut':
		  require(MOB_PAGE.'checkOut.php');
		break;
		case 'catList':
		  require(MOB_PAGE.'catList.php');
		break;
		case 'states':
		  require(MOB_PAGE.'states.php');
		break;
		case 'sitemap':
		  require(MOB_PAGE.'sitemap.php');
		break;
		case 'page':
		  require(MOB_PAGE.'page.php');
		break;
		case 'contactUs':
		  require(MOB_PAGE.'contact.php');
		break;
		default:
		  require(MOB_PAGE.'404.php');
		break;
	}
} else {
	// page uri check
	if($_SERVER["REQUEST_URI"] == '/mobile/'){
	  require(MOB_PAGE.'landing.php');
	} else {
	  header("HTTP/1.0 404 Not Found");
	  require(MOB_PAGE.'404.php');
	}
}

$mobLayout->page_header_title = $selHedMetTitle;
$mobLayout->page_meta_description = $selHedMetDesc;
$mobLayout->page_meta_keywords = $selHedMetDesc;
$mobLayout->enable_tabs_lib = $selTabs;
$mobLayout->template = $selTemp;
echo $mobLayout->renderPage();

?>