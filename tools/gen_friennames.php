<?PHP

// generates search friendly name for zip codes that do not have them assigned
// load application top
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

$sql_query = "SELECT
			id, zip, state, city
		 FROM
			zip_codes
		 WHERE
		 url_name is null OR url_name = ''
		 ;";

$rows = $dbh->queryAll($sql_query);

foreach($rows as $cor_row) {
			
		$sql_query = "INSERT INTO
					url_names
				 (
					type,
					parent_id,
					url_name
				 )
				 VALUES
				 ('zip',?,?);
				 ";
				 
		$cur_name = $cor_row['state'].'-'.$cor_row['city'].'-'.$cor_row['zip'].'-deals';
				 
		$update_vals = array($cor_row['id'],
							strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $cur_name))
							);
							
		$stmt = $dbh->prepare($sql_query);					 
		$stmt->execute($update_vals);
			
	$sql_querya = "SELECT
				id
			 FROM
				url_names
			 ORDER BY id DESC
			 LIMIT 1
			 ;";
	
	$rowsa = $dbh->queryRow($sql_querya);
	
	$sql_query = "UPDATE
		   		zip_codes
		   SET 
				url_name = ?
		   WHERE 
				id = ?;";
				 
	$update_vals = array($rowsa['id'],
						$cor_row['id']
						);
			
	$stmt = $dbh->prepare($sql_query);					 
	$stmt->execute($update_vals);

}


?>