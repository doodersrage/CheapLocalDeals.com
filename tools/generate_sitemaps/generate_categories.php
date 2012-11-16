<?PHP
// load application header
require('../../includes/application_top.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Generate Categories XML Sitemap</title>
</head>

<body>

<?PHP

// get total zip codes count
$sql_query = "SELECT
				count(*) as rcount
			 FROM
				state_city_category
			 WHERE
				url_name <> ''
			 ;";
$rowscount = $dbh->queryRow($sql_query);

// set count to var
$found_cities = $rowscount['rcount'];

// maximum number or lines to read per run
$run_limiter = 5000;

$parent_drop_down_parent = '<?xml version="1.0" encoding="UTF-8"?>'.LB;
$parent_drop_down_parent .= '<sitemapindex xmlns="http://www.google.com/schemas/sitemap/0.84">'.LB;

// cycle through the next 3000 entries
for($cur_row = 0; $cur_row <= $found_cities; $cur_row += $run_limiter) {
	
	// draw sitemap
	$parent_drop_down_parent .= '<sitemap>'.LB;
	$parent_drop_down_parent .= '<loc>'.SITE_URL.'sitemaps/xml_city_cat.deal?city='.$cur_row.'</loc>'.LB;
	$parent_drop_down_parent .= '</sitemap>'.LB;
	
}
$parent_drop_down_parent .= '</sitemapindex>'.LB;

// save file name and location
$file = SITE_DIR.'sitemaps/city_categories_sitemap.xml';
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
