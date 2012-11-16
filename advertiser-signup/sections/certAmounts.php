<?PHP

$page_output .= '<div class="newAdvert" id="frm6">
				<div class="regular_list_head">
					<div class="rlh_left_corner"></div>
                  <div class="rlh_right_corner"></div>
                  <div class="header_txt">Certificate Amounts</div>
                </div>
                <div class="adv_listing_mid"></div>
				<table border="0" cellspacing="0" cellpadding="0" class="rnd_advertiser_form" align="center">
				<tr><td colspan="2">';
		$certificate_amounts = $cert_amt_tbl->get_certificate_amounts();
		
		$cert_amt = '<script type="text/javascript" src="includes/libs/jquery.form-defaults.js"></script><table width="100%" align="center" id="slidebox3" class="certificate_amounts_tbl">
		<tr><td colspan="3">
		<table width="100%" border="0" cellspacing="10" cellpadding="0">
		<tr><td align="center"><a id="cert_req_link" href="includes/popups/advert_cert_agree_popup.deal?popupwindow" class="popupwindow" rel="height:410,width:750,toolbar:0,scrollbars:1,status:0,resizable:0,left:150,top:100"><img src="images/cert-requirements.gif" border="0"></a></td><td align="center"><a id="cert_req_link" href="pdf/test_cert.pdf?popupwindow" class="popupwindow" rel="height:550,width:750,toolbar:0,scrollbars:1,status:1,resizable:0,left:150,top:100"><img src="images/example-cert.png" border="0"></a></td></table></td></tr>
		<tr><td colspan="3" align="left"><p><strong><font color="red">IMPORTANT!</font></strong>: Read this before proceeding.<br>
Please select the certificate(s) that you will offer, how much the consumer must spend to use OR what they can be used towards. Finally, List exclusions, if any. </p>

