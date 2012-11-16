<?PHP
// load application top
require('../../includes/application_top.php');

// pull city zipcode list
$adv_info_tbl->get_db_vars($_POST['advert']);
$avail_cert_lvls = $adv_info_tbl->certificate_levels;

// build certificate amount drop down
	$certificate_options = '<option></option>';
foreach($avail_cert_lvls as $id => $value) {
  
  $cert_amt_tbl->get_db_vars($id);
  
  $certificate_options .= '<option value="'.$cert_amt_tbl->id.'" '.($_POST['certificate_select'] == $cert_amt_tbl->id ? 'selected="selected"' : '').' >'.$cert_amt_tbl->discount_amount.' for '.$cert_amt_tbl->cost.'</option>';
  
}

$select_ajax = '<script type="text/javascript"> 
				jQuery(function(){
				 jQuery("#certificate_select").change(function () {
					var selected_advert = jQuery("#advertiser_select").val();
					var selected_cert = jQuery("#certificate_select").val();
					
					 $.ajax({
					   type: "POST",
					   url: "ajax_calls/cert_req_pull.deal",
					   data: "advert="+selected_advert+"&cert="+selected_cert,
					   success: function(msg){
						 jQuery(\'#certificate_requirements\').val(msg);
					   }
					 });
					 
					 $.ajax({
					   type: "POST",
					   url: "ajax_calls/cert_exclu_pull.deal",
					   data: "advert="+selected_advert+"&cert="+selected_cert,
					   success: function(msg){
						 jQuery(\'#certificate_excludes\').val(msg);
					   }
					 });
					 
				 });
				}); 
				</script>'.LB;

echo $select_ajax.'<select name="certificate_select" id="certificate_select">'.$certificate_options.'</select>';



?>
