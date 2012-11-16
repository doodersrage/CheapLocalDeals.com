<?PHP
// document to be used as includes for generating search results KML file

// load application header
require('includes/application_top.php');

// set header type
header('Content-type: application/vnd.google-earth.kml+xml');

// added for page cache handeling
if (ENABLE_SITE_CACHING == 1) {
  define('PRINT_PAGE',cache_page_header());
} else {
  define('PRINT_PAGE',0);
}

// draws page content
if (PRINT_PAGE == 0) {

  if (!class_exists('google_kml_gen')) {
	require(CLASSES_DIR.'pages/search_kml.php');
	$google_kml_gen = new google_kml_gen;
  }
  
  $google_kml_gen->print_kml();
	  
  // start output buffer
  ob_start();
  
  // print header
  echo '<?xml version="1.0" encoding="UTF-8"?>
  <kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2">
  <Document>
	<name>Deals Search List</name>'."\n";
	
  echo implode("\n",$google_kml_gen->mapmarkers);

  echo '<Placemark>
<name>default look locations</name>
<LookAt>
  <longitude>'.$_GET['long'].'</longitude>
  <latitude>'.$_GET['lat'].'</latitude>
  <heading>-60</heading>
  <tilt>70</tilt>
  <range>6300</range>
</LookAt>
</Placemark>
';
  
  echo implode("",$google_kml_gen->list_row);

  // print document footer
  echo '</Document>
		  </kml>'.LB;
  
	$output = ob_get_contents();
	  
  ob_end_clean();
  
// if not set to draw new page content load cached file
} else {
	  
  $file = SITE_CACHE_DIR.get_cache_file_name();
  $fh = fopen($file, 'r+');
  $output = fread($fh, filesize($file));
  fclose($fh);
	  
}

// added for page cache handeling
if (ENABLE_SITE_CACHING == 1) {
  define('DONOT_PRINT_HEADER',1);
  cache_page_footer($output);
}

echo $output;
?>