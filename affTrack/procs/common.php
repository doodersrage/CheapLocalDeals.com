<?PHP
function generate_userint_csv() {
	global $customer_info_table, $api_ref_track_tbl, $dbh;

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");;
	header("Content-Disposition: attachment;filename=".strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', date("Y-m-d")))."-userints.xls "); 
	header("Content-Transfer-Encoding: binary ");

	// header for spreadsheet
	$headers = array('#','Username/Session ID','Last Update','Page Views');
	
	// build header row
	$xls_output = implode(T,$headers).LB;

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
	  $sql_query .= " ORDER BY sess_id, date_time DESC ;";
	
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
		
		// reset row output
		$cur_row = array();
		
		$cur_row[] = $rec_start;
		$cur_row[] = $cust_id;
		$cur_row[] = date('n/j/Y h:i:s A',strtotime($api_ref_track_tbl->date_time));
		$cur_row[] = $api_ref_track_tbl->get_cnt_by_sess($row['sess_id']);
		
		$xls_output .= implode(T,$cur_row).LB;
	}
	
	echo $xls_output;
	
	die();

}

if($_POST['genUserIntCSV'] == 1) generate_userint_csv();

function generate_allusersessints_csv() {
	global $customer_info_table, $api_ref_track_tbl, $dbh;

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");;
	header("Content-Disposition: attachment;filename=".strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', date("Y-m-d")))."-allusersessints.xls "); 
	header("Content-Transfer-Encoding: binary ");

	// header for spreadsheet
	$headers = array('#','Username/Session ID','Last Update','Page Viewed');
	
	// build header row
	$xls_output = implode(T,$headers).LB;

	  $sql_query = "SELECT
					id
				 FROM
					api_ref_track
				 WHERE
					api_id = ?
				 AND
				 	sess_id = ?
				  ORDER BY date_time DESC;";
	
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
		
		// reset row output
		$cur_row = array();
		
		$cur_row[] = $rec_start;
		$cur_row[] = $cust_id;
		$cur_row[] = date('n/j/Y h:i:s A',strtotime($api_ref_track_tbl->date_time));
		$cur_row[] = $api_ref_track_tbl->page;
		
		$xls_output .= implode(T,$cur_row).LB;
	}
	
	echo $xls_output;
	
	die();

}

if($_POST['allusersessintscsv'] == 1) generate_allusersessints_csv();

function generate_allpurchases_csv() {
	global $customer_info_table, $api_ref_track_tbl, $odrs_tbl, $dbh;

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");;
	header("Content-Disposition: attachment;filename=".strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', date("Y-m-d")))."-allpurchases.xls "); 
	header("Content-Transfer-Encoding: binary ");

	// header for spreadsheet
	$headers = array('#','Username/Session ID','Date/Time','Total');
	
	// build header row
	$xls_output = implode(T,$headers).LB;

	  $sql_query = "SELECT
					id
				 FROM
					orders
				 WHERE
					api_id = ? ";
	  if(!empty($_SESSION['dateFilter'])){
		  if(!empty($_SESSION['dateFilter']['startDate'])) {
			  $sql_query .= " AND date_added >= '".$_SESSION['dateFilter']['startDate']."' ";
		  }
		  if(!empty($_SESSION['dateFilter']['endDate'])) {
			  $sql_query .= " AND date_added <= '".$_SESSION['dateFilter']['endDate']."' ";
		  }
	  }
	  $sql_query .= " ORDER BY date_added DESC ;";
	
	  $update_vals = array(
						  $_SESSION['api_id']
						  );

	  $stmt = $dbh->prepare($sql_query);					 
	  $result = $stmt->execute($update_vals);
	  $totalPurch = 0;
	  while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		$odrs_tbl->get_db_vars($row['id']);
		$customer_info_table->get_db_vars($odrs_tbl->customer_id);
		$rec_start++;
		
		$totalPurch += $odrs_tbl->order_total;
		
		// reset row output
		$cur_row = array();
		
		$cur_row[] = $rec_start;
		$cur_row[] = $customer_info_table->first_name.' '.$customer_info_table->last_name;
		$cur_row[] = date('n/j/Y h:i:s A',strtotime($odrs_tbl->date_added));
		$cur_row[] = format_currency($odrs_tbl->order_total);
		
		$xls_output .= implode(T,$cur_row).LB;
	}
	// total footer for spreadsheet
	$headers = array('Count:',$rec_start);
	// build header row
	$xls_output .= implode(T,$headers).LB;
	
	// total footer for spreadsheet
	$headers = array('Net Rev:',$totalPurch);
	// build header row
	$xls_output .= implode(T,$headers).LB;
	
	$shared_rev = $totalPurch * 0.35;
	// total footer for spreadsheet
	$headers = array('Shared Revenue:',$shared_rev);
	// build header row
	$xls_output .= implode(T,$headers).LB;
	
	echo $xls_output;
	
	die();

}

if($_POST['allpurchasescsv'] == 1) generate_allpurchases_csv();

