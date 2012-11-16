<?PHP

global $pgs_tbl,$url_nms_tbl, $dbh, $stes_tbl, $url_nms_tbl, $geo_data;

// check for search friendly name if search friendly page has not been selected
// if url name id is found 301 redirect to search friendly page
if ($pgs_tbl->url_name != '' && strstr($_SERVER["REQUEST_URI"],'pages.deal')) {
	$url_nms_tbl->get_db_vars($pgs_tbl->url_name);
	header( "HTTP/1.1 301 Moved Permanently" ); 	
	header("Location: ".SITE_URL.$url_nms_tbl->url_name."/");
}


// check for set get var
if (!empty($_GET['pid'])) {
	
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
	
	// page output
	$page_output = ($pgs_tbl->display_name != 1 ? '<center><strong>'.$pgs_tbl->name.'</strong></center>' : '');
	$page_output .= $pgs_tbl->header_content;
	if($_SERVER["REQUEST_URI"] == '/404-error-page/') {
       $page_output .= '<table align="center">
            <tr>
              <td>
			  <div class="regular_list_head">
                  <div class="rlh_left_corner"></div>
                  <div class="rlh_right_corner"></div>
                  <div class="header_txt">Your Random '.$geo_data->city.', '.$geo_data->region.' Local Deals</div>
                </div>
                <div class="adv_listing_mid"></div>
                <table class="listItem">
                  <tr>
                    <td>';
	  require(CLASSES_DIR.'sections/random_advert.php');
	  $rand_lst_qry = new rand_lst_qry(2);
	  $page_output .= $rand_lst_qry->list_row[0];
      $page_output .= '</td>
                  </tr>
                </table>
                <div class="adv_listing_mid"></div>
                <table class="listItem">
                  <tr>
                    <td>';
	  $page_output .= $rand_lst_qry->list_row[1];
      $page_output .= '</td>
                  </tr>
                </table>
                <div class="adv_listing_mid"></div>
                <div class="regular_list_head">
                  <div class="rlh_left_bot_corner"></div>
                  <div class="rlh_right_bot_corner"></div>
                  <div class="header_txt">&nbsp;</div>
                </div><br />
                <script type="text/javascript">
                  //<![CDATA[
                    var requirementsArray = [];
                    '. $rand_lst_qry->sel_restriction_src.'
                  //]]>
                  </script></td>
            </tr>
          </table>';
    }
	$page_output .= $pgs_tbl->footer_content;
	
} else {

	// if page id value has not been set load header defaults and inform page has not been found
	$page_header_title = DEF_PAGE_HEADER_TITLE;
	$page_meta_description = DEF_PAGE_META_DESC;
	$page_meta_keywords = DEF_PAGE_META_KEYWORDS;

	$page_output = '<center><strong>Page Not Found</strong></center>';
}
	
$content_arr = array();
$content_arr['$page_output$'] = $page_output;
$this->template_constants = $content_arr;

// set page header -- only assign for static header data
$this->page_header_title = $page_header_title;
$this->page_meta_description = $page_meta_description;
$this->page_meta_keywords = $page_meta_keywords;
$this->template_file = 'blank-new.php';

?>