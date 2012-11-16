<?PHP
// document to be used as includes for generating search results KML file

// load application header
require('includes/application_top.php');

// set header type
header("Content-Type:text/xml");

// added for page cache handeling
if (ENABLE_SITE_CACHING == 1) {
  define('PRINT_PAGE',cache_page_header());
} else {
  define('PRINT_PAGE',0);
}

  if (!class_exists('api_xml_gen')) {
	require(CLASSES_DIR.'pages/api_xml.php');
	$api_xml_gen = new api_xml_gen;
  }
  
  $api_xml_gen->print_kml();
	  
  // start output buffer
  ob_start();
  
  // print header
  echo '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n";
  echo '<Placemarks>'."\n";
  
  echo implode("",$api_xml_gen->list_row);

  echo '</Placemarks>'."\n";
  
	  $output = ob_get_contents();
	  
  ob_end_clean();
  
// added for page cache handeling
if (ENABLE_SITE_CACHING == 1) {
  define('DONOT_PRINT_HEADER',1);
  cache_page_footer($output);
}

echo $output;
?>