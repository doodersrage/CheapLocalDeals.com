<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// load page content
$pgs_tbl->get_db_vars(7);

// set page header -- only assign for static header data
$page_header_title = $pgs_tbl->header_title;
$page_meta_description = $pgs_tbl->meta_description;
$page_meta_keywords = $pgs_tbl->meta_keywords;

// assign header constant
$prnt_header = new prnt_header();
$prnt_header->page_header_title = $page_header_title;
$prnt_header->page_meta_description = $page_meta_description;
$prnt_header->page_meta_keywords = $page_meta_keywords;
define('PAGE_HEADER',$prnt_header->print_page_header());

$adv_info_tbl->authorization_code = $_GET['authcode'];
$page_output = $pgs_tbl->header_content;
$page_output .= '<center>'.$adv_info_tbl->authorize_advert_by_email().'</center>';
$page_output .= $pgs_tbl->footer_content;

// start output buffer
ob_start();
	
	// load template
	require(TEMPLATE_DIR.'blank.php');
	
	$html = ob_get_contents();
	
ob_end_clean();

print_page($html);
?>