<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if advertiser not logged in redirect to login page
if ($_SESSION['advertiser_logged_in'] != 1) header("Location: ".SITE_SSL_URL."advertiser_admin/advertiser_login.deal");

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Advertiser Level Select';
$page_meta_description = '';
$page_meta_keywords = '';

// assign header constant
$prnt_header = new prnt_header();
$prnt_header->page_header_title = $page_header_title;
$prnt_header->page_meta_description = $page_meta_description;
$prnt_header->page_meta_keywords = $page_meta_keywords;
define('PAGE_HEADER',$prnt_header->print_page_header());

// process form on submit
if (isset($_POST['form_submit'])) {
	// set current date value
	$today = date("Y-m-d H:i:s", time()); 			
	
	// pulls information for selected level
	$adv_lvls_tbl->get_db_vars($_POST['advertiser_level_id']);
	
	$adv_info_tbl->customer_level = $_POST['advertiser_level_id'];
	// determines level expiration date
	$expiration_date = date("Y-m-d",strtotime("+".$adv_lvls_tbl->level_duration." months"));

	$adv_info_tbl->customer_level_exp = $expiration_date;
	// determines level renewal date
	$renewal_date = $todays_date;
	
	$adv_info_tbl->customer_level_renewal_date = $renewal_date;
	$adv_info_tbl->bbb_member = $_POST['bbb_member'];
	$adv_info_tbl->link_partner = $_POST['link_partner'];
	$adv_info_tbl->id = $_SESSION['advertiser_id'];
	// update existing data
	$adv_info_tbl->update_level();
	
	// get selected level data
	$adv_lvls_tbl->get_db_vars($adv_info_tbl->customer_level);

	if($adv_lvls_tbl->level_renewal_cost > 0) {
		// once change has been made redirect to payment options page
		header("Location: ".SITE_SSL_URL."advertiser_payment_options.deal");
	} else {
		// once change has been made redirect to my account page
		header("Location: ".SITE_SSL_URL."my_advertiser_account.deal");
	}
}

// load available account levels
$account_levels = $adv_lvls_tbl->return_all_levels();

// get current level data
$adv_info_tbl->get_db_vars($_SESSION['advertiser_id']);
$adv_lvls_tbl->get_db_vars($adv_info_tbl->customer_level);

// check if level has expired
if (strtotime($adv_info_tbl->customer_level_exp) > strtotime(date("Y-m-d")) && $adv_lvls_tbl->level_renewal_cost > 0) {
	
	$page_output = '';
	$page_output .= '<center><strong>Your current advertisement level has not yet expired</strong></center>';
	
} else {

// draw page output
$page_output = '';
$page_output .= '<form action="" method="post" name="account_signup_form"><input name="form_submit" type="hidden" value="1">
<table class="advertiser_form" width="80%" align="center">
<tr><th colspan="2" align="center">Advertiser Levels</th></tr>
				<tr><td colspan="2">';
// cycle through all account levels and print form
		$requirement_value .= '<script type="text/javascript">'.LB; 
		$default_requirement = '';
		foreach($account_levels as $value) {
			$i++;
						
			$requirement_value .= 'curArray['.$value['id'].'] = "'.str_replace('"','\"',preg_replace('/\>\s+\</', '> <', $value['level_description'])).'";'.LB.LB; 
			if ($i == 1) $default_requirement = $value['level_description'];
		}
		$requirement_value .= '
		
		jQuery(function(){
			jQuery(\'#cert_level\').val('.$adv_info_tbl->customer_level.');
			jQuery("#review").html(curArray['.(empty($adv_info_tbl->customer_level) ? '3' : $adv_info_tbl->customer_level).']);
		}); 
		</script>'.LB; 
		
		// draws level drop down
		$value_drop_down = array();				
		reset($account_levels);
		foreach($account_levels as $value) {
			$value_drop_down[] .= '<tr><td><input name="advertiser_level_id" class="advertiser_level_id" type="radio" value="'.$value['id'].'" '.($value['id'] == $adv_info_tbl->customer_level ? 'checked' : '').' /> '.$value['level_name'].'</td></tr>';
		}
		
$page_output .= $requirement_value.'<table align="center" class="clickrow" id="clickrow2" >'.implode(LB,$value_drop_down).'</table>';
$page_output .= '<div id="review">'.$default_requirement.'</div>';

$page_output .= '<tr>'.LB;
$page_output .= '<th align="center">Initial Payment Method:</th>'.LB;
$page_output .= '</tr>'.LB;
$page_output .= '<tr>'.LB;
$page_output .= '<td><p>Skip this section if you are not interested in premium positions and are running a free listing.</p>
					<p>GET SEEN AHEAD OF THE COMPETITION. With the powerful SEO services of the nations leading authority in Internet marketing thousands of local consumers will see you and your competitors every month. For less than $2 a day your business will be moved to the top of our results page and get up to 300% more responses. For rates see top of page and select 1,3,6 or 12 month term.</p></td>'.LB;
$page_output .= '</tr>'.LB;
//$page_output .= '<tr>'.LB;
//$page_output .= '<th align="center"><strong>For Premium Advertisers Only:</strong> Click All that apply</th>'.LB;
//$page_output .= '</tr>'.LB;
//$page_output .= '<tr>'.LB;
//$page_output .= '<td align="left"><input name="bbb_member" type="checkbox" value="1" '.($adv_info_tbl->bbb_member == 1 ? 'checked ' : '').'/> BBB Member accredited business <br><input name="link_partner" type="checkbox" value="1" '.($adv_info_tbl->link_partner == 1 ? 'checked ' : '').'/> I agree to place a link on my website to link back to www.cheaplocaldeals.com.</td>'.LB;
//$page_output .= '</tr>'.LB;
$page_output .= '</td></tr></tr><tr><td align="center"><input class="submit_btn" name="Submit" type="submit" value="Submit"></td></tr></table>';
$page_output .= '</td>
                    </form>';
}

// start output buffer
ob_start();
	
	// load template
	require(TEMPLATE_DIR.'create_account_user_level_select.php');
	
	$html = ob_get_contents();
	
ob_end_clean();

print_page($html);
?>