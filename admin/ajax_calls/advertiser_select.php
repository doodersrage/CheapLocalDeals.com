<?PHP
// load application top
require('../../includes/application_top.php');

// pull city zipcode list
$cities_tbl->city_state_search($_POST['city'],$_POST['state']);
$zip_cds_tbl->city_id = $cities_tbl->id;
$zip_array = $zip_cds_tbl->get_list();
$zip_array = implode(', ',$zip_array);

// build advertisers drop down
$sql_query = "SELECT
				id,
				company_name
			 FROM
				advertiser_info
			 WHERE
				zip IN (".$zip_array.")
			 ORDER BY
				company_name
			 ;";
$rows = $dbh->queryAll($sql_query);
	$advertiser_options .= '<option></option>';
foreach($rows as $advertiser) {
	$advertiser_options .= '<option value="'.$advertiser['id'].'" '.($_POST['advertiser_select'] == $advertiser['id'] ? 'selected="selected"' : '').' >'.$advertiser['company_name'].'</option>';
}
$select_ajax = '<script type="text/javascript"> 
				jQuery(function(){
				 jQuery("#advertiser_select").change(function () {
					var selected_advert = jQuery("#advertiser_select").val();
					
					 $.ajax({
					   type: "POST",
					   url: "ajax_calls/cert_amt_sel.deal",
					   data: "advert="+selected_advert,
					   success: function(msg){
						 jQuery(\'#cert_amounts\').html(msg);
					   }
					 });
				 });
				}); 
				</script>'.LB;

echo $select_ajax.'<select name="advertiser_select" id="advertiser_select">'.$advertiser_options.'</select>';



?>
