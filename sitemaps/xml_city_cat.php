<?PHP
// load application header
require('../includes/application_top.php');

// set header type
header('Content-type: application/kml; charset="utf-8"',true);

echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'.LB;

// category sitemap
function category_sitemap_gen() {
		global $dbh, $cats_tbl, $url_nms_tbl, $ste_cty_cat_tbl;
	
	$parent_drop_down = '';
	
	// maximum number or lines to read per run
	$run_limiter = 5000;
			
	$sql_query = "SELECT
					id
				 FROM
					state_city_category
				 WHERE
				 	url_name <> ''
				 LIMIT
					".(int)$_GET['city'].",".$run_limiter."
				 ;";

	$rows = $dbh->queryAll($sql_query);
	
	foreach ($rows as $categories) {
	
		$ste_cty_cat_tbl->get_db_vars($categories['id']);
	
		if ($ste_cty_cat_tbl->url_name != '') {
			$url_nms_tbl->get_db_vars($ste_cty_cat_tbl->url_name);
		
			if ($ste_cty_cat_tbl->category != '') {
				// draw parent 	
				$parent_drop_down_parent = '<url>'.LB;
				$parent_drop_down_parent .= '<loc>'.SITE_URL.($ste_cty_cat_tbl->url_name == '' ? 'sections/category_results.deal?cat='.$ste_cty_cat_tbl->category.'&city='.$ste_cty_cat_tbl->city : htmlspecialchars($url_nms_tbl->url_name) . "/" ).'</loc>'.LB;
				$parent_drop_down_parent .= '<lastmod>'.date('Y-m-d',strtotime($ste_cty_cat_tbl->updated)).'</lastmod>'.LB;
				$parent_drop_down_parent .= '<changefreq>weekly</changefreq>'.LB;
				$parent_drop_down_parent .= '</url>'.LB;
				
				$parent_drop_down .= $parent_drop_down_parent;
			}
		}
	}
		
return $parent_drop_down;
}

echo category_sitemap_gen();

?>
</urlset>