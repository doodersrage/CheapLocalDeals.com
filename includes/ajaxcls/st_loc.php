<?PHP
// load application header
require('../../includes/application_top.php');

$locDat['city'] = $_POST['city_sel'];
$locDat['state'] = $_POST['state_sel'];
$locDat = serialize($locDat);
setcookie("GEOCityState", $locDat, 0, "/");

?>