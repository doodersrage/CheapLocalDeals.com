<?PHP
// load application top
require('../../includes/application_top.php');

if(!empty($_POST['zip_id'])){
	$stmt = $dbh->prepare("DELETE FROM a WHERE id = '".(int)$_POST['zip_id']."';");
	$stmt->execute();
	$stmt = $dbh->prepare("DELETE FROM url_names WHERE type = 'zip' AND parent_id = '".(int)$_POST['zip_id']."';");
	$stmt->execute();
}
?>
