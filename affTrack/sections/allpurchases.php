<?PHP
// page specific functions
function orders_total(){
	global $dbh;
	
	$sql_query = "SELECT
					sum(order_total) as total
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
	$sql_query .= " ;";
	
	$update_vals = array(
						$_SESSION['api_id']
						);
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute($update_vals);
	$row = $result->fetchRow();
			
	$total = $row['total'];

	// clear result set
	$result->free();
	
	// reset DB conn
	db_check_conn();
	
return $total;
}

function get_page_lnks(){
	global $dbh;
	
	$sql_query = "SELECT
					count(*) as cnt
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
	$sql_query .= " ;";
	
	$update_vals = array(
						$_SESSION['api_id']
						);
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute($update_vals);
	$row = $result->fetchRow();
			
	$count = $row['cnt'];

	// clear result set
	$result->free();
	
	// reset DB conn
	db_check_conn();
	
	$page_lnks = array();
	$cur_page = 0;
	for($i = 0;$i < $count;$i += 20){
		$cur_page++;
		$page_lnks[] = '<a href="?section=purchases&page='.$cur_page.'">'.$cur_page.'</a>';
	}
	
	$page_lnks = implode(', ',$page_lnks);
	
return $page_lnks;
}

prntDateFiltFrm();
?>
  <h3>All User Purchases</h3>
  <table cellpadding="0" cellspacing="0">
    <tr>
      <th>#</th>
      <th>Username/Session ID</th>
      <th>Date/Time</th>
      <th>Total</th>
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
	  $sql_query .= " ORDER BY date_added DESC
				  LIMIT ".$rec_start.",20 ;";
	
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
		echo '<tr>
				<td>'.$rec_start.'</td>
				<td>'.$customer_info_table->first_name.' '.$customer_info_table->last_name.'</td>
				<td>'.date('n/j/Y h:i:s A',strtotime($odrs_tbl->date_added)).'</td>
				<td>$'.format_currency($odrs_tbl->order_total).'</td>
				<td class="action"><a href="?section=purchases&mode=view&id='.$row['id'].'" class="view">View</a></td>
			  </tr>';
		$totalPurch += $odrs_tbl->order_total;
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
  <table cellpadding="0" cellspacing="0">
    <tr>
      <th style="text-align:right">Overall Total:</th>
      <th style="text-align:left">$<?PHP echo format_currency(orders_total()); ?></th>
    </tr>
    </table>
  <form action="" method="post" class="jNice">
  <table>
  <tr>
  <td><input name="allpurchasescsv" type="hidden" value="1" /><input name="filter" type="submit" value="Generate CSV" /></td>
  </tr>
  </table>
  </form>
