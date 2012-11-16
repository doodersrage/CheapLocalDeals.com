<?PHP
/////////////////////////////////////////////////////////////////////////////////
//  Class used to draw content for category results page
/////////////////////////////////////////////////////////////////////////////////
//  Functions within this class:
//  __construct: Runs at initiation of class.
//  bread_crumbs: Generates pages breadcrumbs string.
//  bread_crumbs_parent: Generates breadcrumb parents string.
//  get_category_adverts: Gets actual listing of advertisers.
//  get_listing: Assembles page listing.
//  category_drop_down: Generates header nav category drop down options.
//  parent_dd_child_chk: Pulls child category options for category drop down selection.
//  build_quantity_drop_down: Builds order form quantity drop down options.
//  build_value_drop_down: Creates order form quantity drop down options.
//  draw_list_row: Creates a row for the categories listing by the assigned advertisers id.
//  row_foot: Creates spacer between advertiser rows.
//  reg_list_head: Draws the regular listing header.
//  sponsored_list_head: Draws the header for sponsored advertiser listing.
/////////////////////////////////////////////////////////////////////////////////

class category_results_pg {
	public $bc, $zip, $state, $city, $category, $list_row, $view, $alpha_filter, $search_results;
	// used for listing
	private $advert_noncert_id, $advertisers_id, $zip_array;

	public function __construct() {
		global $geo_data, $cities_tbl, $listing_qry;
		
		$this->zip = (!empty($_SESSION['cur_zip']) ? $_SESSION['cur_zip'] : '' );
		$this->category = (isset($_GET['cat']) ? (int)$_GET['cat'] : $geo_data->cityid);
		// sets current cat value
		$_SESSION['previous_cat_id'] = $this->category;
		$this->view = (isset($_GET['view']) ? $_GET['view'] : '');
		if(isset($_GET['alpha'])) $_SESSION['alpha_filter'] = (isset($_GET['alpha']) ? $_GET['alpha'] : '');
		$this->alpha_filter = (isset($_SESSION['alpha_filter']) ? $_SESSION['alpha_filter'] : '');
		$this->search_results = (isset($_POST['cur_results_search']) ? $_POST['cur_results_search'] : '');
		
		// if city value is set assign city and state values
		if(!empty($_GET['city'])) {
			// set city values
			$cities_tbl->get_db_vars($_GET['city']);
			$this->city = $cities_tbl->city;
			$this->state = $cities_tbl->state;
		}
		
		$this->bread_crumbs();
		
	}
	
	// display current page link and link backs
	public function bread_crumbs() {
		global $cats_tbl, $url_nms_tbl, $zip_cds_tbl, $cities_tbl, $ste_cty_cat_tbl, $obj_google, $api_load;
	
		if ($this->view != 'all') {
			$cats_tbl->get_db_vars($this->category);
			
			if ($cats_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($cats_tbl->url_name);
		}
				
		$zip_cds_tbl->search($this->zip);
		
		// assign link names
		if (isset($_GET['city'])) {
			// pull category city url name
			$ste_cty_cat_tbl->city_category_search($_GET['city'],$this->category);
			
			if($ste_cty_cat_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($ste_cty_cat_tbl->url_name);
				$link_name = $url_nms_tbl->url_name.'/';
			} else {
				$link_name = 'sections/category_results.deal?cat='.$this->category.'&city='.$cities_tbl->id;
			}
			$view_all_link = 'view-all-results/?city='.$cities_tbl->id;
			$link_text = $cities_tbl->city;
		} else {
			$cities_tbl->get_db_vars($zip_cds_tbl->city_id);
			if($cats_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($cats_tbl->url_name);
				$link_name = htmlspecialchars($url_nms_tbl->url_name).'/';
			} else {
				$link_name = 'sections/category_results.deal?cat='.$this->category;
			}
			$view_all_link = 'view-all-results/';
			$link_text = $cities_tbl->city;
		}
		
		// added 12/11/2009 to write urls for api system
		if ($api_load->status == 1) {
		
			// pull category city url name
			$ste_cty_cat_tbl->city_category_search($_GET['city'],$this->category);
			
			if($ste_cty_cat_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($ste_cty_cat_tbl->url_name);
				$link_name = '?page='.urlencode($url_nms_tbl->url_name);
			} else {
				$link_name = '';
			}
			$view_all_link = '';
		}
			
		$this->bc[] = '<a href="'.SITE_URL.$link_name.'">'.$link_text.' '.$cats_tbl->category_name.'</a>';
			
		if ($cats_tbl->parent_category_id > 0) $this->bread_crumbs_parent($cats_tbl->parent_category_id);
		} else {
			// assign link names
			if (isset($_GET['city'])) {
				$view_all_link = 'view-all-results/?city='.$cities_tbl->id;
			} else {
				$view_all_link = 'view-all-results/';
			}
	
			$this->bc[] = '<a href="'.SITE_URL.$view_all_link.'">View All</a>';
		}
				
		$zip_cds_tbl->search($this->zip);

		if (isset($_GET['city'])) {		
			if($cities_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($cities_tbl->url_name);
				$link_name = $url_nms_tbl->url_name.'/';
			} else {
				$link_name = 'sections/results.deal?city='.$_GET['city'];
			}
			$link_text = $cities_tbl->city.', '.$cities_tbl->state;
		} else {
			$cities_tbl->get_db_vars($zip_cds_tbl->city_id);
			if ($zip_cds_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($zip_cds_tbl->url_name);
				$link_name = $url_nms_tbl->url_name.'/';
			} else {
				$link_name = 'sections/results.deal?setzip=' . $this->zip;
			}	
			$link_text = $cities_tbl->city.', '.$cities_tbl->state;
		}
		
		// added 12/11/2009 to write urls for api system
		if ($api_load->status == 1) {
	
			$link_name = '';
			
		}

		$this->bc[] = '<a href="'.SITE_URL.$link_name.'">'.$link_text.' Categories</a>';
		
		krsort($this->bc);
		
		$this->bc = implode(' > ',$this->bc);
		
	}
	
