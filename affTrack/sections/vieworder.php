<form action="" class="jNice">
<h3>Order View</h3>
<?PHP
$odrs_tbl->get_db_vars($_GET['id']);

if($odrs_tbl->id > 0) {
			  
  // start output buffer
  ob_start();
				
	var_export(unserialize($odrs_tbl->order_notes));
	
	$order_notes = ob_get_contents();
	
  ob_end_clean();

  // pull customer data
  $customer_info_table->get_db_vars($odrs_tbl->customer_id);
  $customer_name = $customer_info_table->first_name . ' ' . $customer_info_table->last_name;
  
  $order_info .= "<table>"."\n";
  $order_info .= "<tr><th align=\"center\" colspan=\"2\"></th></tr>"."\n";
  $order_info .= "<tr><td align=\"right\">Order Date:</td><td>".date('m/d/Y g:iA',strtotime($odrs_tbl->date_added))."</td></tr>"."\n";
  $order_info .= "<tr><td align=\"right\">Order Total:</td><td>$".$odrs_tbl->order_total."</td></tr>"."\n";
  $order_info .= "<tr><td align=\"right\">Customer Name:</td><td>".$customer_name."</td></tr>"."\n";
  $order_info .= "<tr><td align=\"right\">Promo Code:</td><td>".$odrs_tbl->promo_code."</td></tr>"."\n";
  $order_info .= "<tr><th align=\"center\" colspan=\"2\">Order Items</th></tr>"."\n";
  $order_info .= "<tr><td align=\"center\" colspan=\"2\">"."\n";
  
  // gets and prints orders items info
  $sql_query = "SELECT
				  id,
				  item_id,
				  certificate_value_id,
				  item_type,
				  item_value,
				  item_quantity
			   FROM
				  order_items
			   WHERE
				  order_id = '".$_GET['id']."'
			   ;";
  $order_row = $dbh->queryAll($sql_query);
  
  if(count($order_row) == 0) {
	  $page_output = '<center><strong><font color="red">Order information was not found.</font></strong></center>';
  } else {
	// print list of items
	$page_output = '<table align="center" class="order_listing">';
	$page_output .= '<tr><th>Row</th><th>Location Name</th><th>Requirements</th><th>Value</th><th>Price</th><th>Quantity</th></tr>';
	$sel_row = 0;
	foreach($order_row as $cur_row) {
		// get customer data
		
		$sql_query = "SELECT
						id
					 FROM
						certificate_orders
					 WHERE
						customer_id = '".$odrs_tbl->customer_id."'
					 AND
						advertiser_id = '".$cur_row['item_id']."'
					 AND
						certificate_amount_id = '".$cur_row['certificate_value_id']."'
					 ;";
		$rows = $dbh->queryRow($sql_query);
		
		$cert_odrs_tbl->get_db_vars($rows['id']);

		$sel_row++;
		$adv_info_tbl->get_db_vars($cur_row['item_id']);
		$cert_amt_tbl->get_db_vars($cur_row['certificate_value_id']);
			
		if(is_numeric($cert_amt_tbl->discount_amount)) {
		  $cert_disc_amt = $cert_amt_tbl->discount_amount;
		} else {
		  $cert_disc_amt = $adv_info_tbl->certificate_requirements[$cert_amt_tbl->id]['blank_val'];
		}
			
		$page_output .= '<tr><td>'.$sel_row.'</td><td>'.$adv_info_tbl->company_name.'</td><td>'.$cert_odrs_tbl->requirements.'</td><td>$'.format_currency($cert_disc_amt).'</td><td>$'.$cur_row['item_value'].'</td><td>'.$cur_row['item_quantity'].'</td></tr>';				
	  }
	  $page_output .= '</table>';
	}

	$order_info .= $page_output.'</td></tr></table>';
	
	$orders_listing .= $order_info;
	$orders_listing .= '<center><a href="javascript: history.go(-1)"><--Back</a></center>';
} else {
  $orders_listing .= '<center>The selected order was not found<br><a href="history.go(-1)"><--Back</a></center>';
}
echo $orders_listing;
?>
</form>