<p>NOTE: All certificates have the following requirements preprinted on them (please do not list them in the Exclusions below). Certificates are not Valid with any other offer or promotion. Certificate has no cash back value. Certificate cannot be used towards outstanding balances, tips etc.. Limit one certificate redemption per visit. To view additional preprinted restrictions and a sample certificate, please <a id="cert_req_link" href="pdf/test_cert.pdf?popupwindow" class="popupwindow" rel="height:550,width:750,toolbar:0,scrollbars:1,status:1,resizable:0,left:150,top:100">click here</a>.</p>
</td></tr>';
//		$cert_amt = '<tr><td colspan="3" align="left">
//		<table width="100%"><th align="center" colspan="2" width="155">Coupon Value</th><th width="669">Requirements for use or restrictions</th></tr>';
//		
//		foreach($certificate_amounts as $value) {
//			
//			$valid_purchase = 'Enter Products(s) required to purchase for $'.$value['discount_amount'].' discount here';
////			$valid_minimum = 'Enter minimum spend required for $'.$value['discount_amount'].' discount here';
//			$valid_with = 'Other: If neither of the above can be used, please enter requirements for use of $'.$value['discount_amount'].' Discount here';
//			$excludes_def = 'enter, IF ANY, exclusions for certificate use here';
//			
//			$requirements = '<script type="text/javascript">
//jQuery(function(){
//jQuery("#requirement_texta'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$valid_purchase.'");';
////$requirements .= 'jQuery("#requirement_textb'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$valid_minimum.'");';
//$requirements .= 'jQuery("#requirement_textd'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$excludes_def.'");';
//$requirements .= 'jQuery("#requirement_textc'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$valid_with.'");
//});
// </script>
//<script type="text/javascript">
//jQuery(function(){
// jQuery(".certificate_amount_requirements td").css("cursor","pointer");
//  jQuery(\'#certificate_amount_requirements'.$value['id'].' td\').click(function(event) {
//	if (event.target.type !== \'radio\') {
//	  jQuery(\'.cert_requirements'.$value['id'].':radio\', this).trigger(\'click\');
//	}
//  });
//  
//  jQuery(\'#certificate_levels'.$value['id'].'\').click(function(event) {
//	if (jQuery(\'#certificate_amount_requirements'.$value['id'].'\').css("display") == \'none\') {
//	  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeIn("slow");
//	} else {
//	  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeOut("slow");
//	}
//  });
//
//  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').css("display","none");
//  
//});
//</script><table width="100%" class="certificate_amount_requirements" id="certificate_amount_requirements'.$value['id'].'">';
//			
//			$requirements .= '<tr><td valign="top" align="left">Opt 1:
//			'.$form_write->input_radio('cert_requirements['.$value['id'].']',2,(!empty($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] : ''),'cert_requirements'.$value['id'],'cert_requirements'.$value['id']).' <font color="red">Valid With</font> Min Spend Of:';
//			
//			$min_spend_opts = explode(',',$value['min_spend_amts']);
//			foreach($min_spend_opts as $cur_spend_val) {
//				$requirements .= ' <input onclick="set_price('.$value['id'].','.$cur_spend_val.',\'#requirement_textb'.strtolower(numtoalpha($value['id'])).'\');" name="requirement_text['.$value['id'].'][min_spend]" type="radio" value="'.$cur_spend_val.'" '.($requirement_text[$value['id']][2] == $cur_spend_val ? 'checked' : '').' /> $'.$cur_spend_val;
//			}
//			
//			$requirements .= ' Other $'.$form_write->input_text('requirement_text['.$value['id'].'][2]',$requirement_text[$value['id']][2],2,10,'','requirement_textb'.strtolower(numtoalpha($value['id']))).'
//			</td></tr>';
//
//// old min spend input
////			$requirements .= '<tr><td valign="top" align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="2" '.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 2 ? 'checked ' : '' : '').'/> <font color="red">Valid With</font> Minimum Spend Of $<input id="requirement_textb'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][2]" type="text" value="'.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 2 ? $requirement_text[$value['id']][2] : '' : '').'"  maxlength="90" size="43" /></td></tr>';
//			
//			$requirements .= '<tr><td valign="top" align="left">Opt 2: '.$form_write->input_radio('cert_requirements['.$value['id'].']',1,$cert_requirements[$value['id']],'','cert_requirements'.$value['id']).' <font color="red">Valid Towards</font> : '.$form_write->input_text('requirement_text['.$value['id'].'][1]',(!empty($requirement_text[$value['id']][1]) ? $requirement_text[$value['id']][1] : ''),40,90,'','requirement_texta'.strtolower(numtoalpha($value['id']))).'</td></tr>';
//			
////			$requirements .= '<tr><td valign="top" align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="3" '.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 3 ? 'checked ' : '' : '').'/> <font color="red">Valid With</font> <textarea id="requirement_textc'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][3]" cols="48" rows="2">'.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 3 ? $requirement_text[$value['id']][3] : '' : '').'</textarea></td></tr>';
//			
//			$requirements .= '<tr><td valign="top" align="left">Opt 3: '.$form_write->input_radio('cert_requirements['.$value['id'].']',4,(!empty($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] : ''),'','cert_requirements'.$value['id']).'No Requirements'.$form_write->input_hidden('requirement_text['.$value['id'].'][4]','').'</td></tr>';
//			
//			$requirements .= '<tr><td valign="top" align="center">Excludes: '.$form_write->input_text('requirement_text['.$value['id'].'][excludes]',$requirement_text[$value['id']]['excludes'],47,90,'','requirement_textd'.strtolower(numtoalpha($value['id']))).'</td></tr>';
//
//			$requirements .= '</table>';
//			
//			$cert_amt .= '<tr><td align="right" valign="top">$'.$value['discount_amount'].':</td><td valign="top"> '.$form_write->input_checkbox('certificate_levels['.$value['id'].']',1,(!empty($certificate_levels[$value['id']]) ? $certificate_levels[$value['id']] : ''),'certificate_levels'.$value['id']).'</td><td>'.$requirements.'</td></tr>'.LB; 
//			
//		}
//		$cert_amt .= '</table>
//					<script type="text/javascript">
//					  jQuery(function(){
//						jQuery(\'#certificate_levels1\').attr(\'checked\', \'checked\');
//						jQuery(\'#certificate_amount_requirements1\').toggle();
//					  });
//					</script>';
				
$page_output .= $cert_amt;
$page_output .= '</td></tr></table>';
$page_output .= '</td></tr></table>
				</div>';

?>