<?PHP
// load application header
require('application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

echo 	'jQuery(function(){'."\n";
echo 	'var kml = new GGeoXml("'.SITE_URL.'search_kml.kml?set_cat='.(!empty($_GET['set_cat']) ? $_GET['set_cat'] : '')
							   .(!empty($_GET['set_zip']) ? '&set_zip='.$_GET['set_zip'] : '')
							   .(!empty($_GET['radius']) ? '&radius='.$_GET['radius'] : '')
							   .(!empty($_GET['view']) ? '&view='.$_GET['view'] : '')
							   .(!empty($_GET['alpha']) ? '&alpha='.$_GET['alpha'] : '')
							   .(!empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : '')
							   .(!empty($_GET['city']) ? '&city='.urlencode(trim($_GET['city'])) : '')
							   .(!empty($_GET['state']) ? '&state='.urlencode($_GET['state']) : '').'");
var map = new GMap2(document.getElementById("map_canvas"), { size: new GSize(820, 380) } );
map.addOverlay(kml)
map.setCenter(new GLatLng('.(isset($_GET['lat']) ? $_GET['lat'] : '').','.(isset($_GET['long']) ? $_GET['long'] : '').'), 9); 
map.setUIToDefault();'."\n";
		
echo	'});';

?>