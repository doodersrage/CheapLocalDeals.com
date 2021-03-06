<?PHP
// load application top
require('../includes/application_top.php');

// remove image from customer record
$sql_query = "UPDATE
				advertiser_info
			 SET
				image = ''
			 WHERE
			 	id = ?
			 ;";
		 
$update_vals = array(
					$_POST['advert_id']
					);
					
$stmt = $dbh->prepare($sql_query);
$stmt->execute($update_vals);

// remove image from server
unlink(CUSTOMER_IMAGES_DIRECTORY.$_POST['image']);

if(!empty($page_output)) {
	$page_output = create_warning_box('Image Deleted');
}

echo $page_output;
?>
