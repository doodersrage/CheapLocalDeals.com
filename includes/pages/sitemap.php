<?PHP

global $stes_tbl, $url_nms_tbl, $category_results_pg, $dbh;

// draw page output
$page_output = '<center><strong>Sitemap</strong></center>';
$page_output .= '<table class="ste_box" align="center"><tr><td>';
$page_output .= '<table><tr><td>';

// pull states info from states table
$sql_query = "SELECT
				id
			 FROM
				states
			 ;";
$state_list = db_memc_str($sql_query);

// listing output
	$state_listings .= '<div class="regular_list_head">
					<div class="rlh_left_corner"></div>
                  <div class="rlh_right_corner"></div>
                  <div class="header_txt">Browse Listings By State</div>
                </div>
                <div class="adv_listing_mid"></div>
				<table border="0" align="center" class="rnd_advertiser_form">';
	$state_listings .= '<tr><td align="left"><table width="100%"><tr><td valign="top" align="left">
					<ul class="state_list">'.LB;
					
	// get state list count
	$state_cnt = count($state_list);
	// state cnt half
	$state_cnt_half = ceil($state_cnt/4);
	
	// reset cur state count val
	$cur_state_cnt = 0;
	
	// jump through each state
	foreach($state_list as $sel_state) {
	
		// pull current state info
		$stes_tbl->get_db_vars($sel_state['id']);
		$id = $stes_tbl->id;
		$acn = $stes_tbl->acn;
		$cur_state = $stes_tbl->state;
		
		// check for search friendly url name entry
		if($stes_tbl->url_name > 0) {
			$url_nms_tbl->get_db_vars($stes_tbl->url_name);
			$link_name = $url_nms_tbl->url_name.'/';
		} else {
			$link_name = 'state-browse/?state='.$sel_state['id'];
		}
	
		// print city list if state is selected
		if (empty($_GET['state']) || $id == $_GET['state']) {
					
		$state_listings .= '<li><a '.(!empty($_GET['state']) ? 'class="state_link"' : '').' href="'.SITE_URL.$link_name.'">'.$cur_state.' Deals</a>'.LB;
		
		if(!empty($_GET['state']) && $_GET['state'] == $id) {
			// print header data
			if($stes_tbl->page_title != '' || $stes_tbl->page_title != NULL) {
				$page_header_title = $stes_tbl->page_title;
			} else {
				$page_header_title = 'Find Deals By State '.$cur_state.' - CheapLocalDeals.com';
			}
			if($stes_tbl->meta_description != '' || $stes_tbl->meta_description != NULL) {
				$page_header_title = $stes_tbl->meta_description;
			} else {
				$page_meta_description = 'Find local deals in your area by state. '.$cur_state;
			}
			if($stes_tbl->meta_description != '' || $stes_tbl->meta_description != NULL) {
				$page_header_title = $stes_tbl->meta_keywords;
			} else {
				$page_meta_keywords = 'local deals by state,select state to find local deals,local deals,state selected local deals'.$cur_state;
			}
			
			// assign header and footer content
			$page_header = $stes_tbl->page_header;
			$page_footer = $stes_tbl->page_footer;
			
		}
		
		// update cur state count val
		$cur_state_cnt++;
		
		$state_listings .= '</li>'.LB;
		
		// check for current state position
		if ($cur_state_cnt == $state_cnt_half) {
			$state_listings .= '</ul></td><td valign="top" align="left"><ul class="state_list">';
			$cur_state_cnt = 0;
		}
		
		}
	}
	
	$state_listings .= '</ul>
					</td>
					</tr>
					</table>
					</td>
					</tr>';
	$state_listings .= '</table>'.LB;

// build page
$page_output .= '<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
		  <td>'.$page_header.'</td>
		</tr>
	  </table>';
$page_output .= $state_listings;
$page_output .= '<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
		  <td>'.$page_footer.'</td>
		</tr>
	  </table>';

$page_output .= '</td></tr></table>';
$page_output .= '</td></tr><tr><td valign="top">';
$page_output .= '<div class="regular_list_head">
					<div class="rlh_left_corner"></div>
                  <div class="rlh_right_corner"></div>
                  <div class="header_txt">Other Pages</div>
                </div>
                <div class="adv_listing_mid"></div>
				<table border="0" align="center" class="rnd_advertiser_form">';
$page_output .= '<tr><td align="left"><table width="100%"><tr><td valign="top" align="left">'.LB;
$page_output .= '<a href="'.SITE_URL.'about-cheap-local-deals/">About Cheap Local Deals</a><br/>';
$page_output .= '<a href="'.SITE_URL.'privacy-policy/">Privacy Policy</a><br/>';
$page_output .= '<a href="'.SITE_URL.'contact_us.deal">Contact Us</a><br/>';
$page_output .= '<a href="'.SITE_SSL_URL.'customer_admin/create_account.deal">Customer Signup</a><br/>';
$page_output .= '<a href="'.SITE_SSL_URL.'customer_admin/account_login.deal">Customer Login</a><br/>';
$page_output .= '<a href="'.SITE_URL.'new-advertiser/">Advertiser Signup</a><br/>';
$page_output .= '<a href="'.SITE_SSL_URL.'advertiser_admin/advertiser_login.deal">Advertiser Login</a><br/>';
$page_output .= '<a href="'.SITE_URL.'state-browse/">Browse Listings By State</a><br/>';
$page_output .= '</td></tr></table>';
$page_output .= '</td></tr></table>';
$page_output .= '</td></tr></table>'; 

$content_arr = array();
$content_arr['$page_output$'] = $page_output;
$this->template_constants = $content_arr;

$page_header_title = 'CheapLocalDeals.com - Sitemap';
$page_meta_description = DEF_PAGE_META_DESC;
$page_meta_keywords = DEF_PAGE_META_KEYWORDS;

// set page header -- only assign for static header data
$this->page_header_title = $page_header_title;
$this->page_meta_description = $page_meta_description;
$this->page_meta_keywords = $page_meta_keywords;
$this->template_file = 'blank-new.php';

?>