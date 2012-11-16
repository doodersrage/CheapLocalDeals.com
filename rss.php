<?PHP
header("Content-type: text/xml");

// load application header
require('includes/application_top.php');

// set radius data
if(empty($_SESSION['set_radius'])) {
  $_SESSION['set_radius'] = DEF_MIN_RADIUS;
} 

if (!empty($_GET['zip'])) {
  $_SESSION['cur_zip'] = $_GET['zip'];
  $set_zip = $_SESSION['cur_zip'];
  $zip_cds_tbl->search($set_zip);
  $cities_tbl->get_db_vars($zip_cds_tbl->city_id);
  $_GET['city'] = $cities_tbl->id;
}
if (!empty($_GET['city'])) { $_SESSION['city'] = (int)$_GET['city']; } else { $_SESSION['city']=$geo_data->cityid;} 

  // set city values
  $cities_tbl->get_db_vars($_SESSION['city']);
  // pull city zipcode list
  $zip_cds_tbl->city_id = $cities_tbl->id;
  $zip_array = $zip_cds_tbl->get_list();
  $zip_array = $zip_cds_tbl->fetchZipsInRadiusByZip($zip_array[0], $_SESSION['set_radius'], 100);
  $city_str = $cities_tbl->city;
  $state_str = $cities_tbl->state;

echo '<?xml version="1.0"?>
<rss version="2.0">
<channel>

<title>Cheap Local Deals Listings Feed</title>
<link>'.SITE_URL.'</link>
<description>'.$city_str.', '.$state_str.' local listing of great deals.</description>
<language>en-us</language>'.LB;

$zip_string = implode(', ',$zip_array);

$sql_query = "SELECT
				ci.id as customers_id
			 FROM
			  advertiser_info ci 
			WHERE
			 ci.zip IN (".$zip_string.") 
			  AND ci.account_enabled = 1 
			  AND ci.approved = 1 
			  AND ci.update_approval = 1
			ORDER BY 
			  ci.date_created DESC
			LIMIT 20;";
$rows = $dbh->queryAll($sql_query);

// run through all advertisers
foreach($rows as $advertiser) {
	$adv_info_tbl->get_db_vars($advertiser['customers_id']);		

$title = str_replace("<br />", "n", $adv_info_tbl->company_name);
$title = htmlentities($title);
$title = str_replace("&", "&amp;", $title);

$location_image = 'customers/'.$adv_info_tbl->image;

echo '<item>
<title>'.$title.'</title>
<link>'.SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/</link>
<description>';

// start output buffer
ob_start();

echo '<![CDATA[<img src="'.SITE_URL.'includes/resize_image.deal?image='.$location_image.'&new_width=150&new_height=120" />
'."<br>";

if ($adv_info_tbl->hide_address != 1) {
		echo "<br>".$adv_info_tbl->address_1."<br>".($adv_info_tbl->address_2 != '' ? $adv_info_tbl->address_2."<br>" : '').'
	'.$adv_info_tbl->city.', '.$adv_info_tbl->state.' '.$adv_info_tbl->zip."<br>".$adv_info_tbl->phone_number."<br>"; 
}
if (count($adv_info_tbl->payment_options) > 0) {
echo '<p><b>Payment Methods:</b></p>
';
// build payment method options
$payment_methods = $adv_pmt_mtds_tbl->get_all();
	$payment_method_sel = '';
	$payment_method_sel_op = '<table class="payment_methods">';
	foreach($payment_methods as $id => $value) {
		if(isset($adv_info_tbl->payment_options[$value['id']])) {
			if ($adv_info_tbl->payment_options[$value['id']] == 1) {
				$payment_method_sel[] = '<td>'.$value['method'].'</td>';
				if (count($payment_method_sel) == 2) {
					$payment_method_sel_op .= '<tr>'.implode('',$payment_method_sel).'</tr>';
					$payment_method_sel = '';
				}
			}
		}
	}
	if ($payment_method_sel != '') {
		$payment_method_sel_op .= '<tr>'.implode('',$payment_method_sel).'</tr>';
		$payment_method_sel = '';
	}
	$payment_method_sel_op .= '</table>';
	echo $payment_method_sel_op.'';
}

echo '<br><b>Hours of Operation:</b><br/>
                  <font size="1">';

$hours_of_operation = '';

// if HOP values are set load values into form
$hours_operation = $adv_info_tbl->hours_operation;

switch($hours_operation['selected']['type']) {
case '24hr':
	echo 'Open 24 hours';
break;
case 'select':
	$days_array = unserialize(DAYS_ARRAY);
	$hours_of_operation .= '<table class="hours_operation_tbl">';
	$hours_of_operation .= '<tr>';
	reset($days_array);
	foreach($days_array as $value) {
		$hours_of_operation .= '<th>'.$value.'</th>';
	}
	$hours_of_operation .= '</tr><tr>';
	reset($days_array);
	// draw days selection
	foreach($days_array as $day_value) {
		$hours_of_operation .= '<td valign="top">';
		$hours_of_operation .= $hours_operation['selected'][$day_value.'open'].'<br/>';
		$hours_of_operation .= 'to<br/>';
		$hours_of_operation .= $hours_operation['selected'][$day_value.'close'];
		$hours_of_operation .= '<br/>';
		$hours_of_operation .= '</td>';
	}
	$hours_of_operation .= '</tr></table>';	
	echo $hours_of_operation;		
break;
}
		
echo '</font><br>';

if (!empty($adv_info_tbl->products_services)) {
  echo 'Products & Services:'."<br>"."<br>"
  .$adv_info_tbl->products_services."<br>"; 
}
		
echo $adv_info_tbl->customer_description;

	$content = ob_get_contents();
	
ob_end_clean();
	   		
echo $content;
		
echo ']]></description>
</item>';

}
echo '</channel>
</rss>';
?>
