<?PHP
// load application top
require('../../includes/application_top.php');

// build advertisers drop down
$sql_query = "SELECT
				id,
				city
			 FROM
				cities
			 WHERE
				state = '".$_POST['state']."'
			 ORDER BY
				city
			 ;";
$rows = $dbh->queryAll($sql_query);
	$advertiser_options .= '<option></option>';
foreach($rows as $advertiser) {
	$advertiser_options .= '<option value="'.$advertiser['city'].'" '.($_POST['city_select'] == $advertiser['city'] ? 'selected="selected"' : '').' >'.$advertiser['city'].'</option>';
}

$select_ajax = '<script type="text/javascript"> 
				jQuery(function(){
				 jQuery("#city_select_input").change(function () {
					var selected_state = jQuery("#state_select").val();
					var selected_city = jQuery("#city_select_input").val();
					
					 $.ajax({
					   type: "POST",
					   url: "ajax_calls/advertiser_select.deal",
					   data: "state="+selected_state+"&city="+selected_city,
					   success: function(msg){
						 jQuery(\'#advertiser_select_bx\').html(msg);
					   }
					 });
				 });
				}); 
				</script>'.LB;

echo $select_ajax.'<select name="city_select" id="city_select_input">'.$advertiser_options.'</select>';

?>
