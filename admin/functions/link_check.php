<?PHP

// checks and prevents duplicate links from being written to db
function existing_link_check($link_name = '', $id = '', $type = '') {
		global $dbh;
		
		if (!empty($link_name)) {
			// check for url usage within zip codes table
			$sql_query = "SELECT
							count(*) as rcount
						 FROM
							url_names
						 WHERE url_name = '".$link_name."' 
						 AND parent_id <> '".$id."'
						 AND type = '".$type."'
						 ;";
			
			$rowscount = $dbh->queryRow($sql_query);
	
			$row_count = $rowscount['rcount'];
			
			$link_chk = $row_count;
			
		}

return $link_chk;
}

?>