	// bread crumbs sub function
	private function bread_crumbs_parent($parent_id) {
		global $cats_tbl, $url_nms_tbl, $cities_tbl, $ste_cty_cat_tbl, $zip_cds_tbl, $api_load;
		
		$cats_tbl->get_db_vars($parent_id);
		
		if ($cats_tbl->url_name > 0) {
			$url_nms_tbl->get_db_vars($cats_tbl->url_name);
		}
		
		// assign link names
		if (isset($_GET['city'])) {
			// pull category city url name
			$ste_cty_cat_tbl->city_category_search($_GET['city'],$parent_id);
			
			if($ste_cty_cat_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($ste_cty_cat_tbl->url_name);
				$link_name = $url_nms_tbl->url_name.'/';
			} else {
				$link_name = 'sections/category_results.deal?cat='.$cats_tbl->id.'&city='.$cities_tbl->id;
			}
			$link_text = $cities_tbl->city.', '.$cities_tbl->state;
		} elseif (isset($_SESSION['cur_zip'])) {
			$zip_cds_tbl->search($_SESSION['cur_zip']);
			if($cats_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($cats_tbl->url_name);
				$link_name = htmlspecialchars($url_nms_tbl->url_name).'/';
			} else {
				$link_name = 'sections/category_results.deal?cat='.$cats_tbl->id;
			}
			$cities_tbl->get_db_vars($zip_cds_tbl->city_id);
			$link_text = $cities_tbl->city.', '.$cities_tbl->state;
		}
		
		// added 12/11/2009 to write urls for api system
		if ($api_load->status == 1) {
		
			$ste_cty_cat_tbl->city_category_search($_GET['city'],$parent_id);
			
			if($ste_cty_cat_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($ste_cty_cat_tbl->url_name);
				$link_name = '?page='.urlencode($url_nms_tbl->url_name);
			} else {
				$link_name = '';
			}
		}
		
		$this->bc[] = '<a href="'.SITE_URL.$link_name.'">'.$link_text.' '.$cats_tbl->category_name.'</a>';

		if ($cats_tbl->parent_category_id > 0) $this->bread_crumbs_parent($cats_tbl->parent_category_id);
	
	}
	
