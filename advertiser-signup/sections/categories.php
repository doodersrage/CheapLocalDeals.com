<?PHP

require(CLASSES_DIR.'sections/category_select.php');
$category_select_pg = new category_select_pg;

$page_output .= '<div class="newAdvert" id="frm3">
				<div class="regular_list_head">
					<div class="rlh_left_corner"></div>
                  <div class="rlh_right_corner"></div>
                  <div class="header_txt">Categories</div>
                </div>
                <div class="adv_listing_mid"></div>
				<table border="0" cellspacing="0" cellpadding="0" class="rnd_advertiser_form" align="center">';
$page_output .= '<tr><td colspan="2"><table id="slidebox1" width="100%"><tr><td>';
$page_output .= $category_select_pg->list_categories();
$page_output .= '</td></tr></table></td></tr></table>
				</div>';

?>