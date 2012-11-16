<?PHP

$page_output .= '<div class="newAdvert" id="frm4">
				<div class="regular_list_head">
					<div class="rlh_left_corner"></div>
                  <div class="rlh_right_corner"></div>
                  <div class="header_txt">Hours of Operation </div>
                </div>
                <div class="adv_listing_mid"></div>
				<table border="0" cellspacing="0" cellpadding="0" class="rnd_advertiser_form" align="center">
				<tr><td colspan="2"><script type="text/javascript">
jQuery(function(){
  
  jQuery(\'#select_hours\').click(function(event) {
	if (jQuery(\'#hours_of_operation_tbl\').css("display") == \'none\') {
	  jQuery(\'#hours_of_operation_tbl\').css("display","block");
	}
  });
  
  jQuery(\'.other_oper\').click(function(event) {
	if (jQuery(\'#hours_of_operation_tbl\').css("display") == \'block\') {
	  jQuery(\'#hours_of_operation_tbl\').css("display","none");
	}
  });

  jQuery(\'#hours_of_operation_tbl\').css("display","none");

}); 
</script><table width="100%" id="slidebox"><tr>
                <td align="left">';

// builds hours of operation selection
		$hours_list = unserialize(HOURS_SELECT);
		$days_array = unserialize(DAYS_ARRAY);
		if(!isset($hours_of_operation)) {
			// if hours of operation settings do not exist print HOP form
			$hours_of_operation_frm = $form_write->input_radio('hours_of_operation[selected][type]','nohours','nohours','','other_oper').' Do Not Display Hours<br/>';
			$hours_of_operation_frm .= $form_write->input_radio('hours_of_operation[selected][type]','24hr','','','other_oper').' Open 24 Hours<br/>';
			$hours_of_operation_frm .= $form_write->input_radio('hours_of_operation[selected][type]','select','','select_hours').' Select Hours<br/>';
			$hours_of_operation_frm .= '<table class="hours_of_operation_tbl" id="hours_of_operation_tbl">';
			$hours_of_operation_frm .= '<tr>';
			reset($days_array);
			foreach($days_array as $value) {
				$hours_of_operation_frm .= '<th>'.$value.'</th>';
			}
			$hours_of_operation_frm .= '</tr><tr>';
			reset($days_array);
			reset($hours_list);
			// draw days selection
			foreach($days_array as $day_value) {
				$hours_of_operation_frm .= '<td>';
				$top_hrs_arr = array();
				foreach($hours_list as $value) {
					$top_hrs_arr[] = '<option '.('9:00 AM' == $value && $day_value != 'Saturday' && $day_value != 'Sunday' ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation_frm .= $form_write->select_dd('hours_of_operation[selected]['.$day_value.'open]',$top_hrs_arr);
				$hours_of_operation_frm .= '<br/>';
				$hours_of_operation_frm .= 'to<br/>';
				$top_hrs_arr = array();
				foreach($hours_list as $value) {
					$top_hrs_arr[] = '<option '.('5:00 PM' == $value && $day_value != 'Saturday' && $day_value != 'Sunday' ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation_frm .= $form_write->select_dd('hours_of_operation[selected]['.$day_value.'close]',$top_hrs_arr);
				$hours_of_operation_frm .= '<br/>';
				$hours_of_operation_frm .= '</td>';
			}
			$hours_of_operation_frm .= '</tr></table>';
		} else {
			// if HOP values are set load values into form
			$hours_of_operation_frm = $form_write->input_radio('hours_of_operation[selected][type]','nohours',$hours_of_operation['selected']['type'],'','other_oper').' Do Not Display Hours<br/>';
			$hours_of_operation_frm .= $form_write->input_radio('hours_of_operation[selected][type]','24hr',$hours_of_operation['selected']['type'],'','other_oper').' Open 24 Hours<br/>';
			$hours_of_operation_frm .= $form_write->input_radio('hours_of_operation[selected][type]','select',$hours_of_operation['selected']['type'],'select_hours').' Select Hours<br/>';
			$hours_of_operation_frm .= '<table class="hours_of_operation_tbl" id="hours_of_operation_tbl">';
			$hours_of_operation_frm .= '<tr>';
			reset($days_array);
			foreach($days_array as $value) {
				$hours_of_operation_frm .= '<th>'.$value.'</th>';
			}
			$hours_of_operation_frm .= '</tr><tr>';
			reset($days_array);
			reset($hours_list);
			// draw days selection
			foreach($days_array as $day_value) {
				$hours_of_operation_frm .= '<td>';
				$top_hrs_arr = array();
				foreach($hours_list as $value) {
					$top_hrs_arr[] = '<option '.($hours_of_operation['selected'][$day_value.'open'] == $value ? 'selected="selected"' : '9:00 AM' == $value && $day_value != 'Saturday' && $day_value != 'Sunday' ? 'selected="selected"' : '' ).' >'.$value.'</option>';
				}
				$hours_of_operation_frm .= $form_write->select_dd('hours_of_operation[selected]['.$day_value.'open]',$top_hrs_arr);
				$hours_of_operation_frm .= '<br/>';
				$hours_of_operation_frm .= 'to<br/>';
				$top_hrs_arr = array();
				foreach($hours_list as $value) {
					$top_hrs_arr[] = '<option '.($hours_of_operation['selected'][$day_value.'close'] == $value ? 'selected="selected"' : $value == '5:00 PM' && $day_value != 'Saturday' && $day_value != 'Sunday' ? 'selected="selected"' : '' ).' >'.$value.'</option>';
				}
				$hours_of_operation_frm .= $form_write->select_dd('hours_of_operation[selected]['.$day_value.'close]',$top_hrs_arr);
				$hours_of_operation_frm .= '<br/>';
				$hours_of_operation_frm .= '</td>';
			}
			$hours_of_operation_frm .= '</tr></table>';			
		}
				
$page_output .= $hours_of_operation_frm;
$page_output .= '</td></tr></table>
				</td></tr></table>
				</div>';

?>