	// runs listing query
	public function get_listing() {
		global $dbh, $cats_tbl, $url_nms_tbl, $adv_info_tbl, $cities_tbl, $ste_cty_cat_tbl, $no_index, $listing_qry, $api_load, $zip_cds_tbl;
				
		// load get listing class
		require(CLASSES_DIR.'sections/category_listings.php');
		$listing_qry = new listing_qry;

		$this->list_row = $listing_qry->list_row;

		// sets page link value
		if(isset($_GET['view'])) {
			if($_GET['view'] == 'all') {
				$page_str = 'view-all-results/'.(!empty($_GET['city']) ? '?city='.$_GET['city'] : '');
			} 
		} else {
		
			// assign link names
			if (!empty($_GET['city'])) {
				// pull category city url name
				$ste_cty_cat_tbl->city_category_search($_GET['city'],$this->category);
				
				if($ste_cty_cat_tbl->url_name > 0) {
					$url_nms_tbl->get_db_vars($ste_cty_cat_tbl->url_name);
					$page_str  = $url_nms_tbl->url_name.'/';
				} else {
					$page_str = 'sections/category_results.deal?cat='.$this->category.'&city='.$cities_tbl->id;
				}
			} else {
				if($cats_tbl->url_name > 0) {
					$url_nms_tbl->get_db_vars($cats_tbl->url_name);
					$page_str = htmlspecialchars($url_nms_tbl->url_name).'/';
				} else {
					$page_str = 'sections/category_results.deal?cat='.$this->category;
				}
			}
		}
		
		// clear list output value
		$list_output = '';
		// does a count and prints pagination options at top of page
		if (is_array($this->list_row)) {
			if(count($this->list_row) > 0) {
				$results_cnt = count($this->list_row);
				// reset listing pages value
				$listing_pages = '';
				$cur_page = 0;
				
				// breaks listing into selectable pages
				for($i = 0;$i < $results_cnt; $i += $_SESSION['set_per_page_res']) {
					$cur_page++;
					// build link
					$page_link = '<a href="javascript:void(0);" onclick="javascript:set_page_val('.$cur_page.');" class="pagelnk'.$cur_page.'">';
					$page_link .= $cur_page;
					$page_link .= '</a>';
					
					$listing_pages[] = $page_link;
				}
				
				// section modified to break page links into sections
				define('PAGE_LINKS_PER_SEC',9);
				$page_link_limit = PAGE_LINKS_PER_SEC + 1;
				$page_group = 1;
				$cur_page_link = 1;
				$page_group_arr = array();
				foreach($listing_pages as $cur_page_lnk) {
				  if($cur_page_link != $page_link_limit){
					$page_group_arr[$page_group][$cur_page_link] = $cur_page_lnk;
					$cur_page_link++;
				  } else {
					$cur_page_link = 1;
					$page_group++;
					$page_group_arr[$page_group][$cur_page_link] = $cur_page_lnk;						
				  }
				}
				
				$cur_page_link_array = array();
				$listing_pages = '';
				foreach($page_group_arr as $grp_id => $cur_page_grp) {
				  foreach($cur_page_grp as $cur_page_lnk){
					$cur_page_link_array[] = $cur_page_lnk;
				  }
				  
				  $listing_pages .= ($grp_id > 1 ? '<div class="links_sect_'.$grp_id.'" style="display:none" > <a href="javascript:void(0);" onclick="previouslinkspage()"><<</a> | ' : '<div class="links_sect_1" >').implode(' | ',$cur_page_link_array).(count($cur_page_grp) == 9 ? ' | <a href="javascript:void(0);" onclick="nextlinkspage()">>></a>' : '').'</div>';
				  
				  $cur_page_link_array = array();
				}
				
				// printed per page options dd
				$page_options = '';
				for($i = LISTING_PAGE_RESULTS_CNT; $i <= LISTING_PAGE_RESULTS_CNT * 5; $i += LISTING_PAGE_RESULTS_CNT) {
				  $page_options .= '<option value="'.$i.'" '.($_SESSION['set_per_page_res'] == $i ? 'selected="selected"' : '' ).' >'.$i.'</option>';
				}
				
//				$listing_pages = implode(' | ',$listing_pages);
				
				// prints listing header area
				// first table row
				$list_nav = '<tr class="results_head_menu">
							 <td align="left" width="333">
								<div class="boxed_area"><strong>We found <span class="large-green">'.$listing_qry->advert_cnt.'</span> Local Deals!</strong></div>
							  </td>';
				if($api_load->status != 1) {
				  $list_nav .= '<td align="center" width="170"><div class="boxed_area">RESULTS_LISTING_TABLE_HEADER</div></td>';
				}
				$list_nav .= '<td align="right" width="333">
								
									<table class="srch_pgs_bx">
										<tr>
											<td align="right">Pages:</td>
											<td align="left">'.$listing_pages.'</td>
										</tr>
									</table>
								
							</td>
							</tr>
							<tr class="results_head_menu">
								<td colspan="3">
									<table width="100%" class="filterbox" style="border-collapse:collapse;">GOOG_MAP_AREA';
				$goog_map_lnk = '<tr><td>
						  <table id="slidebox1" style="display:none;" align="center">
							  <tr>
								  <td align="center">';
								
				if($api_load->status != 1) {
				  // get selected zip long lat	for map
				  $obj_google=new googleRequest;
				  
				  if (!empty($_GET['city'])) {
					$zip_cds_tbl->city_search($_GET['city']);
					$latlng[0] = $zip_cds_tbl->latitude;
					$latlng[1] = $zip_cds_tbl->longitude;
				  } else {
					$zip_cds_tbl->search($this->zip);
					$latlng[0] = $zip_cds_tbl->latitude;
					$latlng[1] = $zip_cds_tbl->longitude;
				  }
				  
				  $goog_map_lnk .= '<div id="map_canvas" style="width: 820px; height: 380px; border: 1px solid black; text-align: center;"></div>
				  <script src="'.OVERRIDE_SITE_URL.'js_load.deal?type=curl&amp;js_doc='.urlencode('http://maps.google.com/maps?file=api&v=2&key='.GOOGLE_MAPS_API_KEY).'" type="text/javascript"></script>
					  <script type="text/javascript" src="'.SITE_URL.'includes/goog_maps.deal?set_cat='.$this->category.'&amp;set_zip='.$this->zip.'&amp;radius='.$_SESSION['set_radius'].'&amp;view='.$this->view.'&amp;alpha='.$this->alpha_filter.'&amp;search='.urlencode($this->search_results).'&amp;city='.urlencode(trim($this->city)).'&amp;state='.urlencode($this->state).'&amp;lat='.$latlng[0].'&amp;long='.$latlng[1].'&amp;status='.$obj_google->error.'"> </script>

						<div class="goog_map_lnk">
					  	<a href="'.SITE_URL.'search_kml.kml?set_cat='.$this->category.'&amp;set_zip='.$this->zip.'&amp;radius='.$_SESSION['set_radius'].'&amp;view='.$this->view.'&amp;alpha='.$this->alpha_filter.'&amp;search='.urlencode($this->search_results).'&amp;city='.urlencode(trim($this->city)).'&amp;state='.urlencode($this->state).'&amp;lat='.$latlng[0].'&amp;long='.$latlng[1].'&amp;status='.$obj_google->error.'" rel="nofollow" >Download results for integration with Google Earth.</a>
					  </div>';
				}
                
				$goog_map_lnk .= '</td>
							</tr>
						</table>
					</td>
				</tr>';
				
				$list_nav .= '<tr><td class="header_txt">NARROW YOUR SEARCH</td></tr>
				<tr>
					<td>
						<table width="100%"><tr>';
//				$list_nav .= '<td align="right"><form action="" method="post" name="per_page_results">Results Per Page<select name="per_page_settings" onchange="this.form.submit();">'.$page_options.'</select></form></td>';
				
				// if city value is empty print radius search options
				$page_radius_options = '';
				for($i = MINIMUM_RADIUS; $i <= MINIMUM_RADIUS + RADIUS_DIVISOR * 5; $i += RADIUS_DIVISOR) {
					$page_radius_options .= '<option value="'.$i.'" '.($_SESSION['set_radius'] == $i ? 'selected="selected"' : '' ).' >'.$i.'</option>';
				}
				if(empty($_GET['city'])) {
					// printed per page radius dd
					$list_nav .= '<td align="left" class="per_page_radius" style="padding-bottom: 8px;"><form action="" method="post" name="per_page_radius">Show Deals Within <select name="per_page_radius" onchange="this.form.submit();">'.$page_radius_options.'</select> Miles Of Zip <input name="zip_entry" type="text" value="'.$listing_qry->zip.'" size="5" maxlength="5" /><input name="Search" type="submit" value="SEARCH" class="search_btn" /></form></td>';
				} else {
					$cities_tbl->get_db_vars($_GET['city']);
					// printed per page radius dd
					$list_nav .= '<td align="left" class="per_page_radius" style="padding-bottom: 8px;"><form action="" method="post" name="per_page_radius">Show Deals Within <select name="per_page_radius" onchange="this.form.submit();">'.$page_radius_options.'</select> Miles Of '.$cities_tbl->city.', '.$cities_tbl->state.'</form></td>';
//					$list_nav .= '<td align="left" style="padding-bottom: 8px;"><form action="" method="post" name="results_search">Filter by keyword: <input name="cur_results_search" type="text" value="'.(isset($_SESSION['cur_results_search']) ? $_SESSION['cur_results_search'] : '').'" /><input name="Search" type="submit" value="SEARCH" class="search_btn" /></form></td>';
				}
				
				$list_nav .= '<td class="search_another_zip" align="right" style="padding-bottom: 8px;"><form action="" method="post">Filter By Category: '.$this->category_drop_down((isset($_GET['cat']) ? (int)$_GET['cat'] : '')).'</form></td>';
				$list_nav .= '</tr>
						</table>
					</td>
				</tr>'.LB;				
				// filter by alpha list
//				// second table row
//				$list_nav .= '<tr class="results_head_menu">';
//				$list_nav .= '<td valign="top" colspan="3" class="listing_pages" align="left">';
//				$list_nav .= '<table width="100%"><tr>';
////				$list_nav .= '<td valign="top" align="left" class="found_deals"><strong>Pages:</strong> '.$listing_pages.'</td>';
//				$list_nav .= '<td class="search_another_zip" align="right">';
//									
//				// if city is empty print zip search box
//				if(empty($_GET['city'])) {
//				}
//				
//				$list_nav .= '</td></tr></table></td></tr>';
				
				// third table row
				$list_nav .= '</table>';
				
				$sort_lnk = SITE_URL.$page_str.(strpos($page_str,'?cat') > 0 ? '&' : '?' ).'sort=alpha';
						
				// added 12/11/2009 to write urls for api system
				if ($api_load->status == 1) {
				  $sort_lnk = SITE_URL.'?page='.$url_nms_tbl->url_name.'&sort=alpha';
				}
				
//				$list_nav .= '<table align="center"><tr><td width="900" align="center" class="map_link" style="background-color:#fff;"><a href="'.$sort_lnk.'"><strong>Sort Listings Alphabetically</strong></a></td></tr></table>';
				$list_nav .= '</td></tr>'.LB;	
				
				$map_string = '<center class="top_map_link"><a href="javascript:void(0);" id="a" class="slide1" name="1">Display Map</a></center>';
				
				$list_output .= '<tr class="results_head_menu"><td class="listing_pages" colspan="3" align="center"><a name="page_top" id="page_top"></a><script type="text/javascript" src="'.CONNECTION_TYPE.'includes/js/map_toggle.js" ></script>
				</td></tr>'.str_replace(array('RESULTS_LISTING_TABLE_HEADER','GOOG_MAP_AREA'),array($map_string,$goog_map_lnk),$list_nav);				
			}
		}
		
		// if caching is disabled run hit count update
		// get page hit count
		$cur_cat_cnt = cat_result_cnt();
		
		// print search results
		if ($listing_qry->advert_cnt > 0) {
						  
			  $list_output .= '</table>';
							  
//			  // set row session var
//			  $_SESSION['list_row'] = $this->list_row;
		  
			  // breaks listing into selectable pages
			  $cur_page = 0;
			  $results_cnt += $_SESSION['set_per_page_res'];
			  for($i = 0;$i < $results_cnt; $i += $_SESSION['set_per_page_res']) {
				  $cur_page++;
				  
				  // move to selected page
				  $cur_selected_val = ($cur_page - 1) * $_SESSION['set_per_page_res'];
				  $end_val = ($cur_page * $_SESSION['set_per_page_res'])-1;
				  $start_val = $end_val - ($_SESSION['set_per_page_res']-1);
		  
				  // walks from selected start value to end 
				  $filtered_rows = '';
				  for($i = $start_val;$i <= $end_val; $i++) {
					  if(!empty($this->list_row[$i])) $filtered_rows[] = $this->list_row[$i];
				  }
				  
				  $list_output .= '<div'.($cur_page > 1 ? ' style="display:none;"' : '').' class="advert_listing_table" id="listing_table'.$cur_page.'">';
				  if ($cur_page > 1) {
					  $list_output .= '<div class="regular_list_head"><div class="rlh_left_corner"></div><div class="rlh_right_corner"></div><div class="header_txt">&nbsp;</div></div>'.LB;
					  $list_output .= $this->row_foot();
				  }
				  $list_output .= implode($this->row_foot(),$filtered_rows);
				  $list_output .= $this->row_foot().'<div class="regular_list_head"><div class="rlh_left_bot_corner"></div><div class="rlh_right_bot_corner"></div><div class="header_txt">&nbsp;</div></div>'.LB;
				  $list_output .= '</div>';
  
			  }
			  
			  $list_output .= '<script type="text/javascript">
			  				<!--
						  '.$listing_qry->sel_restriction_src.'
				// -->
				</script><table align="center" width="840" border="0" cellspacing="0" cellpadding="0">';
			  $list_output .= str_replace(array('RESULTS_LISTING_TABLE_HEADER','GOOG_MAP_AREA'),array('<div class="boxed_area"><a class="top_map_link" href="'.curPageURL().'#page_top">Back to Top Of Page</a></div>',''),$list_nav);
			  if ($this->view != 'all') {
				$list_output .= '<tr>
								  <td valign="top" colspan="3" align="center" style="padding: 10px;">
									This category has been viewed <span class="red">'.(ENABLE_SITE_CACHING == 1 ? '$array_cat_cnt$' : number_format($cur_cat_cnt)).'</span> times. <a href="'.OVERRIDE_SITE_URL.'new-advertiser/" class="add_business_lnk">Make sure your business can be seen!</a> 
</td>
								</tr>'.LB;
			  }
			} else {
			  $list_output .= '<tr>
								<td valign="top" colspan="3" align="center">We are sorry but no listings were found for your search. This page has been viewed <span class="red">'.(ENABLE_SITE_CACHING == 1 ? '$array_cat_cnt$' : number_format($cur_cat_cnt)).'</span> times. <a href="'.SITE_URL.'new-advertiser/">Add your business to this listing today!</a> </td>
							  </tr>
							  <tr class="results_head_menu"><td class="listing_pages" colspan="3" align="center">
							   <a href="'.SITE_URL.$page_str.(strpos($page_str,'?cat') > 0 ? '&' : '?' ).'search=reset">Reset Search Results</a></td>
								</tr>'.LB;
			}

	return $list_output;
	}
		
