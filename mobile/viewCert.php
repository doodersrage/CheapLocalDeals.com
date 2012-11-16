<?PHP
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
// load application header
require('includes/app_top.php');

//// if advertiser not logged in redirect to login page
//if ($_SESSION['customer_logged_in'] != 1) header("Location: ".SITE_SSL_URL."account_login.php");

// set certificate id
$cert_id = $_GET['cert_id'];

// pull certificate info
$cert_odrs_tbl->assign_db_vars_by_cert_id($cert_id);

// check if certificate is active
if($cert_odrs_tbl->enabled == 1) {
  
  // pull advertiser info
  $adv_info_tbl->get_db_vars($cert_odrs_tbl->advertiser_id);
  // pull certificate amount info
  $cert_amt_tbl->get_db_vars($cert_odrs_tbl->certificate_amount_id);
    
  $cert_terms = CERTIFICATE_AGREEMENT;
  
  $expires_text = 'NO CASH VALUE
  EXPIRES '.date("m/d/Y",strtotime($cert_odrs_tbl->date_added." +1 years"));
  
  $redemable_text = '<p>Redeemable only at: '.$adv_info_tbl->company_name.'</p>'.LB;
  
  $address_text = '<p>Address: '.$adv_info_tbl->address_1.'<br/>'.LB;
  if($adv_info_tbl->address_2 != '') $address_text .= $adv_info_tbl->address_2.'<br/>'.LB;
  $address_text .= '        '.$adv_info_tbl->city.', '.$adv_info_tbl->state.' '.$adv_info_tbl->zip.'</p>'.LB;
  
  $telephone_text = '<p>Telephone: <a href="tel:'.$adv_info_tbl->phone_number.'">'.$adv_info_tbl->phone_number.'</a></p>'.LB;
  
  $purchase_date_text = '<p>Purchase Date: '.date("m/d/Y",strtotime($cert_odrs_tbl->date_added)).'</p>'.LB;
  
  $terms_text = '<p>Terms: '.$cert_odrs_tbl->requirements.'</p>'.LB;

  if(!empty($cert_odrs_tbl->excludes)) $terms_text .= '<p>Excludes: '.$cert_odrs_tbl->excludes.'</p>'.LB;

  $merchant_use_text = '<p>FOR MERCHANT USE ONLY
  Please Login to CheapLocalDeals.com to verify certificate authenticity.  For questions or support, please call:
  1-866-283-6809 8:30 to 6:00 EST.</p>'.LB;
  
  $certificate_num_text = '<p>CERTIFICATE #: '.$cert_odrs_tbl->certificate_code.'</p>';
  
  if(is_numeric($cert_amt_tbl->discount_amount)) {
	$cert_disc_amt = $cert_amt_tbl->discount_amount;
  } else {
	$cert_disc_amt = $adv_info_tbl->certificate_requirements[$cert_amt_tbl->id]['blank_val'];
  }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<title>View Certificate</title>
<link rel="stylesheet" type="text/css" href="includes/template/style.css" media="screen" />
</head>
<body>
<div class="wrap">
    <div class="main">
        <div class="logo">
        	<a href="<?PHP echo MOB_URL; ?>"><img class="alnCent" src="includes/template/images/gldDolLog.png" height="65" width="62" alt="Cheap Local Deals dollar sign logo image"></a><br/>
            <a href="<?PHP echo MOB_URL; ?>"><img class="alnCent" src="includes/template/images/cldLogo.png" height="35" width="275" alt="Cheap Local Deals start saving logo image"></a>
        </div>
        <div class="mnTxt">
        	<table width="100%">
            <tr>
            	<td align="left" width="50%"><?PHP echo $expires_text; ?></td><td align="right" width="50%"><?PHP echo $expires_text; ?></td>
            </tr>
            <tr>
            	<td colspan="2" align="center"><?PHP echo '$'.format_currency($cert_disc_amt);  ?><br />
				<strong>SUBJECT TO TERMS & CONDITIONS SPECIFIED BELOW</strong></td>
            </tr>
            <tr>
            	<td align="left" valign="top" width="50%"><?PHP echo $redemable_text.$address_text.$telephone_text; ?></td>
                <td align="left" valign="top" width="50%"><?PHP echo $purchase_date_text.$terms_text.$merchant_use_text; ?></td>
            </tr>
             <tr>
            	<td colspan="2" align="center">*** This certificate is not valid without the terms & conditions fully displayed below. ***</td>
            </tr>
             <tr>
            	<td colspan="2" align="left"><strong><?PHP echo $certificate_num_text; ?></strong></td>
            </tr>
           </table>
        </div>
        <div class="mnTxt">
        	<?PHP echo $cert_terms; ?>
        </div>
        <div class="smallPad textCent boxCent"> CheapLocalDeals.com &copy; 2008-<?PHP echo  date('Y'); ?>.&nbsp; All Rights Reserved. </div>
    </div>
</div>
</body>
</html>
<?PHP
} else {
  echo '<center><strong>The certificate you are trying to view is no longer valid.</strong></center>';
}
?>