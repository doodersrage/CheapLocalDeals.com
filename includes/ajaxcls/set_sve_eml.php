<?PHP
// load application header
require('../../includes/application_top.php');

// set email address cookie
setcookie("email_address", $_POST['email_sub'], 0, "/");

// check email address against existing users in database, if no user is found add email to database
$_POST['email_address'] = $_POST['email_sub'];
if($customer_info_table->email_check() == 0){
	$customer_info_table->reset_vars();
	$customer_info_table->email_address = $_POST['email_sub'];
	$locDat = unserialize($_COOKIE["GEOCityState"]);
	$city = $locDat['city'];
	$state = $locDat['state'];
	$customer_info_table->city = $city;
	$customer_info_table->state = $state;
	$customer_info_table->longitude = $geo_data->longitude;
	$customer_info_table->latitude = $geo_data->latitude;
	$customer_info_table->insert();
}
?>