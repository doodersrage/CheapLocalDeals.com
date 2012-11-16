<?PHP

global $sessions;

// logs user out and clears current session
//HTTP_Session2::destroy();
$sessions->clear();

// this script writes the content for the sites logoff page and handles search form submissions
$selTemp = 'logoff.php';
$selHedMetTitle = 'Cheap Local Deals Mobile | Your account has been logged out.';
$selHedMetDesc = 'Thanks for visiting Cheap Local Deals Mobile!';
$selHedMetKW = '';
?>