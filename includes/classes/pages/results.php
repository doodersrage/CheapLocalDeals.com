<?PHP

// classes used for populating results page
class results_pg {
	private $category_list, $subcat_list, $full_cat_list, $sub_count, $sort_array, $parent_cat_cnt = array();
	public $view_all_link, $advert_cnt = 0, $fav_lnk, $zip_array;
	
	// list categories
	public function list_categories() {
		global $dbh;
		
		$zip_string = implode(', ',$this->zip_array);
		// set memcached id value
		$listKey = md5($zip_string.'CID'.$_SESSION['city']);
		$list_cat = str_memc($listKey);
		if(empty($list_cat)){
		  $sql_query = "SELECT
						  id
					  FROM
						  categories
					  WHERE
						  parent_category_id = 0 
					 AND
					 	  disabled IS NULL
					  ORDER BY sort_order ASC, category_name ASC;";
		  
		  // store cache results in memcached
		  $rows = db_memc_str($sql_query);
				  
		  $cur_row = 1;
		  $list_cat = '';
		  $this->category_list = '';
		  
		  foreach ($rows as $categories) {
		  
			  $this->subcat_list = '';
			  
			  $this->category_listing($categories['id']);
		  
		  }
		  
		  $this->sort_category_list();
		  
		  foreach($this->sort_array as $id => $value) {
			  $list_cat .= '<div class="category_list_box" >'.LB;
			  $list_cat .= $this->category_list[$id]['main_cat'];
			  $list_cat .= $this->category_list[$id]['sub_cats'];
			  $list_cat .= '</div>'.LB;
		  }
		  // store final value in memcached
		  str_memc($listKey, $list_cat);
		}
		
	return $list_cat;
	}
	
	// sort pulled category list
	private function sort_category_list() {
		
		foreach($this->category_list as $id => $value) {
			$count_order[$id] = $value['sub_cat_cnt'];
		}
		
		arsort($count_order);
		
		reset($this->category_list);
		
		$this->sort_array = $count_order;
	}
	
	// count of items within category
	private function category_count($category_id) {
			global $dbh, $adv_info_tbl;
	
		$zip_string = implode(', ',$this->zip_array);
		
		// reset category count
		$category_count = '';
		
		// reset selected cat array
		$selected_cats = array();

		$sql_query = "SELECT
						ai.id
					 FROM
						advertiser_info ai LEFT JOIN advertiser_alt_locations aal ON ai.id = aal.advertiser_id 
						RIGHT JOIN advertiser_categories ac ON ac.advertiser_id = ai.id
					 WHERE
						ac.category_id = '".$category_id."' 
					AND ai.account_enabled = 1 
					AND ai.approved = 1 
					AND ai.update_approval = 1 
					AND (ai.zip IN (".$zip_string.") 
					OR aal.zip IN (".$zip_string."));";
//	echo $sql_query;
		// store cache results in memcached
		$rows = db_memc_str($sql_query);
		
		// added to check for payment data entry
		foreach($rows as $cur_row) {
			// added to check level selected and if payment info has been entered
			$adv_info_tbl->get_db_vars($cur_row['id']);
			if ($adv_info_tbl->customer_level != 3) {
			  if(!empty($adv_info_tbl->payment_method)) {
				$selected_cats[] = $cur_row['id'];
			  }
			} else {
				$selected_cats[] = $cur_row['id'];
			}
		}
		
		// get selected category count
		$category_count = count($selected_cats);
		
		// set total cat count
		if (count($selected_cats) > 0) {
			foreach($selected_cats as $cur_advertiser) {
				$this->parent_cat_cnt[$cur_advertiser] = $cur_advertiser;
			}
		}
	
	return $category_count;
	}
	
	// load category list
	private function category_listing($category_id) {
		global $cats_tbl,$url_nms_tbl,$cities_tbl,$ste_cty_cat_tbl, $api_load;
			
		$cats_tbl->get_db_vars($category_id);
				
		// category count
		$this->parent_cat_cnt = array();;
		$cur_count = $this->category_count($cats_tbl->id);
		$this->sub_count = '';
		
		$link_name = $this->create_link($category_id);
		
		// parent cat string
		$parent_cat = '<a class="top_cat" href="'.SITE_URL.(isset($link_name) ? $link_name : '').'">'.htmlspecialchars($cats_tbl->category_name);
		
		$sub_cat_listing = $this->sub_category_listing($category_id);
		
		$parent_cat_cnt = count($this->parent_cat_cnt);
		
		// assign cat count val
		$this->advert_cnt += $parent_cat_cnt;
		
		$this->category_list[] = array(
										'sub_cats' => $sub_cat_listing,
										'main_cat' => $parent_cat.'</a> <span class="catcount">('.$parent_cat_cnt.')</span>'.LB,
										'sub_cat_cnt' => $this->sub_count
										);
				
	}
	
