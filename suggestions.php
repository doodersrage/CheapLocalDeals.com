<?PHP

require('includes/application_top.php');

  // Is there a posted query string?
  if(isset($_POST['queryString'])) {
	$queryString = $_POST['queryString'];
	// Is the string length greater than 0?
	if(strlen($queryString) >0) {
	// Run the query: We use LIKE '$queryString%'
	// The percentage sign is a wild-card, in my example of countries it works like this…
	// $queryString = 'Uni';
	// Returned data = 'United States, United Kindom';

	// added to check for number or string values
	if(is_numeric($queryString) === true) {
	  $sql_query = "SELECT 
	  					zip,
						city_id
					FROM 
						zip_codes
					WHERE 
						zip LIKE ? 
					LIMIT 50";
	  $values = array(
					  $queryString.'%'
					  );
	  
	  $stmt = $dbh->prepare($sql_query);					 
	  $query = $stmt->execute($values);
	  
	  if($query) {
		// While there are results loop through them - fetching an Object (i like PHP5 btw!).
		while ($result = $query->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		  // Format the results, im using <li> for the list, you can change it.          
		  // The onClick function fills the textbox with the result.
		  $cities_tbl->get_db_vars($result['city_id']);
		  echo '<li class="search_lst" onclick="fill(\''.$result['zip'].'\');">'.$result['zip'].' '.$cities_tbl->city.', '.$cities_tbl->state.'</li>';
		}
	  } else {
		echo 'ERROR: There was a problem with the query.';
	  }
	// if not numeric check by city and state
	} else {
	  $location_data = explode(',',$queryString);
	  if(count($location_data) > 1) {
		$sql_query = "SELECT 
						city,
						state 
					FROM 
						cities 
					WHERE 
						LOWER(city) LIKE LOWER(?) 
					AND 
						LOWER(state) LIKE LOWER(?)
					LIMIT 50";
		$values = array(
						trim($location_data[0]).'%',
						trim($location_data[1]).'%',
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$query = $stmt->execute($values);
	  } else {
		$sql_query = "SELECT 
						city,
						state 
					FROM 
						cities 
					WHERE 
						LOWER(city) LIKE LOWER(?)
					LIMIT 50";
		$values = array(
						trim($location_data[0]).'%',
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$query = $stmt->execute($values);
		
	  }
	  if($query) {
		// While there are results loop through them - fetching an Object (i like PHP5 btw!).
		echo '<ul>';
		while ($result = $query->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		  // Format the results, im using <li> for the list, you can change it.          
		  // The onClick function fills the textbox with the result.
		  echo '<li class="search_lst" onclick="fill(\''.$result['city'].', '.$result['state'].'\');">'.$result['city'].', '.$result['state'].'</li>';
		}
		echo '</ul>';
	  } else {
		echo 'ERROR: There was a problem with the query.';
	  }		
	}
  } else {
	// Dont do anything.
  } // There is a queryString.
} else {
  echo 'There should be no direct access to this script!';
}

?>