function generate_usersignups_csv() {
	global $customer_info_table, $api_ref_track_tbl, $odrs_tbl, $dbh;

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");;
	header("Content-Disposition: attachment;filename=".strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', date("Y-m-d")))."-usersignups.xls "); 
	header("Content-Transfer-Encoding: binary ");

	// header for spreadsheet
	$headers = array('#','User Name','Email','State','Created');
	
	// build header row
	$xls_output = implode(T,$headers).LB;

	  $sql_query = "SELECT
					id
				 FROM
					customer_info
				 WHERE
					api_id = ? ";
	  if(!empty($_SESSION['dateFilter'])){
		  if(!empty($_SESSION['dateFilter']['startDate'])) {
			  $sql_query .= " AND date_created >= '".$_SESSION['dateFilter']['startDate']."' ";
		  }
		  if(!empty($_SESSION['dateFilter']['endDate'])) {
			  $sql_query .= " AND date_created <= '".$_SESSION['dateFilter']['endDate']."' ";
		  }
	  }
	  $sql_query .= " ORDER BY date_created DESC ;";
	
	  $update_vals = array(
						  $_SESSION['api_id']
						  );

	  $stmt = $dbh->prepare($sql_query);					 
	  $result = $stmt->execute($update_vals);
	  $totalPurch = 0;
	  while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		$customer_info_table->get_db_vars($row['id']);
		$cust_id = $customer_info_table->first_name.' '.$customer_info_table->last_name;
		$rec_start++;

		// reset row output
		$cur_row = array();
		
		$cur_row[] = $rec_start;
		$cur_row[] = $cust_id;
		$cur_row[] = $customer_info_table->email_address;
		$cur_row[] = $customer_info_table->state;
		$cur_row[] = date('n/j/Y h:i:s A',strtotime($customer_info_table->date_time));
		
		$xls_output .= implode(T,$cur_row).LB;
	}
	// total footer for spreadsheet
	$headers = array('Count:',$rec_start);
	// build header row
	$xls_output .= implode(T,$headers).LB;

	$payout = $rec_start * 1.50;
	// total footer for spreadsheet
	$headers = array('Payout:',$payout);
	// build header row
	$xls_output .= implode(T,$headers).LB;
	
	echo $xls_output;
	
	die();

}

if($_POST['usersignupscsv'] == 1) generate_usersignups_csv();

// generate date filter form
function prntDateFiltFrm(){
  ?>
  <h3>Filter By Date</h3>
  <?PHP 
  if(empty($_SESSION['dateFilter'])){
  ?>
  <form action="" method="post" class="jNice">
  <table>
  <tr>
  <td>Start: <input id="startDate" name="startDate" type="text" /></td><td>End: <input id="endDate" name="endDate" type="text" /><td><td><input name="filter" type="submit" value="Filter" /></td>
  </tr>
  </table>
  </form>
  <script type="text/javascript">
	$(function() {
		$("#startDate").datepicker();
		$("#endDate").datepicker();
	});
  </script>
  <?PHP 
  } else {
  ?>
  <form action="" method="post" class="jNice">
  <table>
  <tr>
  <td><input name="removeDateFilter" type="hidden" value="1" /><input name="filter" type="submit" value="Remove Date Filter" /></td>
  </tr>
  </table>
  </form>
  <?PHP 
  }
}
if($_GET['mode'] == 'logout'){
  unset($_SESSION['logged_in']);
  header('Location: index.php');
}

// assign filter values
if(!empty($_POST['startDate']) || !empty($_POST['endDate'])) {
	$startDate = explode('/',$_POST['startDate']);
	$startDate = $startDate[2].'-'.$startDate[0].'-'.$startDate[1];
	$_SESSION['dateFilter']['startDate'] = $startDate;
	$endDate = explode('/',$_POST['endDate']);
	$endDate = $endDate[2].'-'.$endDate[0].'-'.$endDate[1];
	$_SESSION['dateFilter']['endDate'] = $endDate;
}

if($_POST['removeDateFilter'] == 1) {
	unset($_SESSION['dateFilter']);
}

// process a change password request
if($_POST['changePassSub'] == 1){
	
	if(!empty($_POST['oldpassword']) && !empty($_POST['newpassword']) && !empty($_POST['confirmnewpassword'])){
		if($_POST['newpassword'] != $_POST['confirmnewpassword']){
		  $error = "New password and confirmation password do not match.";
		} else {
		  $sql_query = "SELECT
						  id
					   FROM
						  api_access
					   WHERE
						  id = ? AND
						  password = ?
						LIMIT 1;";
		  
		  $values = array(
						  $_SESSION['api_id'],
						  encrypt_password($_POST['oldpassword'])
						  );
		  
		  $stmt = $dbh->prepare($sql_query);					 
		  $result = $stmt->execute($values);
		  
		  $row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		  
		  if ($row['id'] > 0) {
			$sql_query = "UPDATE api_access
							SET 
							password = ?
							ptpassword = ?
						  WHERE id = ?
						  LIMIT 1;";
			
			$values = array(
							encrypt_password($_POST['oldpassword']),
							$_POST['oldpassword'],
							$_SESSION['api_id'],
							);
			
			$stmt = $dbh->prepare($sql_query);					 
			$result = $stmt->execute($values);
			$passwordupdated = 1;
		  } else {
			$error = "The old password entered does not appear to be correct. Please check your submission and try again.";
		  }
			
		}
	} else {
		$error = "One of the required fields have been left blank. Please check your submission and try again.";
	}
	
}


?>