<?PHP

switch($_GET['section']){
	case 'users':
		switch($_GET['mode']){
			case 'view':
			 if(isset($_GET['sess_id'])){
			  require_once(SITE_DIR.'affTrack/sections/allusersessints.php');
			 }
			 if(isset($_GET['id'])){
			  require_once(SITE_DIR.'affTrack/sections/usersessions.php');
			 }
			break;
			default:
			  require_once(SITE_DIR.'affTrack/sections/alluserints.php');
			break;
		}
	break;
	case 'purchases':
		switch($_GET['mode']){
			case 'view':
			  require_once(SITE_DIR.'affTrack/sections/vieworder.php');
			break;
			default:
			  require_once(SITE_DIR.'affTrack/sections/allpurchases.php');
			break;
		}
	break;
	case 'usersignups':
	  require_once(SITE_DIR.'affTrack/sections/usersignups.php');
	break;
	case 'changepass':
	  require_once(SITE_DIR.'affTrack/sections/changepass.php');
	break;
	default:
	  require_once(SITE_DIR.'affTrack/sections/dashboard.php');
	break;
}

?>