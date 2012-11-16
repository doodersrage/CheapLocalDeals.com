<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if advertiser not logged in redirect to login page
if ($_SESSION['customer_logged_in'] != 1) header("Location: ".SITE_SSL_URL."customer_admin/account_login.deal");

// query active certificates
		$sql_query = "SELECT
						id
					 FROM
						certificate_orders
					 WHERE
						customer_id = '".$_SESSION['customer_id']."'
					 AND
						enabled = 1
					 AND
						date_added > '".date("Y-m-d",strtotime("-1 years"))."'
					 ORDER BY date_added DESC
					 ;";
		$selected_rows = $dbh->queryAll($sql_query);
		echo '<table class="advertiser_form"><tr><td>';

		// check for returned rows
		if(count($selected_rows) == 0) {
			$page_output = '<span class="cert-error"><span class="cert-error">No active certificates were found.</span>';
		} else {
			// print list of active certificates
			$page_output = '<center><strong>Active Certificates</strong></center>';
			$page_output .= '<table align="center" class="frn_box" style="width: 808px;">';
			$page_output .= '<tr><th class="frn_header">Row</th><th class="frn_header">Customer Name</th><th class="frn_header">Requirements</th><th class="frn_header">Certificate Code</th><th class="frn_header">Download</th></tr>';
			$sel_row = 0;
			// cycle through found records
			foreach($selected_rows as $cur_row) {
				// get customer data
				$sel_row++;
				$cert_odrs_tbl->get_db_vars($cur_row['id']);
				$adv_info_tbl->get_db_vars($cert_odrs_tbl->advertiser_id);

				$page_output .= '<tr><td>'.$sel_row.'</td><td>'.$adv_info_tbl->company_name.'</td><td>'.$cert_odrs_tbl->requirements.'</td><td>'.$cert_odrs_tbl->certificate_code.'</td><td><a target="_blank" href="'.SITE_URL.'customer_admin/view_certificate.deal?cert_id='.$cert_odrs_tbl->cert_id.'">Download</a></td></tr>';				
			}
			$page_output .= '</table>';
		}
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;
echo '</td></tr></table>';
?>