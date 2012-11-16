<?PHP

$page_output .= '<div class="newAdvert" id="frm6">
				<div class="regular_list_head">
					<div class="rlh_left_corner"></div>
                  <div class="rlh_right_corner"></div>
                  <div class="header_txt">Account Information</div>
                </div>
                <div class="adv_listing_mid"></div>
				<table border="0" cellspacing="0" cellpadding="0" class="rnd_advertiser_form" align="center">
				<tr><td colspan="2"><table align="center" width="450">';
$page_output .= '<tr><td>First Name*<br/> '.$form_write->input_text('first_name',$first_name,30,120,13,'').'</td><td>Username*<br/> '.$form_write->input_text('username',$username,30,120,17,'').'</td></tr>';	
$page_output .= '<tr><td>Last Name*<br/> '.$form_write->input_text('last_name',$last_name,30,120,15,'').'</td><td>Password* '.MINIMUM_PASSWORD_LENGTH.' character or more<br/> '.$form_write->input_password('password',$password,30,120,18,'').'</td></tr>';	
$page_output .= '<tr><td></td><td>Confirm Password* '.MINIMUM_PASSWORD_LENGTH.' character or more<br/> '.$form_write->input_password('confirm_password',$confirm_password,30,120,19,'').'</td></tr>';	
$page_output .= '<tr><td>Affiliate Code "If Applicable"<br/> '.$form_write->input_text('link_affiliate_code',$link_affiliate_code,30,120,16,'').'</td><td>Listing Image: (Please limit size to 150 x 120 and file type to jpg, gif, or png)<br/> '.$form_write->input_file('image',20,$id).'</td></tr>';	
$page_output .= '</table>
				</td></tr>';
$page_output .= '<tr><td>
				<center><strong>For questions regarding advertising please dial 1-866-283-6809</strong></center>
				<script type="text/javascript">
				function set_price(cert_id,price,target) {
					jQuery(target).val(price);
					var cert_radio = \'#cert_requirements\'+cert_id;
					jQuery(cert_radio).attr(\'checked\', \'checked\');
				}
				</script>
				</td></tr>
				</table>
				</div>';

?>