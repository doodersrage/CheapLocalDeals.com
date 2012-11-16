<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if advertiser not logged in redirect to login page
if ($_SESSION['advertiser_logged_in'] != 1) header("Location: ".SITE_SSL_URL."advertiser_login.php");

// query active certificates
		$sql_query = "SELECT
						id
					 FROM
						advertiser_reviews
					 WHERE
						advertiser_id = '".$_SESSION['advertiser_id']."'
					 AND
						approved = 1
					 ORDER BY added DESC;";
		$selected_rows = $dbh->queryAll($sql_query);
		echo '<table class="advertiser_form"><tr><td>';
		
		$page_output = '';

		// check for returned records
		if(count($selected_rows) == 0) {
			$page_output .= '<span class="cert-error">No approved reviews were found.</span>';
		} else {
			// print list of active certificates
			$page_output .= '<center><strong>Approved Reviews</strong></center>';
			$page_output .= '<table align="center" class="frn_box">';
			$page_output .= '<tr><th class="shop_header">Row</th><th class="shop_header">Customer Name</th><th class="shop_header">Rating</th><th class="shop_header">Review</th><th class="shop_header">Added</th></tr>';
			$sel_row = 0;
			// cycle through selected records
			foreach($selected_rows as $cur_row) {
				// get customer data
				$sel_row++;
				$adv_rvws_tbl->get_db_vars($cur_row['id']);
				$customers_table->get_db_vars($adv_rvws_tbl->customer_id);
				$customer_name = '<a href="mailto:'.$customer_info_table->email_address.'">'.$customer_info_table->first_name.' '.$customer_info_table->last_name.'</a>';
				$page_output .= '<tr id="reviewlst_'.$adv_rvws_tbl->id.'">
				<td class="frn_conbox">'.$sel_row.'</td>
				<td class="frn_conbox">'.$customer_name.'</td>
				<td class="frn_conbox">'.$adv_rvws_tbl->rating.'</td>
				<td class="frn_conbox">'.$adv_rvws_tbl->review.'</td>
				<td class="frn_conbox">'.date('n/j/Y h:i:s A',strtotime($adv_rvws_tbl->added)).'</td>
				</tr>';				
			}
			$page_output .= '</table>';
		}
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;
echo '</td></tr></table>';
?>