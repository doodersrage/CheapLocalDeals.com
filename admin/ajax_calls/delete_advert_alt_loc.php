<?PHP
// load application top
require('../../includes/application_top.php');

if (!empty($_POST['id'])) {
		
	$stmt = $dbh->prepare("DELETE FROM advertiser_alt_locations WHERE id = '".$_POST['id']."';");
	$stmt->execute();
	
	echo '<script type="text/javascript">alert(\'Alternate Location Deleted.\')</script>';	

}
?>