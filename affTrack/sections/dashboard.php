
<form action="" class="jNice">
  
  <h3>Newest User Purchases</h3>
  <table cellpadding="0" cellspacing="0">
    <tr>
      <th>Username/Session ID</th>
      <th>Date/Time</th>
      <th>Total</th>
      <th>Action</th>
    </tr>
    <?PHP
	  $sql_query = "SELECT
					id
				 FROM
					orders
				 WHERE
					api_id = ?
				  ORDER BY date_added DESC
				  LIMIT 15;";
	
	  $update_vals = array(
						  $_SESSION['api_id']
						  );

	  $stmt = $dbh->prepare($sql_query);					 
	  $result = $stmt->execute($update_vals);
	  $totalPurch = 0;
	  $cur_num = 0;
	  while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		$odrs_tbl->get_db_vars($row['id']);
		$customer_info_table->get_db_vars($odrs_tbl->customer_id);
		$cur_num++;
		echo '<tr>
				<td>'.$cur_num.'</td>
				<td>'.$customer_info_table->first_name.' '.$customer_info_table->last_name.'</td>
				<td>'.date('n/j/Y h:i:s A',strtotime($odrs_tbl->date_added)).'</td>
				<td>$'.format_currency($odrs_tbl->order_total).'</td>
				<td class="action"><a href="?section=purchases&mode=view&id='.$row['id'].'" class="view">View</a></td>
			  </tr>';
		$totalPurch += $odrs_tbl->order_total;
	  }
	?>
  </table>
  <table cellpadding="0" cellspacing="0">
    <tr>
      <th style="text-align:right">Overall Total:</th>
      <th style="text-align:left">$<?PHP echo format_currency($totalPurch); ?></th>
    </tr>
    </table>
  
  
  <h3>Newest User Views</h3>
  <table cellpadding="0" cellspacing="0">
    <tr>
      <th>#</th>
      <th>Username/Session ID</th>
      <th>Last Update</th>
      <th>Page Views</th>
      <th>Action</th>
    </tr>
    <?PHP
	  $sql_query = "SELECT
					DISTINCT sess_id
				 FROM
					api_ref_track
				 WHERE
					api_id = ?
				  ORDER BY sess_id, date_time DESC
				  LIMIT 15;";
	
	  $update_vals = array(
						  $_SESSION['api_id']
						  );

	  $stmt = $dbh->prepare($sql_query);					 
	  $result = $stmt->execute($update_vals);
	  $cur_num = 0;
	  while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		$api_ref_track_tbl->get_db_vars_by_sess($row['sess_id']);
		if($api_ref_track_tbl->customer_id > 0){
			$customer_info_table->get_db_vars($api_ref_track_tbl->customer_id);
			$cust_id = $customer_info_table->first_name.' '.$customer_info_table->last_name;
		} else {
			$cust_id = $api_ref_track_tbl->sess_id;
		}
		$cur_num++;
		echo '<tr>
				<td>'.$cur_num.'</td>
				<td>'.$cust_id.'</td>
				<td>'.date('n/j/Y h:i:s A',strtotime($api_ref_track_tbl->date_time)).'</td>
				<td>'.$api_ref_track_tbl->get_cnt_by_sess($row['sess_id']).'</td>
				<td class="action"><a href="?section=users&mode=view&sess_id='.$row['sess_id'].'" class="view">View</a></td>
			  </tr>';
	  }
	?>
  </table>
</form>