	// load subcategory list
	private function sub_category_listing($category_id) {
		global $dbh;
	
		$sql_query = "SELECT
						id
					 FROM
						categories
					WHERE
						parent_category_id = '".$category_id."' 
					 AND
					 	  disabled IS NULL
					ORDER BY 
						sort_order ASC, category_name ASC;";
		
		// store cache results in memcached
		$rows = db_memc_str($sql_query);
		
		if (count($rows)) {
		
		$this->subcat_list = '<ul class="sub_cat_list">'.LB;
		
		foreach($rows as $categories) {
			
			$this->sub_count++;
			
			$this->subcat_list .= $this->subcat_output($categories['id']);
			$this->sub_category_listing($categories['id']);
		}
	
		$this->subcat_list .= '</ul>'.LB;
	
		}
	
	return $this->subcat_list;
	}
	// sub category output
	private function subcat_output($category_id) {
		global $cats_tbl,$url_nms_tbl,$cities_tbl,$ste_cty_cat_tbl, $api_load;
		
		$cats_tbl->get_db_vars($category_id);
		
		$link_name = $this->create_link($category_id);
		
		$category_list = '<li><a href="'.SITE_URL.(isset($link_name) ? $link_name : '').'">'.htmlspecialchars($cats_tbl->category_name).'</a> <span class="catcount">('.$this->category_count($cats_tbl->id).')</span></li>'.LB;
	
	return $category_list;
	}
	
	// added 12/11/2009 builds link used within listing page
	private function create_link($category_id) {
		global $cats_tbl,$url_nms_tbl,$cities_tbl,$ste_cty_cat_tbl, $api_load;
		
		$cats_tbl->get_db_vars($category_id);
		
		if ($cats_tbl->url_name != '') {
			$url_nms_tbl->get_db_vars($cats_tbl->url_name);
		}
		
		// assign link names
		if (isset($_SESSION['cur_zip'])) {
			if($cats_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($cats_tbl->url_name);
				$link_name = htmlspecialchars($url_nms_tbl->url_name).'/';
			} else {
				$link_name = 'sections/category_results.deal?cat='.$category_id;
			}
			$this->view_all_link = 'view-all-results/';
			$this->fav_lnk = 'deals-of-the-month/';
		}
		if (isset($_SESSION['city'])) {
		
			// pull category city url name
			$ste_cty_cat_tbl->city_category_search($_SESSION['city'],$category_id);
			
			if($ste_cty_cat_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($ste_cty_cat_tbl->url_name);
				$link_name = $url_nms_tbl->url_name.'/';
			} else {
				$link_name = 'sections/category_results.deal?cat='.$category_id.'&city='.$cities_tbl->id;
			}
			$this->view_all_link = 'view-all-results/'. strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $cities_tbl->city.' '.$cities_tbl->state)).'/'.$cities_tbl->id.'/';
			if($api_load->status != 1) {
			  $this->fav_lnk = 'deals-of-the-month/'. strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $cities_tbl->city.' '.$cities_tbl->state)).'/'.$cities_tbl->id.'/';
			} else {
			  $this->fav_lnk = '?page=deals-of-the-month&city='.$cities_tbl->id;
			}
		}
		
		// added 12/11/2009 to write urls for api system
		if ($api_load->status == 1) {
		
			// pull category city url name
			$ste_cty_cat_tbl->city_category_search($_SESSION['city'],$category_id);
			
			if($ste_cty_cat_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($ste_cty_cat_tbl->url_name);
				$link_name = '?page='.urlencode($url_nms_tbl->url_name);
			} else {
				$link_name = '';
			}
			$this->view_all_link = '?page=view-all&city='.$cities_tbl->id;
		}

	return $link_name;
	}
	
	
}

?>