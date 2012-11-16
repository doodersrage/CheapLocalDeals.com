<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if customer is not logged in redirect to login page
if ($_SESSION['customer_logged_in'] != 1) header("Location: ".SITE_SSL_URL."customer_admin/account_login.deal");			

// write page data
$page_output = '';
if($_POST['id'] > 0) {
// query active certificates
		$sql_query = "SELECT
						id
					 FROM
						order_items
					 WHERE
						order_id = '".$_POST['id']."'
					 ;";
		$order_row = $dbh->queryAll($sql_query);
		
		echo '<table class="advertiser_form"><tr><td>';
		// check for returned rows
		if(count($order_row) == 0) {
			$page_output = '<span class="cert-error">Order information was not found.</span>';
		} else {
			// print list of items
			$page_output = '<center><strong>Order Details</strong></center>';
			$page_output .= '<table align="center" class="frn_box" style="width: 808px;">';
			$page_output .= '<tr><th class="shop_header">Item #</th><th class="shop_header">Location Name</th><th class="shop_header">Requirements</th><th class="shop_header">Price</th><th class="shop_header">Quantity</th></tr>';
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
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;
echo '</td></tr></table>';
?>