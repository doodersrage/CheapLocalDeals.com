<?php
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

require_once ("../includes/libs/paypalfunctions.php");
// ==================================
// PayPal Express Checkout Module
// ==================================

//'------------------------------------
//' The paymentAmount is the total value of 
//' the shopping cart, that was set 
//' earlier in a session variable 
//' by the shopping cart page
//'------------------------------------
$total_amount = $shopping_cart_manage->sub_total;

if(!empty($_POST['promo_code']) && $cust_promo_cds_tbl->promo_code_chk($_POST['promo_code']) > 0) {
	$cust_promo_cds_tbl->assign_db_vars_procode($_POST['promo_code']);
	$promo_discount_amt = $total_amount*($cust_promo_cds_tbl->percentage/100);
	$total_amount = $total_amount-$promo_discount_amt;
}

if(TAX_STATE_VAL == $geo_data->region && TAX_APPLY_VALUE == 1) {
  $tax_amount = $shopping_cart_manage->sub_total*(TAX_AMOUNT_VAL/100);
  $total_amount = $total_amount+$tax_amount;
  $paymentAmount = $total_amount;
}
  
 $paymentAmount = $total_amount;
 
//'------------------------------------
//' The currencyCodeType and paymentType 
//' are set to the selections made on the Integration Assistant 
//'------------------------------------
$currencyCodeType = "USD";
$paymentType = "Sale";

//'------------------------------------
//' The returnURL is the location where buyers return to when a
//' payment has been succesfully authorized.
//'
//' This is set to the value entered on the Integration Assistant 
//'------------------------------------
$returnURL = "https://www.cheaplocaldeals.com/checkout/checkout_success.php";

//'------------------------------------
//' The cancelURL is the location buyers are sent to when they hit the
//' cancel button during authorization of payment during the PayPal flow
//'
//' This is set to the value entered on the Integration Assistant 
//'------------------------------------
$cancelURL = "https://www.cheaplocaldeals.com/checkout/";
//'------------------------------------
//' Calls the SetExpressCheckout API call
//'
//' The CallShortcutExpressCheckout function is defined in the file PayPalFunctions.php,
//' it is included at the top of this file.
//'-------------------------------------------------
$resArray = CallShortcutExpressCheckout ($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL);
$ack = strtoupper($resArray["ACK"]);
if($ack=="SUCCESS")
{
	$pp_pmts_tbl->session_id = session_id();
	$pp_pmts_tbl->token = $resArray["TOKEN"];
	$pp_pmts_tbl->amount = $paymentAmount;
	$pp_pmts_tbl->insert();
	
	RedirectToPayPal ( $resArray["TOKEN"] );
} 
else  
{
	//Display a user friendly Error on the page using any of the following error information returned by PayPal
	$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
	$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
	$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
	$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
	
	echo "SetExpressCheckout API call failed. ";
	echo "Detailed Error Message: " . $ErrorLongMsg;
	echo "Short Error Message: " . $ErrorShortMsg;
	echo "Error Code: " . $ErrorCode;
	echo "Error Severity Code: " . $ErrorSeverityCode;
}
?>