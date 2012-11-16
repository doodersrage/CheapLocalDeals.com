<?PHP
// load application top
require('../../includes/application_top.php');

// remove image from customer record
$sql_query = "UPDATE
				api_key
			 SET
				image = ''
			 WHERE
			 	id = ?
			 ;";
		 
$update_vals = array(
					$_POST['api_id']
					);
					
$stmt = $dbh->prepare($sql_query);
$stmt->execute($update_vals);

// remove image from server
unlink(API_IMAGES_DIRECTORY.$_POST['image']);
?>
