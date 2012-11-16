<?PHP

global $pgs_tbl,$url_nms_tbl, $dbh, $stes_tbl, $url_nms_tbl, $geo_data;

// assign previous url link
assign_previous_url_val();

// check for set get var
if (!empty($_GET['pid'])) {
	
	$pgs_tbl->get_db_vars($_GET['pid']);
	
	// set page header -- only assign for static header data
	// set header title
	if ($pgs_tbl->header_title != '') {
		$page_header_title = $pgs_tbl->header_title;
	} else {
		$page_header_title = DEF_PAGE_HEADER_TITLE;
	}
	
	// set meta description
	if ($pgs_tbl->meta_description != '') {
		$page_meta_description = $pgs_tbl->meta_description;
	} else {
		$page_meta_description = DEF_PAGE_META_DESC;
	}
	
	// set meta keywords
	if ($pgs_tbl->meta_keywords != '') {
		$page_meta_keywords = $pgs_tbl->meta_keywords;
	} else {
		$page_meta_keywords = DEF_PAGE_META_KEYWORDS;
	}
	
	$page_output = ($pgs_tbl->display_name != 1 ? '<center><strong>'.$pgs_tbl->name.'</strong></center>' : '');
	$page_output .= $pgs_tbl->header_content;
	$page_output .= $pgs_tbl->footer_content;
} else {

	// if page id value has not been set load header defaults and inform page has not been found
	$page_header_title = DEF_PAGE_HEADER_TITLE;
	$page_meta_description = DEF_PAGE_META_DESC;
	$page_meta_keywords = DEF_PAGE_META_KEYWORDS;

	$page_output = '<center><strong>Page Not Found</strong></center>';
}

// this script writes the content for the sites logoff page and handles search form submissions
$selTemp = 'pages.php';
$selHedMetTitle = $page_header_title;
$selHedMetDesc = $page_meta_description;
$selHedMetKW = $page_meta_keywords;
?>