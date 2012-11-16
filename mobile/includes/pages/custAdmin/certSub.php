<?PHP

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

// check for returned rows
if(count($selected_rows) == 0) {
	$page_output = '<span class="cert-error"><span class="cert-error">No active certificates were found.</span>';
} else {
	// print list of active certificates
	$page_output = '<center><strong>Active Certificates</strong></center>';
	$page_output .= '<table align="center" class="frn_box" style="width: 100%;">';
	$page_output .= '<tr><th>Row</th><th>Location</th><th>Reqs</th><th>Cert Code</th><th>View</th></tr>';
	$sel_row = 0;
	// cycle through found records
	foreach($selected_rows as $cur_row) {
		// get customer data
		$sel_row++;
		$cert_odrs_tbl->get_db_vars($cur_row['id']);
		$adv_info_tbl->get_db_vars($cert_odrs_tbl->advertiser_id);

		$page_output .= '<tr><td>'.$sel_row.'</td><td>'.$adv_info_tbl->company_name.'</td><td>'.$cert_odrs_tbl->requirements.'</td><td>'.$cert_odrs_tbl->certificate_code.'</td><td><a href="'.MOB_URL.'viewCert.php?cert_id='.$cert_odrs_tbl->cert_id.'">View</a></td></tr>';				
	}
	$page_output .= '</table>';
}

?>