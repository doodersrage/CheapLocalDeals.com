<?PHP

// view and manage orders and purchased certificates

// handles and displays orders admin page
class orders_frm {
  
  // delete orders
  function delete_orders() {
	global $dbh;
	foreach($_POST['delete_orders'] as $selected_orders) {
	  $stmt = $dbh->prepare("DELETE FROM orders WHERE id = '".$selected_orders."';");
	  $stmt->execute();
	  $stmt = $dbh->prepare("DELETE FROM order_items WHERE order_id = '".$selected_orders."';");
	  $stmt->execute();
	  $stmt = $dbh->prepare("DELETE FROM certificate_orders WHERE order_id = '".$selected_orders."';");
	  $stmt->execute();
	}
  }
  
  // delete orders
  function delete_certificates() {
	global $dbh;
	foreach($_POST['delete_certificates'] as $selected_delete_certificates) {
	  $stmt = $dbh->prepare("DELETE FROM certificate_orders WHERE id = '".$selected_delete_certificates."';");
	  $stmt->execute();
	}
  }
  
  // prints order info
  function view_order_info($message = '') {
	$recent_orders_view = open_table_listing_form('View Order Info','view_page','','post',$message);
	$recent_orders_view .= $this->order_info_content();
	$recent_orders_view .= close_table_form();
  return $recent_orders_view;
  }
  
  function order_info_content() {
	global $dbh, $customer_info_table, $adv_info_tbl, $cert_amt_tbl, $pp_pmts_tbl, $odrs_tbl, $cert_odrs_tbl;
	
	// sets record limit per page	
	$page_limiter = ADMIN_PER_PAGE_RESULTS; 

	// draw table header
	$orders_listing = T.'<tr>'.LB;
	$orders_listing .= T.T.'<th colspan="4"><center><strong>Order Information</strong></center></th>'.LB;
	$orders_listing .= T.'</tr>'.LB;
	
	// get/print order info
	if(!empty($_GET['oid'])) {
		$odrs_tbl->get_db_vars($_GET['oid']);
		
		if($odrs_tbl->id > 0) {
					  
		  // start output buffer
		  ob_start();
						
			var_export(unserialize($odrs_tbl->order_notes));
			
			$order_notes = ob_get_contents();
			
		  ob_end_clean();
	  
		  // pull customer data
		  $customer_info_table->get_db_vars($odrs_tbl->customer_id);
		  $customer_name = $customer_info_table->first_name . ' ' . $customer_info_table->last_name;
		  
		  $order_info = "<center>"."\n";
		  $order_info .= "<table align=\"center\">"."\n";
		  $order_info .= "<tr><th align=\"center\" colspan=\"2\"></th></tr>"."\n";
		  $order_info .= "<tr><td align=\"right\">Order Date:</td><td>".date('m/d/Y g:iA',strtotime($odrs_tbl->date_added))."</td></tr>"."\n";
		  $order_info .= "<tr><td align=\"right\">Order Total:</td><td>$".$odrs_tbl->order_total."</td></tr>"."\n";
		  $order_info .= "<tr><td align=\"right\">Customer Name:</td><td><a href=\"?sect=regcustomer&mode=edit&cid=".$odrs_tbl->customer_id."\">".$customer_name."</a></td></tr>"."\n";
		  $order_info .= "<tr><td align=\"right\">Promo Code:</td><td>".$odrs_tbl->promo_code."</td></tr>"."\n";
		  $order_info .= "<tr><th align=\"center\" colspan=\"2\">Order Payment Information</th></tr>"."\n";
		  $order_info .= "<tr><td align=\"right\">Credit Card Type:</td><td>".$odrs_tbl->credit_card_type."</td></tr>"."\n";
		  $order_info .= "<tr><td align=\"right\">Credit Card Number:</td><td>".$odrs_tbl->credit_card_number."</td></tr>"."\n";
		  $order_info .= "<tr><td align=\"right\">Credit Card CVV:</td><td>".$odrs_tbl->cvv."</td></tr>"."\n";
		  $order_info .= "<tr><td align=\"right\">Credit Card Expiration Date:</td><td>".$odrs_tbl->expiration_date."</td></tr>"."\n";
		  $order_info .= "<tr><td align=\"right\">Order Notes:</td><td>".$order_notes."</td></tr>"."\n";
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
						  order_id = '".$_GET['oid']."'
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

			$order_info .= $page_output.'</td></tr></table>
			</center>';
			
			$orders_listing .= table_listing_span_form_field($order_info);
			$orders_listing .= table_listing_span_form_field('<center><a href="javascript: history.go(-1)"><--Back</a></center>');
		} else {
		  $orders_listing .= table_listing_span_form_field('<center>The selected order was not found<br><a href="'.$_SESSION['previous_page'].'"><--Back</a></center>');
		}
	} elseif(!empty($_GET['pid'])) {
		$page_output = '<table align="center" class="order_listing">';
		$page_output .= '<tr><th>Row</th><th>Location Name</th><th>Requirements</th></tr>';
		
		$pp_pmts_tbl->assign_db_vars_token($_GET['pid']);
		
		$order_info = "<center>"."\n";
		$order_info .= "<table align=\"center\">"."\n";
		$order_info .= "<tr><th align=\"center\" colspan=\"2\"></th></tr>"."\n";
		$order_info .= "<tr><td align=\"right\">Order Date:</td><td>".date('m/d/Y g:iA',strtotime($pp_pmts_tbl->date))."</td></tr>"."\n";
		$order_info .= "<tr><td align=\"right\">Order Total:</td><td>$".$pp_pmts_tbl->amount."</td></tr>"."\n";
		$order_info .= "<tr><th align=\"center\" colspan=\"2\">Order Payment Information</th></tr>"."\n";
		$order_info .= "<tr><td align=\"right\">Approved:</td><td>".$pp_pmts_tbl->approved."</td></tr>"."\n";
		$order_info .= "<tr><th align=\"center\" colspan=\"2\">Order Items</th></tr>"."\n";
		$order_info .= "<tr><td align=\"center\" colspan=\"2\">"."\n";

		$sql_query = "SELECT
						id
					 FROM
						certificate_orders
					 WHERE
						token = '".$_GET['pid']."'
					 ;";
		$order_row = $dbh->queryAll($sql_query);
		
		$sel_row = 0;
		foreach($order_row as $cur_row) {
		  $sel_row++;
		  $cert_odrs_tbl->get_db_vars($cur_row['id']);
		  $adv_info_tbl->get_db_vars($cert_odrs_tbl->advertiser_id);
		  $page_output .= '<tr><td>'.$sel_row.'</td><td>'.$adv_info_tbl->company_name.'</td><td>'.$cert_odrs_tbl->requirements.'</td></tr>';				
		}
		$page_output .= '</table>';
		$order_info .= $page_output.'</td></tr></table>
		</center>';

		$orders_listing .= table_listing_span_form_field($order_info);
		$orders_listing .= table_listing_span_form_field('<center><a href="'.$_SESSION['previous_page'].'"><--Back</a></center>');
	} else {
	  $orders_listing .= table_listing_span_form_field('<center>The selected order was not found<br><a href="'.$_SESSION['previous_page'].'"><--Back</a></center>');
	}
	
  return $orders_listing;
  }
}

?>