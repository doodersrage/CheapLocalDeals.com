<?PHP
global $stes_tbl, $url_nms_tbl, $dbh, $cities_tbl;

// assign previous url link
assign_previous_url_val();

if(!empty($_GET['cid'])){
	
  // print category listing if available
  $cities_tbl->get_db_vars($_GET['cid']);
  if(!empty($_GET['ccid']) && $_GET['ccid'] != 'dom') $cats_tbl->get_db_vars($_GET['ccid']);
  $page_output = '<div class="header_txt">'.(!empty($_GET['ccid']) ? $_GET['ccid'] != 'dom' ? $cats_tbl->category_name.' ' : 'Deals Of The Month ' : '').'Listings For '.$cities_tbl->city.', '.$cities_tbl->state.'</div>';
  if($_GET['ccid'] != 'dom'){
	$page_output .= '<a class="linkBtn" href="'.MOB_URL.'cats/'.$_GET['cid'].'/dom">Deals Of The Month</a>';
	$page_output .= list_categories($_GET['ccid']);
  }
  
  if(!empty($_GET['ccid'])){
	
	// print advetriser listing if available
	require(MOB_CLASS.'advertListing.php');
	$advertListing = new advertListing;
	foreach($advertListing->list_row as $listItm){
		$page_output .= $listItm;
	}
	
	$ste_cty_cat_tbl->city_search($cities_tbl->id,$_GET['ccid']);
	$stes_tbl->get_db_vars($ste_cty_cat_tbl->state);
	// set page header -- only assign for static header data
	if ($ste_cty_cat_tbl->page_title != '') {
		$page_header_title = $ste_cty_cat_tbl->page_title;
	} else {
		if (strpos($_SERVER["REQUEST_URI"],'view-all-results') > 0) {
			$page_header_title = 'View All deals in '.$cities_tbl->city.' '.$stes_tbl->state.' ('.$stes_tbl->acn.') - Discounts, Coupons, Deals';
		} elseif(strpos($_SERVER["REQUEST_URI"],'deals-of-the-month') > 0) {
			$page_header_title = 'Deals of the month in '.$cities_tbl->city.' '.$stes_tbl->state.' ('.$stes_tbl->acn.') - Discounts, Coupons, Deals';
		} else {
			$page_header_title =  'Restaurant Coupons and Local Deals - '.$cats_tbl->category_name.' in '.$cities_tbl->city.' '.$stes_tbl->state.' ('.$stes_tbl->acn.')';
		}
	}
	
	if ($ste_cty_cat_tbl->meta_description != '') {
		$page_meta_description = $ste_cty_cat_tbl->meta_description;
	} else {
		$page_meta_description = DEF_PAGE_META_DESC;
	}
	
	if ($ste_cty_cat_tbl->meta_keywords != '') {
		$page_meta_keywords = $ste_cty_cat_tbl->meta_keywords;
	} else {
		$page_meta_keywords = DEF_PAGE_META_KEYWORDS;
	}
  } else {
	// set page header -- only assign for static header data
	if ($cities_tbl->page_title != '') {
		$page_header_title = $cities_tbl->page_title;
	} else {
		$page_header_title = $cities_tbl->city.' Restaurant Coupons and Local Deals | '.$cities_tbl->state;
	}
	
	// set header keywords
	if ($cities_tbl->meta_keywords != '') {
		$page_meta_keywords = $cities_tbl->meta_keywords;
	} else {
		$sql_query = "SELECT
						state
					 FROM
						states
					 WHERE
						acn = ?
					 LIMIT 1;";
	
		$values = array(
						$cities_tbl->state
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		$row_sel_city = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		$result->free();
		
		$page_meta_keywords = $cities_tbl->city.' '.$row_sel_city['state'].' ('.$cities_tbl->state.') local deals restaurants shopping coupons directory businesses';
	}
	
	if ($cities_tbl->meta_description != '') {
		$page_meta_description = $cities_tbl->meta_description;
	} else {
		$page_meta_description = 'Find discounts and coupon offers from local businesses within '.$cities_tbl->city.', '.$cities_tbl->state.'.';
	}
  }
} else {
  $page_header_title = 'CheapLocalDeals.com - Category Not Found';
  $page_meta_description = DEF_PAGE_META_DESC;
  $page_meta_keywords = DEF_PAGE_META_KEYWORDS;
}

$selTemp = 'pages.php';
$selHedMetTitle = $page_header_title;
$selHedMetDesc = $page_meta_description;
$selHedMetKW = $page_meta_keywords;
?>