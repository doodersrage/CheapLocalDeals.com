<?PHP

// load zipcodes page
require(SITE_ADMIN_CLASSES_DIR.'forms/zip_codes.php');
$zip_codes_frm = new zip_codes_frm;

// load zipcodes page
require(SITE_ADMIN_CLASSES_DIR.'listings/zip_codes.php');
$zip_codes_lst = new zip_codes_lst;

// write page header
$page_content = page_header('Zip Codes');

// delete selected zip codes
if (!empty($_POST['delete_zip'])) {
  $zip_codes_frm->delete();
}

// delete selected file
if (!empty($_GET['deletefile'])) {
  unlink(SITE_ADMIN_CSV_DIR.'zipcodes/'.$_GET['deletefile']);
}

// select page output function
switch($_GET['mode']) {
// view zip code listing
case 'view';
  $page_content .= $zip_codes_lst->listing();
break;
// view zip code listing
case 'viewhits';
  $page_content .= $zip_codes_lst->views_listing();
break;
// edit existing zip
case 'edit';
  $zip_cds_tbl->get_db_vars($_GET['cid']);
  $page_content .= $zip_codes_frm->edit();
break;
// check edited zip
case 'editcheck':
  $zip_cds_tbl->get_post_vars();
  $error_message = $zip_codes_frm->form_check();
  if (empty($error_message)) {
	$zip_cds_tbl->update();
	$page_content .= create_warning_box('<center>Zip Code Updated</center>');
	$page_content .= $zip_codes_lst->listing();
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $zip_codes_frm->edit();
  } 
break;
// add new zip
case 'add';
  $page_content .= $zip_codes_frm->add();
break;
// add new zip submission check
case 'addcheck':
  $zip_cds_tbl->get_post_vars();
  $error_message = $zip_codes_frm->form_check();
  if (empty($error_message)) {
	$zip_cds_tbl->insert();
	$page_content .= create_warning_box('<center>New zip code has been added</center>');
  } else {
	$page_content .= create_warning_box($error_message);
	$page_content .= $zip_codes_frm->add();
  } 
break;
// csv download page
case 'download';
  $page_content .= '<center><a href="'.SITE_ADMIN_SSL_URL.'?sect=zipcodes&mode=downloadcsv">Click to generate new zip codes CSV.</a></center>';
  $page_content .= $zip_codes_page->generate_existing_csv_list();
break;
// csv generation page
case 'downloadcsv';
  $zip_codes_frm->write_csv();
  header("Location: ".SITE_ADMIN_SSL_URL."?sect=zipcodes&mode=download");
break;
// csv updload page
case 'upload';
  $page_content .= '<center>Browse to file to upload then click submit.<form action="'.SITE_ADMIN_SSL_URL.'?sect=zipcodes&mode=uploadcsv" method="post" enctype="multipart/form-data" name="csv_upload"><input name="csv_file" type="file" /><br><input name="submit" type="submit" value="Submit" /></form></center>';
break;
case 'uploadcsv';
  $zip_codes_frm->upload_csv();
  $page_content .= '<center>CSV uploaded.</center>';
break;
}

?>