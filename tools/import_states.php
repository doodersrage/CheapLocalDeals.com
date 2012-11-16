<?PHP

// load application top
require('../includes/application_top.php');

ob_start();

echo 'This tool is used for generating search friendly links for city category pages.<br>';
echo '<form method="post">';
echo '<table><tr><td><strong>Select state:</strong></td><td>';

$sql_query = "SELECT
				id,
				state
			 FROM
				states
			 ;";
$rows = $dbh->queryAll($sql_query);
echo '<select name="state">';
echo '<option value="All">All</option>';
foreach($rows as $cur_state) {
echo '<option value="'.$cur_state['id'].'" '.($_POST['state'] == $cur_state['id'] ? ' selected="selected" ' : '').'>'.$cur_state['state'].'</option>';
}
echo '</select></td></tr><tr><td colspan="2" align="center">';
echo '<input name="submit" value="Submit" type="submit"></td></tr></table>';
echo '</form>';
ob_flush();

if (!empty($_POST['state'])) {

	if($_POST['state'] == 'All') {
		$sql_query = "SELECT
						id,
						state
					 FROM
						states
					 ;";
		$rows = $dbh->queryAll($sql_query);
		echo 'Progress:<br>';
		ob_flush();
		foreach($rows as $cur_state) {
			echo '<strong>'.$cur_state['state'].'</strong><br/>';
			update_state($cur_state['id']);
			ob_flush();
		}
	} else {
		update_state($_POST['state']);
	}
	echo 'finished';
	ob_flush();
}

// updates a states url names
function update_state($state) {
	global $dbh, $stes_tbl, $cities_tbl, $cats_tbl;
		
	$pr_limit = 5000;
	$pr_start = 0;
	
	// reset script timeout
	set_time_limit(0);

	// get rows count
	$sql_query = "SELECT
						count(*) as rcount
					 FROM
						state_city_category scc
					 LEFT JOIN
					 	url_names un ON scc.id = un.parent_id
					 WHERE
						scc.state = '".$state."'
					 AND
					 	un.id IS NULL
				 ;";
				 
	$rows = $dbh->queryRow($sql_query);
	$row_cnt = $rows['rcount'];

	// break run into pages
	for($i = $pr_start;$i < $row_cnt; $i += $pr_limit) {
	
		$sql_query = "SELECT
						scc.id,
						scc.state,
						scc.city,
						scc.category
					 FROM
						state_city_category scc
					 LEFT JOIN
					 	url_names un ON scc.id = un.parent_id
					 WHERE
						scc.state = '".$state."'
					 AND
					 	un.id IS NULL
					 LIMIT ".$i.",".$pr_limit."
					 ;";
					 
		echo 'Set: '.$i.'<br>';
		
		$rows = $dbh->queryAll($sql_query);
		foreach($rows as $cur_city) {
		
		// reset script timeout
		set_time_limit(0);
		
				// pull related cit cat information
				$stes_tbl->get_db_vars($cur_city['state']);
				$cities_tbl->get_db_vars($cur_city['city']);
				$cats_tbl->get_db_vars($cur_city['category']);
				
				// check for existing category
				if ($cats_tbl->id > 0) {
					
					// build new url string
					$url_string = $cities_tbl->city . '-' . $stes_tbl->acn;
					
					$categories_list = array();
					$categories_list[] = $cats_tbl->category_name;
					if ($cats_tbl->parent_category_id > 0) {
						$cats_tbl->get_db_vars($cats_tbl->parent_category_id);
						$categories_list[] = $cats_tbl->category_name;
					}
					
		//			// resort categories array
		//			krsort($categories_list);
					
					// set categories list
					$categories_list = implode('-',$categories_list);
					
					// finish new url string
					$url_string .= '-'.$categories_list;
					
					// cleanup url string
					$url_string = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $url_string));
		
					$url_array = array();
					$url_array_res = array();
					$url_array = explode('-',$url_string);
					foreach($url_array as $url_part) {
						if (!empty($url_part)) {
							$url_array_res[] = $url_part;
						}
					}
					
					$url_string = implode('-',$url_array_res);
					
					
					$sql_query = "INSERT INTO
									url_names
								 (
									type,
									parent_id,
									url_name
								 )
								 VALUES
								 (?,?,?);
								 ";
							 
					$update_vals = array('citiescate',
										$cur_city['id'],
										$url_string
										);
				
					$stmt = $dbh->prepare($sql_query);					 
					$stmt->execute($update_vals);
			
					$sql_query = "SELECT
								id
							 FROM
								url_names
							 ORDER BY
								id DESC
							 LIMIT 1
							 ;";
					$rows = $dbh->queryRow($sql_query);
					
					$sql_query = "UPDATE
									state_city_category
								 SET
									url_name = ?
								 WHERE
									id = ?
								 ;";
							 
					$update_vals = array($rows['id'],
										$cur_city['id']
										);
										
					$stmt = $dbh->prepare($sql_query);					 
					$stmt->execute($update_vals);
				} else {
					// if category does not exist remove city state category assignment
					$sql_query = "DELETE FROM
									state_city_category
								 WHERE
									id = ?
								 ;";
							 
					$update_vals = array(
										$cur_city['id']
										);
										
					$stmt = $dbh->prepare($sql_query);					 
					$stmt->execute($update_vals);
				}
		}
	}
}

ob_end_clean();
?>