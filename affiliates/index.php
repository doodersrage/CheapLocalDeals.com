<?PHP
// load application top
require('../includes/application_top.php');

if (!class_exists('admin_users_table')) {
	// include admin_users_table class
	require(CLASSES_DIR.'tables/admin_users.php');
	$admin_users_table = new admin_users_table;
}

// check admin login status
if($admin_users_table->user_login_session_check() == 0) {
	header("Location: login_page.php");
}

// load page layout functions
require(SITE_ADMIN_FUNCTIONS_DIR.'layout.php');

// added to allow user lockout
if ($_SESSION['allowed_access']['Affiliate System'] == 1) {
	
	// load page based on set variables
	switch ($_GET['sect']) {
	case 'affiliates':
		
		// load settings orders class
		require(SITE_AFFILIATE_CLASSES_DIR.'affiliates.php');
		$affiliates_manage_page = new affiliates_manage;
		
		// delete selected admin_users
		if(isset($_POST['delete_affiliates'])) {
			$affiliates_manage_page->delete_affiliates();
		}
			
		// write page header
		$page_content = page_header('Affiliates');
	
		// select affiliates output function
		switch ($_GET['mode']) {
		// edit affiliates category
		case 'view':
			$page_content .= $affiliates_manage_page->affiliates_listing();
		break;
		case 'add':
			$page_content .= $affiliates_manage_page->add_affiliates();
		break;
		case 'addcheck':
			$aff_usrs_tbl->get_post_vars();
			$error_message = $affiliates_manage_page->form_check();
			if (empty($error_message)) {
				$aff_usrs_tbl->insert();
				$page_content .= '<center>New advertiser has been added</center>'.LB;
				$page_content .= $affiliates_manage_page->affiliates_listing();
			} else {
				$page_content .= $affiliates_manage_page->add_affiliates($error_message);
			} 
		break;
		case 'edit':
			$aff_usrs_tbl->get_db_vars($_GET['pid']);
			$page_content .= $affiliates_manage_page->edit_affiliates();
		break;
		case 'editcheck':
			$aff_usrs_tbl->get_post_vars();
			$error_message = $affiliates_manage_page->form_check();
			if (empty($error_message)) {
				$aff_usrs_tbl->update();
				$page_content .= '<center>Affiliate Updated</center>'.LB;
				$page_content .= $affiliates_manage_page->affiliates_listing();
			} else {
				$page_content .= $affiliates_manage_page->edit_affiliates($error_message);
			} 
		break;
		}
			
		// write output to constant
		define('ADMIN_PAGE_CONTENT',$page_content);						
	break;
	case 'affiliatesreports':
	
		// load settings orders class
		require(SITE_AFFILIATE_CLASSES_DIR.'affiliate_tracking.php');
		$affiliate_tracking_page = new affiliate_tracking;

		// write page header
		$page_content = page_header('Affiliate Reports');
		
		// select affiliates output function
		switch ($_GET['mode']) {
		// edit affiliates category
		case 'advertiser':
			$page_content .= $affiliate_tracking_page->affiliates_tracking_listing();
		break;
		case 'advertiserlistreport';
			$page_content .= $affiliate_tracking_page->affiliates_advert_tracking_listing();
			
			// download options
			if ($_GET['action'] == 'download_xls') {
			    header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");;
				header("Content-Disposition: attachment;filename=".strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $_GET['pid']))."-adverts.xls "); 
				header("Content-Transfer-Encoding: binary ");
				
				// header for spreadsheet
				$headers = array('Date','Company Name','Plan','Paid So Far','Gift Certificates Sum');
				
				// build header row
				$xls_output = implode(T,$headers).LB;
				
				$sql_query = "SELECT
								id,
								company_name,
								customer_level,
								date_created
							 FROM
								advertiser_info
							 WHERE
								(link_affiliate_code = '".$_GET['pid']."'
							 OR
								affiliate_code = '".$_GET['pid']."') ";
				$sql_query .= "ORDER BY date_created DESC
							;";
				
				$rows = $dbh->queryAll($sql_query);
				
				foreach ($rows as $pages) {
				
				// reset row output
				$cur_row = array();
				
				$cur_row[] = date('n/j/Y h:i:s A',strtotime($pages['date_created']));
				$cur_row[] = $pages['company_name'];

				// load selected plan details
				$adv_lvls_tbl->get_db_vars($pages['customer_level']);
				$cur_row[] = $adv_lvls_tbl->level_name;
				
				// get advertiser paid so far
				$sql_query = "SELECT
								sum(payment) as payment_amt
							 FROM
								membership_process
							 WHERE
								advertiser_id = '".$pages['id']."'
							 ;";
				$rows = $dbh->queryRow($sql_query);
				$cur_row[] = $rows['payment_amt'];
				
				// get certificates sold count
				$sql_query = "SELECT
								count(cm.cost) as cost
							 FROM
								certificate_orders co LEFT JOIN certificate_amount cm ON co.certificate_amount_id = cm.id
							 WHERE
								co.advertiser_id = '".$pages['id']."'
							 ;";
				$rows = $dbh->queryRow($sql_query);
				$cur_row[] = $rows['cost'];
							
				$xls_output .= implode(T,$cur_row).LB;
					
				}
				
				// print blank line
				$xls_output .= LB;
				
				// header for spreadsheet
				$headers = array('Date Purchased','Certificate Amount','Certificate Cost','Requirements','Certificate Code');
				
				// build header row
				$xls_output .= implode(T,$headers).LB;
				
				$sql_query = "SELECT
								id,
								customer_id,
								advertiser_id,
								requirements,
								certificate_amount_id,
								certificate_code,
								date_added
							 FROM
								certificate_orders
							 WHERE
								advertiser_id = '".$_GET['advertid']."' ";
				if (!empty($_GET['start_date'])) {
				$sql_query .= "AND
								date_added >= '".$_GET['start_date']."'
							 AND
								date_added <= '".$_GET['end_date']."' ";
				}
				$sql_query .= "ORDER BY date_added DESC
				;";
				
				$rows = $dbh->queryAll($sql_query);
				
				// set total vals
				$total_saved = 0;
				$total_cost = 0;
				
				foreach($rows as $certs){
	
					// reset row output
					$cur_row = array();
					
					$cur_row[] = date('n/j/Y h:i:s A',strtotime($certs['date_added']));
					$cert_amt_tbl->get_db_vars($certs['certificate_amount_id']);
					$cur_row[] = $cert_amt_tbl->discount_amount;
					$total_saved += $cert_amt_tbl->discount_amount;
					$cur_row[] = $cert_amt_tbl->cost;
					$total_cost += $cert_amt_tbl->cost;
					$cur_row[] = str_replace(array(T,LB),array(" "," "),$certs['requirements']);
					$cur_row[] = $certs['certificate_code'];
					$xls_output .= implode(T,$cur_row).LB;
						
				}
				
				// print blank line
				$xls_output .= LB;
				
				// header for spreadsheet
				$headers = array('Totals:');
				
				// build header row
				$xls_output .= implode(T,$headers).LB;
				
				// header for spreadsheet
				$headers = array('Saved:',$total_saved);
				
				// build header row
				$xls_output .= implode(T,$headers).LB;
				
				// header for spreadsheet
				$headers = array('Cost:',$total_cost);
				
				// build header row
				$xls_output .= implode(T,$headers).LB;
						
				echo $xls_output;
				die();
			}
		break;
		// edit affiliates category
		case 'advertiserlist':
			$page_content .= $affiliate_tracking_page->affiliates_advertisers_listing();
			
			// download options
			if ($_GET['action'] == 'download_xls') {
			    header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");;
				header("Content-Disposition: attachment;filename=".strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $_GET['pid']))."-adverts.xls "); 
				header("Content-Transfer-Encoding: binary ");
				
				// header for spreadsheet
				$headers = array('Date','Company Name','Plan','Paid So Far','Gift Certificates Sum');
				
				// build header row
				$xls_output = implode(T,$headers).LB;
				
				$sql_query = "SELECT
								id,
								company_name,
								customer_level,
								date_created
							 FROM
								advertiser_info
							 WHERE
								(link_affiliate_code = '".$_GET['pid']."'
							 OR
								affiliate_code = '".$_GET['pid']."') ";
							if (!empty($_GET['start_date'])) {
							$sql_query .= "AND
											date_created >= '".$_GET['start_date']."'
										 AND
											date_created <= '".$_GET['end_date']."' ";
							}
							$sql_query .= "ORDER BY date_created DESC
							;";
				
				$rows = $dbh->queryAll($sql_query);
				
				foreach ($rows as $pages) {
				
				// reset row output
				$cur_row = array();
				
				$cur_row[] = date('n/j/Y h:i:s A',strtotime($pages['date_created']));
				$cur_row[] = $pages['company_name'];

				// load selected plan details
				$adv_lvls_tbl->get_db_vars($pages['customer_level']);
				$cur_row[] = $adv_lvls_tbl->level_name;
				
				// get advertiser paid so far
				$sql_query = "SELECT
								sum(payment) as payment_amt
							 FROM
								membership_process
							 WHERE
								advertiser_id = '".$pages['id']."'
							 ;";
				$rows = $dbh->queryRow($sql_query);
				$cur_row[] = $rows['payment_amt'];
				
				// get certificates sold count
				$sql_query = "SELECT
								count(cm.cost) as cost
							 FROM
								certificate_orders co LEFT JOIN certificate_amount cm ON co.certificate_amount_id = cm.id
							 WHERE
								co.advertiser_id = '".$pages['id']."'
							 ;";
				$rows = $dbh->queryRow($sql_query);
				$cur_row[] = $rows['cost'];
							
				$xls_output .= implode(T,$cur_row).LB;
					
				}
				
				echo $xls_output;
				die();
			}
			
		break;
		}
			
		// write output to constant
		define('ADMIN_PAGE_CONTENT',$page_content);			
	break;
	case 'salesrepreports':
		
		// write page header
		$page_content = page_header('Advertiser to Advertiser Affiliates Report');
		
		// load settings orders class
		require(SITE_AFFILIATE_CLASSES_DIR.'advertisers_advertisers.php');
		$advertisers_advertisers_report = new advertisers_advertisers_report;
		
		// select affiliates output function
		switch ($_GET['mode']) {
		// edit affiliates category
		case 'signedup':
			$page_content .= $advertisers_advertisers_report->advertisers_tracking_listing();
		break;
		case 'advertdata':
			$page_content .= $advertisers_advertisers_report->affiliates_advertisers_listing();
		break;
		case 'advertiserlistreport':
			$page_content .= $advertisers_advertisers_report->affiliates_advert_tracking_listing();
		break;
		}
			
		// write output to constant
		define('ADMIN_PAGE_CONTENT',$page_content);
	break;
	default:
		// write output to constant
		define('ADMIN_PAGE_CONTENT','');
	break;
	}
	
} else {
	// write output to constant
	define('ADMIN_PAGE_CONTENT','<center><strong>You are not permitted to view this section of the site.</strong></center>');
}

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Affiliates System';

// assign header constant
$prnt_header = new prnt_header();
$prnt_header->page_header_title = $page_header_title;
$prnt_header->page_meta_description = $page_meta_description;
$prnt_header->page_meta_keywords = $page_meta_keywords;
$prnt_header->admin = 2;
define('PAGE_HEADER',$prnt_header->print_page_header());

// load page left nav
require('left_nav.php');
// write left nav to constant
define('LEFT_NAV',prnt_left_nav());

//// clear output buffer
//ob_end_clean();


// start output buffer
ob_start();

// load template
require(TEMPLATE_AFFILIATE_DIR.'index.php');

$html = ob_get_contents();

ob_end_clean();

print_page($html);
?>