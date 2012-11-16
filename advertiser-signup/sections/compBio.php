<?PHP
$page_output .= '<div class="newAdvert" id="frm2">
				<div class="regular_list_head">
					<div class="rlh_left_corner"></div>
                  <div class="rlh_right_corner"></div>
                  <div class="header_txt">Company Bio</div>
                </div>
                <div class="adv_listing_mid"></div>
				<table border="0" cellspacing="0" cellpadding="0" class="rnd_advertiser_form" align="center">
				<tr><td colspan="2">
				
				<table align="center"><tr><td colspan="2" align="left">Description (Displayed on your information page.)<br/> '.$form_write->textarea('customer_description',$customer_description,4,80,11).'</td></tr>
				<tr><td colspan="2" align="left">Products and Services (Displayed within the listing and your information page.)<br/> '.$form_write->textarea('products_services',$products_services,4,80,12).'</td></tr></table>
				
				</td></tr>
				</table>
				</div>';
?>