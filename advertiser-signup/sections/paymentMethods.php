<?PHP

$page_output .= '<div class="newAdvert" id="frm5">
				<div class="regular_list_head">
					<div class="rlh_left_corner"></div>
                  <div class="rlh_right_corner"></div>
                  <div class="header_txt">Accepted Payment Methods</div>
                </div>
                <div class="adv_listing_mid"></div>
				<table border="0" cellspacing="0" cellpadding="0" class="rnd_advertiser_form" align="center">
				<tr><td colspan="2"><table width="100%" id="slidebox2"><tr>
                <td align="left">';
// build payment method options
$payment_methods = $adv_pmt_mtds_tbl->get_all();
		$payment_method_sel = '';
		$payment_method_sel_op = '<script type="text/javascript">
jQuery(function(){
 jQuery(".rowclick td").css("cursor","pointer");
  jQuery(\'#rowclick2 td\').click(function(event) {
	jQuery(this).toggleClass(\'selected\');
	if (event.target.type !== \'checkbox\') {
	  jQuery(\':checkbox\', this).trigger(\'click\');
	}
  });
});
</script><table class="rowclick" id="rowclick2">';
		foreach($payment_methods as $value) {
			$payment_method_sel[] = '<td>'.$form_write->input_checkbox('payment_options['.$value['id'].']',1,$payment_options[$value['id']]).' '.$value['method'].'</td>';
			if (count($payment_method_sel) == 4) {
				$payment_method_sel_op .= '<tr>'.implode('',$payment_method_sel).'</tr>';
				$payment_method_sel = '';
			}
		}
		if (count($payment_method_sel) > 0) {
			$payment_method_sel_op .= '<tr>'.implode('',$payment_method_sel).'</tr>';
			$payment_method_sel = '';
		}
		$payment_method_sel_op .= '</table>';
		
$page_output .= $payment_method_sel_op;
$page_output .= '</td></tr></table>
				</td></tr></table>
				</div>';

?>