<?PHP
// load application header
require('../../includes/application_top.php');

echo '<select name="city_sel" id="city_sel">'.gen_city_dd($_POST['state_sel'],'').'</select>';

?>