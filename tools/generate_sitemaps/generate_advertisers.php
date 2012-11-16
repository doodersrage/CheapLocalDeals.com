<?PHP
// load application header
require('../../includes/application_top.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Generate Advertisers XML Sitemap</title>
</head>

<body>

<?PHP

// get total zip codes count
$sql_query = "SELECT
				count(*) as rcount
			 FROM
				advertiser_info
			 ;";
$rowscount = $dbh->queryRow($sql_query);

// set count to var
$found_cities = $rowscount['rcount'];

// maximum number or lines to read per run
$run_limiter = 1000;

$parent_drop_down_parent = '<?xml version="1.0" encoding="UTF-8"?>'.LB;
$parent_drop_down_parent .= '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'.LB;

$cur_date = date('Y-m-d');

// save file name and location
$file = SITE_DIR.'sitemaps/advertiser_sitemap.xml';
// write to file
$fp = fopen($file, 'w');

// write buffer to file		
fwrite($fp, $parent_drop_down_parent.LB);

// cycle through the next 3000 entries
for($cur_row = 0; $cur_row <= $found_cities; $cur_row += $run_limiter) {

	// this section generates a new category for all cities within the site
	$sql_query = "SELECT
					id
				 FROM
					advertiser_info
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
		$adv_info_tbl->get_db_vars($cur_city['id']);
	
		// draw sitemap
		$sitemap_url = '<url>'.LB;
		$sitemap_url .= '<loc>'.SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/</loc>'.LB;
		$sitemap_url .= '<lastmod>'.date('Y-m-d',strtotime($adv_info_tbl->date_updated)).'</lastmod>'.LB;
		$sitemap_url .= '<changefreq>weekly</changefreq>'.LB;
		$sitemap_url .= '</url>'.LB;
		
		// write buffer to file		
		fwrite($fp, $sitemap_url);
		
		$sitemap_url = '';
		
	}
//	$rows_city->free();
}
$parent_drop_down_parent = '</urlset>'.LB;

// write buffer to file		
fwrite($fp, $parent_drop_down_parent.LB);

// close opened file
fclose($fp);

echo 'Finished';

?>

</body>
</html>
