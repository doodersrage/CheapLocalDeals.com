<?PHP

global $stes_tbl, $url_nms_tbl, $category_results_pg, $dbh, $city_type;

// assign previous url link
assign_previous_url_val();

// listing output
if(empty($_GET['id'])){
	$page_output = '<div class="header_txt">Browse Deals By State</div>';
	$page_output .= prntStates();
	$page_header_title = 'CheapLocalDeals.com - Browse Deals By State';
	$page_meta_description = DEF_PAGE_META_DESC;
	$page_meta_keywords = DEF_PAGE_META_KEYWORDS;
} else {
	// insert new list processor here
	$stes_tbl->get_db_vars($_GET['id']);
	$page_output = '<div class="header_txt">'.$stes_tbl->state.' Citys and Towns</div>';
	$city_type = array_reverse($city_type, true);
	foreach($city_type as $id => $value) {
		$page_output .= prnt_city_lsts($id, $stes_tbl->acn);
	}
	if($stes_tbl->page_title != '' || $stes_tbl->page_title != NULL) {
		$page_header_title = $stes_tbl->page_title;
	} else {
		$page_header_title = $stes_tbl->state.' Restaurant Coupons and Local Deals in '.$stes_tbl->acn.' - CheapLocalDeals.com';
	}
	if($stes_tbl->meta_description != '' || $stes_tbl->meta_description != NULL) {
		$page_meta_description = $stes_tbl->meta_description;
	} else {
		$page_meta_description = 'Find local restaurants deals in your area by state. '.$stes_tbl->state;
	}
	if($stes_tbl->meta_keywords != '' || $stes_tbl->meta_keywords != NULL) {
		$page_meta_keywords = $stes_tbl->meta_keywords;
	} else {
		$page_meta_keywords = strtolower('local deals by state,select state to find local deals,local deals,state selected local deals '.$stes_tbl->state.','.$stes_tbl->state.' restaurants deals,'.$stes_tbl->state.' restaurants,'.$stes_tbl->state.' deals,'.$stes_tbl->state.' savings');
	}
}

$selTemp = 'pages.php';
$selHedMetTitle = $page_header_title;
$selHedMetDesc = $page_meta_description;
$selHedMetKW = $page_meta_keywords;
?>