<?PHP

// this document configures the sites domain constant

if ($api_load->website != '') {
  $set_domain = $api_load->website;
} else {
  $set_domain = 'http://www.cheaplocaldeals.com/';
}

define('SITE_URL',$set_domain);
define('OVERRIDE_SITE_URL','http://www.cheaplocaldeals.com/');
define('SITE_ADMIN_URL',SITE_URL.'admin/');
define('SITE_AFFILIATE_URL',SITE_URL.'affiliates/');
define('SITE_TEMPLATE_DIR',SITE_URL.'includes/template/');

// assign secure or non-secure page linking as requested by selected port
if($_SERVER['SERVER_PORT'] == 443) {
	define('CONNECTION_TYPE',SITE_SSL_URL);
} else {
	define('CONNECTION_TYPE',OVERRIDE_SITE_URL);
}

//// assign redirect post var if system var is set
//if(!empty($_SERVER['REDIRECT_URL'])) {
//  $_POST['REDIRECT_URL'] = $_SERVER['REDIRECT_URL'];
//}
?>