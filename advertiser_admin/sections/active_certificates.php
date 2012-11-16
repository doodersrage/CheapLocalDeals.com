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
						certificate_orders
					 WHERE
						advertiser_id = '".$_SESSION['advertiser_id']."'
					 AND
						enabled = 1
					 ;";
		$selected_rows = $dbh->queryAll($sql_query);
		echo '<table class="advertiser_form"><tr><td>';
		
		$page_output = '';

		// check for returned records
		if(count($selected_rows) == 0) {
			$page_output .= '<span class="cert-error">No active certificates were found.</span>';
		} else {
			// print list of active certificates
			$page_output .= '<center><strong>Active Certificates</strong></center>';
			$page_output .= '<table align="center" class="frn_box">';
			$page_output .= '<tr><th class="shop_header">Row</th><th class="shop_header">Customer Name</th><th class="shop_header">Requirements</th><th class="shop_header">Certificate Code</th><th class="shop_header">View</th><th class="shop_header">Disable</th></tr>';
			$sel_row = 0;
			// cycle through selected records
			foreach($selected_rows as $cur_row) {
				// get customer data
				$sel_row++;
				$cert_odrs_tbl->get_db_vars($cur_row['id']);
				$customer_info_table->get_db_vars($cert_odrs_tbl->customer_id);
				$customer_name = $customer_info_table->first_name.' '.$customer_info_table->last_name;
				$page_output .= '<tr id="certlst_'.$cert_odrs_tbl->id.'"><td class="frn_conbox">'.$sel_row.'</td><td class="frn_conbox">'.$customer_name.'</td><td class="frn_conbox">'.$cert_odrs_tbl->requirements.'</td><td class="frn_conbox">'.$cert_odrs_tbl->certificate_code.'</td><td class="frn_conbox"><a target="_blank" href="'.SITE_URL.'customer_admin/view_certificate.deal?cert_id='.$cert_odrs_tbl->cert_id.'">View</a></td><td class="frn_conbox"><a href="javascript:void(0)" onclick="disable_cert('.$cert_odrs_tbl->id.')" >Disable</a></td></tr>';				
			}
			$page_output .= '</table>';
		}
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;
echo '</td></tr></table>';
?>