	// category drop down menu
	private function category_drop_down($selected_id = '') {
			global $dbh;
		
		$parent_drop_down = '<select class="category_dd" onchange="this.form.submit();" name="category">'.LB;
		
		$parent_drop_down .= '<option value="all">View All</option>';
		
		$sql_query = "SELECT
						id,
						category_name,
						parent_category_id
					 FROM
						categories
					 WHERE
						zip_id is NULL
					 AND
						parent_category_id = 0
					 ORDER BY sort_order ASC, category_name ASC
					 ;";
					 
		// store results in memcached
		$rows = db_memc_str($sql_query);
		
		foreach ($rows as $categories) {
		  $ind = '--';
		  
		  // draw child drop down
		  $parent_drop_down_child = $this->parent_dd_child_chk($categories['id'],$ind,$selected_id);
		  
		  // draw parent drop down
		  $parent_drop_down_parent = '<option value="'.$categories['id'].'" '.($selected_id == $categories['id'] ? 'selected="selected" ' : '').'>'.htmlentities($categories['category_name']).'</option>'.LB;
		  
		  $parent_drop_down .= $parent_drop_down_parent . $parent_drop_down_child;
		}
		
		$parent_drop_down .= '</select>'.LB;
		
	return $parent_drop_down;
	}
	
	// check for child categories
	private function parent_dd_child_chk($cid,$ind,$selected_id = '') {
			global $dbh;
			
		$parent_drop_down = '';
			
		$sql_query = "SELECT
						id,
						category_name,
						parent_category_id
					 FROM
						categories
					 WHERE
						zip_id is NULL
					 AND
						parent_category_id = '".$cid."'
					 ORDER BY sort_order ASC, category_name ASC
					 ;";
		
		// store results in memcached
		$rows = db_memc_str($sql_query);
		
		foreach ($rows as $categories) {
		  $parent_drop_down .= '<option value="'.$categories['id'].'" '.($selected_id == $categories['id'] ? 'selected="selected" ' : '').'>'.$ind.' '.htmlentities($categories['category_name']).'</option>'.LB;
		  $parent_drop_down .= $this->parent_dd_child_chk($categories['id'],$ind.'--');
		}
		
	return $parent_drop_down;
	}
	
	// list row footer used for value implosion
	private function row_foot() {
			
		$row_foot = '<div class="adv_listing_mid"><hr class="listing_divisor" /></div>'.LB;
			
	return $row_foot;
	}
}

?>