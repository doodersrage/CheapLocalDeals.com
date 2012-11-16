<?PHP
// load application header
require('../../includes/application_top.php');

db_disconnect();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Generate Cities XML Sitemap</title>
</head>

<body>

<?PHP

db_connect();

// get total zip codes count
$sql_query = "SELECT
				count(*) as rcount
			 FROM
				cities
			 ;";
$rowscount = $dbh->queryRow($sql_query);

// set count to var
$found_cities = $rowscount['rcount'];

// maximum number or lines to read per run
$run_limiter = 1000;

$parent_drop_down_parent = '<?xml version="1.0" encoding="UTF-8"?>'.LB;
$parent_drop_down_parent .= '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'.LB;

// cycle through the next 3000 entries
for($cur_row = 0; $cur_row <= $found_cities; $cur_row += $run_limiter) {

	db_connect();
	// this section generates a new category for all cities within the site
	$sql_query = "SELECT
					id,
					state
				 FROM
					cities
				 LIMIT
					?, ?
				 ;";
			 
	$values = array(
					$cur_row,
					$run_limiter
					);

	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute($values);

	while($cur_city = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				
		 //Set it to no-limit
		set_time_limit(0);

		// load city data
		$cities_tbl->get_db_vars($cur_city['id']);
	
	  echo $cities_tbl->url_name.' '.memory_get_usage().'<br>';
	  
		if ($cities_tbl->url_name != '') {
			$url_nms_tbl->get_db_vars($cities_tbl->url_name);
		}
		// draw sitemap
		$parent_drop_down_parent .= '<url>'.LB;
		$parent_drop_down_parent .= '<loc>'.SITE_URL.($cities_tbl->url_name == '' ? 'sections/results.deal?city='.$cur_city['id'] : htmlspecialchars($url_nms_tbl->url_name) . "/" ).'</loc>'.LB;
		$parent_drop_down_parent .= '<lastmod>'.date('Y-m-d',strtotime($cities_tbl->updated)).'</lastmod>'.LB;
		$parent_drop_down_parent .= '<changefreq>weekly</changefreq>'.LB;
		$parent_drop_down_parent .= '</url>'.LB;
		
	}
	$result->free();
	db_disconnect();
}
db_disconnect();

$parent_drop_down_parent .= '</urlset>'.LB;

// save file name and location
$file = SITE_DIR.'sitemaps/cities_sitemap.xml';
// write to file
$fp = fopen($file, 'w');

// write buffer to file		
fwrite($fp, $parent_drop_down_parent.LB);

// close opened file
fclose($fp);

echo 'Finished';

?>

</body>
</html>
