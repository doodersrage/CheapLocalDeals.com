<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if advertiser not logged in redirect to login page
if ($_SESSION['advertiser_logged_in'] != 1) header("Location: ".SITE_SSL_URL."advertiser_login.php");

echo '<table class="advertiser_form"><tr><td>';

// load certificate data
$adv_info_tbl->get_db_vars($_SESSION['advertiser_id']);
$certificate_levels = $adv_info_tbl->certificate_levels;
$requirement_text = $adv_info_tbl->certificate_requirements;

$page_output = '
<tr><td><center><strong><span class="red">Warning: Altering your certificate settings will disable your account from our listing until we have had the time to review your changes.</span></strong></center></td></tr>';
$page_output .= '<tr><th width="100%"><div class="advertiser_header_sec">Certificate Amounts</div></th></tr>
				<tr><td colspan="2">
				<form name="update_certificate_settings" id="update_certificate_settings"><input name="form_submit" type="hidden" value="1">
				<script type="text/javascript">
jQuery(function(){

jQuery("#slidebox3head").css("cursor","pointer");

jQuery(\'#slidebox3head\').click(function() {
        var id = jQuery(this).attr(\'id\');
		
		if (jQuery(\'a.slide3\').text() == \'Expand\') {
			jQuery(\'a.slide3\').text(\'Collapse\');
			jQuery(\'#slidebox3\').fadeIn("slow");
		} else {
			jQuery(\'a.slide3\').text(\'Expand\');
			jQuery(\'#slidebox3\').fadeOut("slow");
		}
		
        // alert(id);
     return false;
     });

}); 
</script>';
		$certificate_amounts = $cert_amt_tbl->get_certificate_amounts();
		
		$cert_amt = '<script type="text/javascript" src="includes/libs/jquery.form-defaults.js"></script><table align="center" id="slidebox3" class="certificate_amounts_tbl"><tr><th align="center" colspan="2">Gift Certificate Value</th><th>Requirements for use or restrictions</th></tr>';
		
		foreach($certificate_amounts as $value) {
		  
		  $valid_purchase = 'Enter Products(s) required to purchase for $'.$value['discount_amount'].' discount here';
		  $valid_minimum = 'Enter minimum spend required for $'.$value['discount_amount'].' discount here';
		  $valid_with = 'Other: If neither of the above can be used, please enter requirements for use of $'.$value['discount_amount'].' Discount here';
		  $excludes_def = 'enter, IF ANY, exclusions for certificate use here';
  
		  
		  $requirements = '<script type="text/javascript">
  jQuery(function(){
  jQuery("#requirement_texta'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$valid_purchase.'");
  jQuery("#requirement_textb'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$valid_minimum.'");
  jQuery("#requirement_textd'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$excludes_def.'");
  jQuery("#requirement_textc'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$valid_with.'");
  });
  </script>
  <script type="text/javascript">
  jQuery(function(){
  jQuery(".certificate_amount_requirements td").css("cursor","pointer");
  jQuery(\'#certificate_amount_requirements'.$value['id'].' td\').click(function(event) {
  if (event.target.type !== \'radio\') {
	jQuery(\'.cert_requirements'.$value['id'].':radio\', this).trigger(\'click\');
  }
  });
  
  jQuery(\'#certificate_levels'.$value['id'].'\').click(function(event) {
  if (jQuery(\'#certificate_amount_requirements'.$value['id'].'\').css("display") == \'none\') {
	jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeIn("slow");
  } else {
	jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeOut("slow");
  }
  });
  
  });
  </script>
  <script type="text/javascript">
  function set_price(price,target) {
  jQuery(target).val(price);
  }
  </script>
  <table class="certificate_amount_requirements" ' .($certificate_levels[$value['id']] == 1 ? '' : 'style="display:none;"' ) . ' id="certificate_amount_requirements'.$value['id'].'" >';
  
		  $requirements .= '<tr><td valign="top" align="left">Opt 1: 
		  <input class="cert_requirements'.$value['id'].'" id="cert_requirements'.$value['id'].'" name="cert_requirements['.$value['id'].']" type="radio" value="2" '.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 2 ? 'checked ' : '' : '').'/>
		  <font color="red">Valid With</font> Min Spend Of:';
		  
		  $min_spend_opts = explode(',',$value['min_spend_amts']);
		  foreach($min_spend_opts as $cur_spend_val) {
			  $requirements .= ' <input onclick="set_price('.$cur_spend_val.',\'#requirement_textb'.strtolower(numtoalpha($value['id'])).'\');" name="requirement_text['.$value['id'].'][min_spend]" type="radio" value="'.$cur_spend_val.'" /> $'.$cur_spend_val;
		  }
		  
		  $requirements .= ' Other $<input id="requirement_textb'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][2]" type="text" value="'.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 2 ? $adv_info_tbl->certificate_requirements[$value['id']]['value'] : '' : '').'"  maxlength="90" size="8" />
		  </td></tr>';
  
  // old min spend input
  //			$requirements .= '<tr><td valign="top" align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="2" '.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 2 ? 'checked ' : '' : '').'/> <font color="red">Valid With</font> Minimum Spend Of $<input id="requirement_textb'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][2]" type="text" value="'.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 2 ? $requirement_text[$value['id']][2] : '' : '').'"  maxlength="90" size="43" /></td></tr>';
		  
		  $requirements .= '<tr><td valign="top" align="left">Opt 2: <input class="cert_requirements'.$value['id'].'" id="cert_requirements'.$value['id'].'" name="cert_requirements['.$value['id'].']" type="radio" value="1" '.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 1 ? 'checked ' : '' : '').' /> <font color="red">Valid Towards</font>: <input id="requirement_texta'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][1]" type="text" value="'.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 1 ? $adv_info_tbl->certificate_requirements[$value['id']]['value'] : '' : '').'"  maxlength="90" size="40" /></td></tr>';
		  
  //			$requirements .= '<tr><td valign="top" align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="3" '.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 3 ? 'checked ' : '' : '').'/> <font color="red">Valid With</font> <textarea id="requirement_textc'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][3]" cols="48" rows="2">'.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 3 ? $requirement_text[$value['id']][3] : '' : '').'</textarea></td></tr>';
		  
		  $requirements .= '<tr><td valign="top" align="left">Opt 3: <input class="cert_requirements'.$value['id'].'" id="cert_requirements'.$value['id'].'" name="cert_requirements['.$value['id'].']" type="radio" value="4" '.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 4 ? 'checked ' : '' : '').'/>No Requirements<input name="requirement_text['.$value['id'].'][4]" type="hidden" value="" /></td></tr>';
		  
		  $requirements .= '<tr><td valign="top" align="left">Excludes: 
		  <input id="requirement_textd'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][excludes]" type="text" value="'.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['excludes'] : '').'"  maxlength="90" size="47" />
		  </td></tr>';
  
  //			$requirements .= '<tr><td align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="1" '.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 1 ? 'checked ' : '' : '').' /> <font color="red">Valid With</font> Purchase Of ... <input id="requirement_texta'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][1]" type="text" value="'.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 1 ? $adv_info_tbl->certificate_requirements[$value['id']]['value'] : '' : '').'"  maxlength="90" size="47" /></td></tr>';
  //			
  //			$requirements .= '<tr><td align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="2" '.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 2 ? 'checked ' : '' : '').'/> <font color="red">Valid With</font> Minimum Spend Of $<input id="requirement_textb'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][2]" type="text" value="'.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 2 ? $adv_info_tbl->certificate_requirements[$value['id']]['value'] : '' : '').'"  maxlength="90" size="43" /></td></tr>';
  //			
  //			$requirements .= '<tr><td align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="3" '.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 3 ? 'checked ' : '' : '').'/> <font color="red">Valid With</font> <textarea id="requirement_textc'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][3]" cols="48" rows="2">'.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 3 ? $adv_info_tbl->certificate_requirements[$value['id']]['value'] : '' : '').'</textarea></td></tr>';
  //			
  //			$requirements .= '<tr><td align="left"><input name="cert_requirements['.$value['id'].']" id="cert_requirements'.$value['id'].'" type="radio" value="4" '.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 4 ? 'checked ' : '' : '').'/>No Requirements<input name="requirement_text['.$value['id'].'][4]" type="hidden" value="" /></td></tr>';
  
		  $requirements .= '</table>';
		  
		  $cert_amt .= '<tr><td align="right" valign="top">$'.$value['discount_amount'].':</td><td valign="top"> <input name="certificate_levels['.$value['id'].']" id="certificate_levels'.$value['id'].'" type="checkbox" value="1" '.($certificate_levels[$value['id']] == 1 ? 'checked ' : '').'/></td><td>'.$requirements.'</td></tr>'.LB; 
		}
		$cert_amt .= '</table>';
				
$page_output .= $cert_amt;
$page_output .= '</form></td></tr><tr><td align="center" colspan="2"><input class="submit_btn" name="Submit" type="button" onclick="update_certificate_settings_proc()" value="Submit"></td></tr></table>';
$page_output .= '</td></tr></table>';
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;

echo '</td></tr></table>';
?>