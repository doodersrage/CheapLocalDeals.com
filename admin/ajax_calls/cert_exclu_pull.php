<?PHP
// load application top
require('../../includes/application_top.php');

// pull city zipcode list
$adv_info_tbl->get_db_vars($_POST['advert']);
$cert_id = $_POST['cert'];
  
  // sets item requirements
  $requirement_value = $adv_info_tbl->certificate_requirements[$cert_id]['excludes'];
  
echo $requirement_value;

?>
