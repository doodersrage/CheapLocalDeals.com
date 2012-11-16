<?PHP
// load application top
require('../../includes/application_top.php');

// pull city zipcode list
$adv_info_tbl->get_db_vars($_POST['advert']);
$cert_id = $_POST['cert'];
  
  // sets item requirements
  $requirement_type = $adv_info_tbl->certificate_requirements[$cert_id]['type'];
  $requirement_value = $adv_info_tbl->certificate_requirements[$cert_id]['value'];

  // set cert req string
  $requirement_value = set_cert_agreement_str($requirement_type,$requirement_value);
  
echo $requirement_value;



?>
