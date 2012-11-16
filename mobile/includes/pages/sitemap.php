<?PHP

global $stes_tbl, $url_nms_tbl, $category_results_pg, $dbh;

// assign previous url link
assign_previous_url_val();

// listing output
$page_output = '<div class="header_txt">Browse Deals By State</div>';
				
$page_output .= prntStates();

$page_output .= '<div class="header_txt">Other Pages</div>';
$page_output .= '<a class="linkBtn" href="'.MOB_URL.'?action=page&pid=11">About Cheap Local Deals</a>';
$page_output .= '<a class="linkBtn" href="'.MOB_URL.'?action=page&pid=2">Privacy Policy</a>';
$page_output .= '<a class="linkBtn" href="'.MOB_URL.'?action=contactUs">Contact Us</a>';
$page_output .= '<a class="linkBtn" href="'.MOB_URL.'customer_admin/create_account.deal">Customer Signup</a>';
$page_output .= '<a class="linkBtn" href="'.MOB_URL.'?action=userLogin">Customer Login</a>';
$page_output .= '<a class="linkBtn" href="'.MOB_URL.'new-advertiser/">Advertiser Signup</a>';
$page_output .= '<a class="linkBtn" href="'.MOB_URL.'advertiser_admin/advertiser_login.deal">Advertiser Login</a>';
$page_output .= '<a class="linkBtn" href="'.MOB_URL.'?action=states">Browse Listings By State</a>';

$page_header_title = 'CheapLocalDeals.com - Sitemap';
$page_meta_description = DEF_PAGE_META_DESC;
$page_meta_keywords = DEF_PAGE_META_KEYWORDS;

$selTemp = 'pages.php';
$selHedMetTitle = $page_header_title;
$selHedMetDesc = $page_meta_description;
$selHedMetKW = $page_meta_keywords;
?>