<?PHP
// load application top
require('../../includes/application_top.php');

if(!empty($_POST['new_zip']) && !empty($_POST['cityid'])){
	$zip_cds_tbl->zip = $_POST['new_zip'];
	$zip_cds_tbl->city_id = $_POST['cityid'];
	$zip_cds_tbl->insert();
	$zip_cds_tbl->search($_POST['new_zip']);
	echo $zip_cds_tbl->id;
}
?>