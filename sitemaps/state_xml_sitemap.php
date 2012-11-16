<?PHP
// load application header
require('../includes/application_top.php');

// set header type
header('Content-type: application/xml; charset="utf-8"',true);

echo '<?xml version="1.0" encoding="UTF-8"?>'.LB;

$parent_drop_down_parent_sitemap = '';

// category drop down menu
function category_sitemap_gen() {
		global $dbh, $stes_tbl, $url_nms_tbl, $parent_drop_down_parent_sitemap;
	
	$sql_query = "SELECT
					id
				 FROM
					states
				 ;";
	$rows = $dbh->queryAll($sql_query);

	foreach ($rows as $categories) {
	
		$stes_tbl->get_db_vars($categories['id']);
	
		if ($stes_tbl->url_name != '') {
			$url_nms_tbl->get_db_vars($stes_tbl->url_name);
		}
		
		// draw sitemap
		$parent_drop_down_parent = '<url>'.LB;
		$parent_drop_down_parent .= '<loc>'.SITE_URL.($stes_tbl->url_name == '' ? 'sections/state_browse.deal?state='.$categories['id'] : htmlspecialchars($url_nms_tbl->url_name) . "/" ).'</loc>'.LB;
		$parent_drop_down_parent .= '<lastmod>'.date('Y-m-d',strtotime($stes_tbl->updated)).'</lastmod>'.LB;
		$parent_drop_down_parent .= '<changefreq>weekly</changefreq>'.LB;
		$parent_drop_down_parent .= '</url>'.LB;
		
		// draw sitemap link
		$parent_drop_down_parent_sitemap .= '<sitemap>
										<loc>'.SITE_URL.'state_xml_sitemap.deal?state='.$stes_tbl->acn.'</loc>
										<lastmod>'.date('Y-m-d').'</lastmod>
									</sitemap>'.LB;
		
		$parent_drop_down .= $parent_drop_down_parent;
	}
		
return $parent_drop_down;
}

function state_city_sitemap_gen() {
		global $dbh, $cities_tbl, $url_nms_tbl, $parent_drop_down_parent_sitemap;
	
	$sql_query = "SELECT
					id
				 FROM
					cities
				 WHERE
					state = ?
				 ;";

	$values = array(
					$_GET['state']
					);
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute($values);

	while ($categories = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	
		$cities_tbl->get_db_vars($categories['id']);
	
		if ($cities_tbl->url_name != '') {
			$url_nms_tbl->get_db_vars($cities_tbl->url_name);
		}
		
		// draw sitemap
		$parent_drop_down_parent = '<url>'.LB;
		$parent_drop_down_parent .= '<loc>'.SITE_URL.($cities_tbl->url_name == '' ? 'sections/results.deal?city='.$categories['id'] : htmlspecialchars($url_nms_tbl->url_name) . "/" ).'</loc>'.LB;
		$parent_drop_down_parent .= '<lastmod>'.date('Y-m-d').'</lastmod>'.LB;
		$parent_drop_down_parent .= '<changefreq>weekly</changefreq>'.LB;
		$parent_drop_down_parent .= '<priority>1.0</priority>'.LB;
		$parent_drop_down_parent .= '</url>'.LB;
		
		// draw sitemap link
		$parent_drop_down_parent_sitemap .= '<sitemap>
										<loc>'.SITE_URL.'state_xml_sitemap.deal?city='.$categories['id'].'</loc>
										<lastmod>'.date('Y-m-d').'</lastmod>
									</sitemap>'.LB;
		
		$parent_drop_down .= $parent_drop_down_parent;
	}

	// clear result set
	$result->free();		

return $parent_drop_down;
}

function state_city_category_sitemap_gen() {
		global $dbh, $ste_cty_cat_tbl, $url_nms_tbl, $parent_drop_down_parent_sitemap;
	
	$sql_query = "SELECT
					id
				 FROM
					state_city_category
				 WHERE
					city = ?
				 ;";

	$values = array(
					(int)$_GET['city']
					);
	
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute($values);

	while ($categories = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
	
		$ste_cty_cat_tbl->get_db_vars($categories['id']);
	
		if ($ste_cty_cat_tbl->url_name != '') {
			$url_nms_tbl->get_db_vars($ste_cty_cat_tbl->url_name);
		}
		
		// draw sitemap
		$parent_drop_down_parent = '<url>'.LB;
		$parent_drop_down_parent .= '<loc>'.SITE_URL.($ste_cty_cat_tbl->url_name == '' ? 'sections/category_results.deal?city='.$categories['id'].'&cat='.$ste_cty_cat_tbl->category : htmlspecialchars($url_nms_tbl->url_name) . "/" ).'</loc>'.LB;
		$parent_drop_down_parent .= '<lastmod>'.date('Y-m-d').'</lastmod>'.LB;
		$parent_drop_down_parent .= '<changefreq>weekly</changefreq>'.LB;
		$parent_drop_down_parent .= '<priority>1.0</priority>'.LB;
		$parent_drop_down_parent .= '</url>'.LB;
		
		// draw sitemap link
		$parent_drop_down_parent_sitemap .= '<sitemap>
										<loc>'.SITE_URL.'state_xml_sitemap.deal?category='.$categories['id'].'</loc>
										<lastmod>'.date('Y-m-d').'</lastmod>
									</sitemap>'.LB;
		
		$parent_drop_down .= $parent_drop_down_parent;
	}

	// clear result set
	$result->free();		
		
return $parent_drop_down;
}

