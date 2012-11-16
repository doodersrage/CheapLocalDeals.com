<?PHP
// page displays advertiser information
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

$adv_rvws_tbl->customer_id = (int)$_SESSION['customer_id'];
$adv_rvws_tbl->advertiser_id = $_POST['advert_id'];
$adv_rvws_tbl->advertiser_alt_id = $_POST['advert_alt_id'];
$adv_rvws_tbl->rating = $_POST['rating'];
$adv_rvws_tbl->review = $_POST['review_txt'];
$adv_rvws_tbl->insert();
?>