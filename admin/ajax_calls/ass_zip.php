<?PHP
// load application top
require('../../includes/application_top.php');

if(!empty($_POST['new_zip'])){
	$zip_cds_tbl->get_db_vars($_POST['new_zip']);
	$zip_cds_tbl->city_id = $_POST['cityid'];
	$zip_cds_tbl->update();
}
?>