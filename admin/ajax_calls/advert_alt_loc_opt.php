<?PHP
// load application top
require('../../includes/application_top.php');

$retail_customer_form = '<select name="alt_select_id" size="12" id="alt_select_id">';
		
        $sql_query = "SELECT
						id,
						location_name
					 FROM
						advertiser_alt_locations
					 WHERE
                     	advertiser_id = '".$_POST['cid']."';";
		$rows = $dbh->queryAll($sql_query);
		foreach($rows as $cur_location) {
			$retail_customer_form .= '<option value="'.$cur_location['id'].'">'.$cur_location['location_name'].'</option>';
		}
    
$retail_customer_form .= '</select>';

echo $retail_customer_form;

?>