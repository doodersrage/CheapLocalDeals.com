<?PHP
$orders_list_output = '';

if(!empty($_GET['id'])){
  if($_GET['id'] > 0) {
  // query active certificates
		  $sql_query = "SELECT
						  id
					   FROM
						  order_items
					   WHERE
						  order_id = '".$_GET['id']."'
					   ;";
		  $order_row = $dbh->queryAll($sql_query);
		  
		  // check for returned rows
		  if(count($order_row) == 0) {
			  $page_output = '<span class="cert-error">Order information was not found.</span>';
		  } else {
			  // print list of items
			  $page_output = '<h1>Order Details</h1>';
			  $page_output .= '<table align="center" class="frn_box" style="width:100%;">';
			  $page_output .= '<tr><th>Item #</th><th>Location</th><th>Reqs</th><th>Price</th><th>Quant</th></tr>';
			  $sel_row = 0;
			  // cycle through selected rows
			  foreach($order_row as $cur_row) {
		  
				  $sel_row++;
				  $odr_itms_tbl->get_db_vars($cur_row['id']);
				  $adv_info_tbl->get_db_vars($odr_itms_tbl->item_id);
				  
				  // get customer data
				  $sql_query = "SELECT
								  requirements
							   FROM
								  certificate_orders
							   WHERE
								  customer_id = '".$_SESSION['customer_id']."'
							   AND
								  advertiser_id = '".$odr_itms_tbl->item_id."'
							   AND
								  certificate_amount_id = '".$odr_itms_tbl->certificate_value_id."'
							   ;";
				  $rows = $dbh->queryRow($sql_query);
				  
				  $page_output .= '<tr><td class="frn_conbox">'.$sel_row.'</td><td class="frn_conbox">'.$adv_info_tbl->company_name.'</td><td class="frn_conbox">'.$rows['requirements'].'</td><td class="frn_conbox">$'.$odr_itms_tbl->item_value.'</td><td class="frn_conbox">'.$odr_itms_tbl->item_quantity.'</td></tr>';				
			  }
			  $page_output .= '</table>';
		  }
  } else {
	  $page_output = '<center><strong><font color="red">You must first select an order to be viewed.</font></strong></ceonter>';
  }
} else {
  $orders_list = $odrs_tbl->list_orders_customer($_SESSION['customer_id']);
	  
  krsort($orders_list);
	  
  if(is_array($orders_list)) {
	  if (count($orders_list) > 0) {
		  $orders_list_output = '<h1>Previous Orders List:</h1>';		
		  $orders_list_output .= '<table align="center" class="frn_box" style="width: 100%;">';		
		  $orders_list_output .= '<tr><th class="shop_header">Order Date/Time</th><th class="shop_header">Order Total</th><th class="shop_header">Payment Type</th></tr>';		
		  foreach($orders_list as $cur_order) {
			$orders_list_output .= '<tr><td class="frn_conbox"><a href="'.MOB_SSL_URL.'?action=manageAcc&section=orders&id='.$cur_order['id'].'" >'.date('m/d/Y g:iA',strtotime($cur_order['date_added'])).'</a></td><td class="frn_conbox">$'.$cur_order['order_total'].'</td><td class="frn_conbox">'.$cur_order['credit_card_type'].'</td></tr>';		
		  }
		  $orders_list_output .= '</table>';		
	  } else {
		  $orders_list_output = '<span class="cert-error">You do not appear to have any orders.</span>';		
	  }
  } else {
	  $orders_list_output = '<span class="cert-error">You do not appear to have any orders.</span>';		
  }
  $page_output = $orders_list_output;
}
?>