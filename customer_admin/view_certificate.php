<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

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
  
  // start output buffer
  ob_start();
  
  // load pdf libs
  require(LIBS_DIR.'fpdf/fpdf.php');
  require(LIBS_DIR.'fpdf/protection.php');
  require(LIBS_DIR.'fpdf/textbox.php');
  
  // clear output buffer
  ob_end_clean();
  
  $cert_terms = strip_tags(CERTIFICATE_AGREEMENT);
  
  $expires_text = 'NO CASH VALUE
  EXPIRES '.date("m/d/Y",strtotime($cert_odrs_tbl->date_added." +1 years"));
  
  $redemable_text = 'Redeemable only at: '.$adv_info_tbl->company_name.'
  '.LB;
  
  $address_text = 'Address: '.$adv_info_tbl->address_1.LB;
  if($adv_info_tbl->address_2 != '') $address_text .= $adv_info_tbl->address_2.LB;
  $address_text .= '        '.$adv_info_tbl->city.', '.$adv_info_tbl->state.' '.$adv_info_tbl->zip.LB.LB;
  
  $telephone_text = 'Telephone: '.$adv_info_tbl->phone_number.'
  '.LB;
  
  $purchase_date_text = 'Purchase Date: '.date("m/d/Y",strtotime($cert_odrs_tbl->date_added)).'
  '.LB;
  
  $terms_text = 'Terms: '.$cert_odrs_tbl->requirements.'
  '.LB;

  if(!empty($cert_odrs_tbl->excludes)) $terms_text .= 'Excludes: '.$cert_odrs_tbl->excludes.'
  '.LB;

  $merchant_use_text = 'FOR MERCHANT USE ONLY
  Please Login to CheapLocalDeals.com to verify certificate authenticity.  For questions or support, please call:
  1-866-283-6809 8:30 to 6:00 EST.
  '.LB;
  
  $certificate_num_text = 'CERTIFICATE #: '.$cert_odrs_tbl->certificate_code;
  
  $pdf=new PDF();
  $pdf->SetProtection(array('print'));
  $pdf->Open();
  $pdf->AddPage();
  $pdf->Image(SITE_DIR.'images/001b.jpg',5,0,95,25,'','http://www.cheaplocaldeals.com');
  $pdf->Image(SITE_DIR.'images/cert3.jpg', 5, 30, 195, 130);
  // draw certificate header
  $pdf->Ln(38);
  // left expires
  $pdf->Cell(11,10);
  $pdf->SetFont('Arial','',8);
  $pdf->drawTextBox($expires_text,30,10, 'L', 'T',0);
  // right expires
  $pdf->Ln(-6);
  $pdf->Cell(145,10);
  $pdf->drawTextBox($expires_text,30,10, 'R', 'T',0);

  if(is_numeric($cert_amt_tbl->discount_amount)) {
	$cert_disc_amt = $cert_amt_tbl->discount_amount;
  } else {
	$cert_disc_amt = $adv_info_tbl->certificate_requirements[$cert_amt_tbl->id]['blank_val'];
  }
  
  // draw gift certificate value
  $pdf->Ln(3);
  $pdf->Cell(80,10);
  $pdf->SetFont('Arial','B',26);
  $pdf->Cell(60,10,'$'.format_currency($cert_disc_amt));
  // draw terms text
  $pdf->Ln(7);
  $pdf->Cell(60,10);
  $pdf->SetFont('Arial','',7);
  $pdf->Cell(170,10,'SUBJECT TO TERMS & CONDITIONS SPECIFIED BELOW');
  
  // draw certificate content
  $pdf->Ln(13);
  $pdf->Cell(11,50);
  // left content box
  $pdf->SetFont('Arial','',10);
  $pdf->drawTextBox($redemable_text.$address_text.$telephone_text,80,60, 'L', 'T',0);
  // right content box
  $pdf->Ln(-25);
  $pdf->Cell(91,50);
  $pdf->drawTextBox($purchase_date_text.$terms_text.$merchant_use_text,80,60, 'L', 'T',0);
  
  // not valid text
  $pdf->SetX(0);
  $pdf->SetY(125);
  $pdf->Cell(25,10);
  $pdf->Cell(170,10,'*** This certificate is not valid without the terms & conditions fully displayed below. ***');
  
  // draw certificate number
  $pdf->Ln(10);
  $pdf->SetFont('Arial','B',14);
  $pdf->Cell(11,10);
  $pdf->Cell(70,10,$certificate_num_text);
  
  // clear page to bottom
  $pdf->Ln(30);
  // draw warning
  $pdf->SetFont('Arial','B',9);
  $pdf->Cell(20,0,'');
  $pdf->Cell(140,0,'',1);
  $pdf->Ln(1);
  $pdf->Cell(25,10);
  $pdf->Cell(170,10,'DO NOT detach bottom portion. Removal of bottom portion will VOID the certificate.');
  $pdf->Ln(10);
  $pdf->Cell(20,0,'');
  $pdf->Cell(140,0,'',1);
  // draw certificate terms
  $pdf->Ln(15);
  $pdf->SetFont('Arial','',9);
  $pdf->MultiCell(180,3,$cert_terms,0,'L');
  // drwa footer
  $pdf->Ln(10);
  $pdf->SetFont('Arial','B',9);
  $pdf->Cell(45,10);
  $pdf->Cell(100,10,'CheapLocalDeals.com © 2008-2009. All Rights Reserved.');
  $pdf->Output();
  
} else {
  echo '<center><strong>The certificate you are trying to view is no longer valid.</strong></center>';
}
?>