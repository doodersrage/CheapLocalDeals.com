<?PHP
function get_page_lnks(){
	global $dbh;
	
	$sql_query = "SELECT
					DISTINCT sess_id
				 FROM
					api_ref_track
				 WHERE
					api_id = ?
					";
	if(!empty($_SESSION['dateFilter'])){
		if(!empty($_SESSION['dateFilter']['startDate'])) {
			$sql_query .= " AND date_time >= '".$_SESSION['dateFilter']['startDate']."' ";
		}
		if(!empty($_SESSION['dateFilter']['endDate'])) {
			$sql_query .= " AND date_time <= '".$_SESSION['dateFilter']['endDate']."' ";
		}
	}
	$sql_query .= "ORDER BY sess_id, date_time DESC
				 ;";
	
	$update_vals = array(
						$_SESSION['api_id']
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
		$page_lnks[] = '<a href="?section=users&page='.$cur_page.'">'.$cur_page.'</a>';
	}
	
	$page_lnks = implode(', ',$page_lnks);
	
return $page_lnks;
}

prntDateFiltFrm();
?>

  <h3>All User Views</h3>
  <table cellpadding="0" cellspacing="0">
    <tr>
      <th>#</th>
      <th>Username/Session ID</th>
      <th>Last Update</th>
      <th>Page Views</th>
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
					DISTINCT sess_id
				 FROM
					api_ref_track
				 WHERE
					api_id = ? ";
	  if(!empty($_SESSION['dateFilter'])){
		  if(!empty($_SESSION['dateFilter']['startDate'])) {
			  $sql_query .= " AND date_time >= '".$_SESSION['dateFilter']['startDate']."' ";
		  }
		  if(!empty($_SESSION['dateFilter']['endDate'])) {
			  $sql_query .= " AND date_time <= '".$_SESSION['dateFilter']['endDate']."' ";
		  }
	  }
	  $sql_query .= " ORDER BY sess_id, date_time DESC
				  LIMIT ".$rec_start.",20 ;";
	
	  $update_vals = array(
						  $_SESSION['api_id']
						  );

	  $stmt = $dbh->prepare($sql_query);					 
	  $result = $stmt->execute($update_vals);
	  
	  while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		$api_ref_track_tbl->get_db_vars_by_sess($row['sess_id']);
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
				<td>'.$api_ref_track_tbl->get_cnt_by_sess($row['sess_id']).'</td>
				<td class="action"><a href="?section=users&mode=view&sess_id='.$row['sess_id'].'" class="view">View</a></td>
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
  <td><input name="genUserIntCSV" type="hidden" value="1" /><input name="filter" type="submit" value="Generate CSV" /></td>
  </tr>
  </table>
  </form>
