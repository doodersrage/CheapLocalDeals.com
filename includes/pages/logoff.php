<?PHP

global $pgs_tbl,$url_nms_tbl, $dbh, $stes_tbl, $url_nms_tbl, $sessions;

// logs user out and clears current session
//HTTP_Session2::destroy();
$sessions->clear();

$page_output = '<div class="logged-out">You are now logged out.</div>';

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Log Off';
$page_meta_description = 'Log Off';
$page_meta_keywords = 'Log Off';
	
$content_arr = array();
$content_arr['$page_output$'] = $page_output;
$this->template_constants = $content_arr;

// set page header -- only assign for static header data
$this->page_header_title = $page_header_title;
$this->page_meta_description = $page_meta_description;
$this->page_meta_keywords = $page_meta_keywords;
$this->template_file = 'blank-new.php';

?>