<?PHP

// load sessions pages
require(SITE_ADMIN_CLASSES_DIR.'forms/create_certificate.php');
$create_certificate_frm = new create_certificate_frm;

// write page header
$page_content = page_header('Create Certificate');

// select page output function
switch ($_GET['mode']) {
// add new certificate form
case 'new':
  $page_content .= $create_certificate_frm->add();
// certificate add check
break;
case 'addcheck':
  $cert_odrs_tbl->order_id = 0;
  $cert_odrs_tbl->customer_id = $_POST['customer_select'];
  $cert_odrs_tbl->advertiser_id = $_POST['advertiser_select'];
  $cert_odrs_tbl->requirements = $_POST['certificate_requirements'];
  $cert_odrs_tbl->excludes = $_POST['certificate_excludes'];
  $cert_odrs_tbl->certificate_amount_id = $_POST['certificate_select'];
  $cert_odrs_tbl->certificate_code = $cert_odrs_tbl->generate_certificate_code();
  $cert_odrs_tbl->enabled = 1;
  $cert_odrs_tbl->insert();
  $page_content .= create_warning_box('<center><strong>New certificate added.</strong></center>');
break;
}

?>