function state_city_category_listing_sitemap_gen() {
		global $dbh, $ste_cty_cat_tbl, $url_nms_tbl, $cities_tbl, $zip_cds_tbl, $cats_tbl, $adv_info_tbl;
	
	$ste_cty_cat_tbl->get_db_vars($_GET['category']);
	$cities_tbl->get_db_vars($ste_cty_cat_tbl->city);
	// build zip codes array
	$zip_cds_tbl->city_id = $cities_tbl->id;
	$zip_array = $zip_cds_tbl->get_list();

		$zip_string = implode(', ',$zip_array);

		// check for parent or subcategory
		$cats_tbl->get_db_vars($ste_cty_cat_tbl->category);

		// if selected category is a sub category display normal listing
		if (($cats_tbl->child_cat_count($cats_tbl->id) == 0)) {
		
			// set category val
			$cat_val = $ste_cty_cat_tbl->category;

			  $list_cat_array = array();
			  $sql_query = "SELECT
						  distinct ci.id as advertisers_id
					   FROM
						  advertiser_info ci LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id 
						  INNER JOIN advertiser_levels al ON al.id = ci.customer_level
					  WHERE
					   ci.zip IN (".$zip_string.") 
					  AND ci.account_enabled = 1 
					  AND ci.approved = 1 
					  AND ci.update_approval = 1
					  ;";
			  $rows = $dbh->queryAll($sql_query);
			  // set session list array
			  foreach($rows as $cur_row) {
				  // added to check level selected and if payment info has been entered
				  $adv_info_tbl->get_db_vars($cur_row['advertisers_id']);
				  if ($adv_info_tbl->customer_level != 3) {
					if(!empty($adv_info_tbl->payment_method)) {
					  $list_cat_array[$cur_row['advertisers_id']] = $cur_row['advertisers_id'];
					}
				  } else {
					  $list_cat_array[$cur_row['advertisers_id']] = $cur_row['advertisers_id'];
				  }
			  }
			  // set current session list cat var
			  $list_cat = $cat_val;			
		} else {
			  
			  $list_cat_array = array();
			  
			  // if selected category is a parent category display all categories below
			  $child_array = $cats_tbl->get_child_cats($ste_cty_cat_tbl->category);
			  
			  $selected_advert_arr = array();
			  
			  foreach($child_array as $cur_id) {
				  $sql_query = "SELECT
							  distinct ci.id as advertisers_id, ac.category_id as category_id
						   FROM
							  advertiser_info ci LEFT JOIN advertiser_categories ac ON ac.advertiser_id = ci.id 
							  INNER JOIN advertiser_levels al ON al.id = ci.customer_level
						  WHERE
						  ac.category_id = '".$cur_id['id']."' 
						  AND ci.zip IN (".$zip_string.") 
						  AND ci.account_enabled = 1 
						  AND ci.approved = 1 
						  AND ci.update_approval = 1
						  ;";
				  $rows = $dbh->queryAll($sql_query);
			  
				  foreach($rows as $cur_row) {
				  	  // added to check level selected and if payment info has been entered
					  $adv_info_tbl->get_db_vars($cur_row['advertisers_id']);
					  if ($adv_info_tbl->customer_level != 3) {
					  	if(!empty($adv_info_tbl->payment_method)) {
						  $list_cat_array[$cur_row['advertisers_id']] = $cur_row['advertisers_id'];
						}
				  	  } else {
						  $list_cat_array[$cur_row['advertisers_id']] = $cur_row['advertisers_id'];
					  }
				  }
				  
			  }
			  
		}

	foreach ($list_cat_array as $categories) {
	
		// pull selected advertiser info
		$adv_info_tbl->get_db_vars($categories);
				
		// draw sitemap
		$parent_drop_down_parent = '<url>'.LB;
		$parent_drop_down_parent .= '<loc>'.SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/</loc>'.LB;
		$parent_drop_down_parent .= '<lastmod>'.date('Y-m-d').'</lastmod>'.LB;
		$parent_drop_down_parent .= '<changefreq>weekly</changefreq>'.LB;
		$parent_drop_down_parent .= '<priority>1.0</priority>'.LB;
		$parent_drop_down_parent .= '</url>'.LB;
				
		$parent_drop_down .= $parent_drop_down_parent;
	}
		
return $parent_drop_down;
}

//if($_GET['state']) {
//	$sitemap = state_city_sitemap_gen();
//} elseif($_GET['city']) {
//	$sitemap = state_city_category_sitemap_gen();
//} elseif($_GET['category']) {
//	$sitemap = state_city_category_listing_sitemap_gen();
//} else {
	$sitemap = category_sitemap_gen();
//}

//if(!empty($parent_drop_down_parent_sitemap)) {
//echo '<sitemapindex xmlns="http://www.google.com/schemas/sitemap/0.84">'.LB;
//	echo $parent_drop_down_parent_sitemap;
//echo '</sitemapindex>'.LB;
//}

echo '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'.LB;
echo $sitemap;
echo '</urlset>'.LB;
?>