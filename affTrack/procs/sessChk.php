<?PHP
// check login info
if($_POST['userLoginSub']==1){
  $sql_query = "SELECT
				  id
			   FROM
				  api_access
			   WHERE
				  apikey = ? AND
				  password = ?
				LIMIT 1;";
  
  $values = array(
				  $_POST['api_code'],
				  encrypt_password($_POST['password'])
				  );
  
  $stmt = $dbh->prepare($sql_query);					 
  $result = $stmt->execute($values);
  
  $row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
  
  if ($row['id'] > 0) {
	$_SESSION['logged_in'] = 1;
	$_SESSION['api_id'] = $row['id'];
  } else {
	$error = "The login info provided appears to be invalid. Please check your username or password and try again.";
  }
}
?>