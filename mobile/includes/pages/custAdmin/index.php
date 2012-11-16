<?PHP

// check if the user is logged in and if not redirect them to the login page
if ($_SESSION['customer_logged_in'] != 1) header("Location: ".MOB_SSL_URL."?action=userLogin");

// select section to load
switch($_GET['section']){
	case 'credit':
		require(MOB_PAGE.'custAdmin/credSub.php');
	break;
	case 'certs':
		require(MOB_PAGE.'custAdmin/certSub.php');
	break;
	case 'orders':
		require(MOB_PAGE.'custAdmin/ordersSub.php');
	break;
	case 'password':
		require(MOB_PAGE.'custAdmin/passSub.php');
	break;
	case 'address':
		require(MOB_PAGE.'custAdmin/addressSub.php');
	break;
	default:
		require(MOB_PAGE.'custAdmin/addressSub.php');
	break;
}

// this script writes the content for the sites logoff page and handles search form submissions
$selTemp = 'manageAcc.php';
$selHedMetTitle = 'Cheap Local Deals Mobile | Account Management.';
$selHedMetDesc = 'Thanks for visiting Cheap Local Deals Mobile!';
$selHedMetKW = '';
$selTabs = 0;

?>