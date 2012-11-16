<?PHP
function get_page_lnks(){
	global $dbh;
	
	$sql_query = "SELECT
					id
				 FROM
					api_ref_track
				 WHERE
					api_id = ?
				 AND
				 	sess_id = ?
				  ORDER BY date_time DESC
				 ;";
	
	$update_vals = array(
						$_SESSION['api_id'],
						$_GET['sess_id']
						);
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute($update_vals);
			
	$count = $result->numRows();;

	// clear result set
	$result->free();
	
	// reset DB conn
	db_check_conn();
	
	$page_lnks = array();
	$cur_page = 0;
	for($i = 0;$i < $count;$i += 20){
		$cur_page++;
		$page_lnks[] = '<a href="?section=users&mode=view&sess_id='.$_GET['sess_id'].'&page='.$cur_page.'">'.$cur_page.'</a>';
	}
	
	$page_lnks = implode(', ',$page_lnks);
	
return $page_lnks;
}
?>
  
  <h3>All User Views By Session ID</h3>
  <table cellpadding="0" cellspacing="0">
    <tr>
      <th>#</th>
      <th>Username/Session ID</th>
      <th>Date/Time</th>
      <th>Page</th>
      <th>Action</th>
    </tr>
    <?PHP
	  // set page value
	  if(isset($_GET['page'])){
		$rec_start = ($_GET['page']-1)*20;
	  } else {
	  	$rec_start = 0;
	  }

	  $sql_query = "SELECT
					id
				 FROM
					api_ref_track
				 WHERE
					api_id = ?
				 AND
				 	sess_id = ?
				  ORDER BY date_time DESC
				  LIMIT ".$rec_start.",20 ;";
	
	  $update_vals = array(
						  $_SESSION['api_id'],
						  $_GET['sess_id']
						  );

	  $stmt = $dbh->prepare($sql_query);					 
	  $result = $stmt->execute($update_vals);
	  
	  while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		$api_ref_track_tbl->get_db_vars($row['id']);
		if($api_ref_track_tbl->customer_id > 0){
			$customer_info_table->get_db_vars($api_ref_track_tbl->customer_id);
			$cust_id = $customer_info_table->first_name.' '.$customer_info_table->last_name;
		} else {
			$cust_id = $api_ref_track_tbl->sess_id;
		}
		$rec_start++;
		echo '<tr>
				<td>'.$rec_start.'</td>
				<td>'.$cust_id.'</td>
				<td>'.date('n/j/Y h:i:s A',strtotime($api_ref_track_tbl->date_time)).'</td>
				<td><a href="'.$api_ref_track_tbl->page.'" target="_blank">'.$api_ref_track_tbl->page.'</a></td>
				<td class="action"><a href="?section=users&mode=view&id='.$row['id'].'" class="view">View</a></td>
			  </tr>';
	  }
	?>
    <tr>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th style="text-align:right">Page:</th>
      <th style="text-align:left"><?PHP echo get_page_lnks(); ?></th>
    </tr>
  </table>
  <form action="" method="post" class="jNice">
  <table>
  <tr>
  <td><input name="allusersessintscsv" type="hidden" value="1" /><input name="filter" type="submit" value="Generate CSV" /></td>
  </tr>
  </table>
  </form>
