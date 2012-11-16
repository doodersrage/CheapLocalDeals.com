<?PHP

// classes used for populating results page
class results_bottom_pg {
	private $category_list, $subcat_list, $full_cat_list, $sub_count, $sort_array, $set_city, $parent_cat_cnt = array(), $sub_cat_array = array();
	
	// list categories
	public function list_categories() {
		global $dbh,$cats_tbl,$url_nms_tbl,$cities_tbl,$ste_cty_cat_tbl,$zip_cds_tbl,$stes_tbl;
		
		// assign link names
		if (!empty($_SESSION['cur_zip'])) {
			$zip_cds_tbl->search($_SESSION['cur_zip']);
			$cities_tbl->get_db_vars($zip_cds_tbl->city_id);
			$this->set_city = htmlspecialchars($cities_tbl->city);
			$set_state = htmlspecialchars($cities_tbl->state);
		}
		if (!empty($_SESSION['city'])) {
			// pull category city url name
			$cities_tbl->get_db_vars($_SESSION['city']);
			$this->set_city = htmlspecialchars($cities_tbl->city);
			$set_state = htmlspecialchars($cities_tbl->state);
		}
	
		$sql_query = "SELECT
						id
					FROM
						categories
					WHERE
						parent_category_id = 0 
					ORDER BY sort_order ASC, category_name ASC;";
		
		$rows = $dbh->queryAll($sql_query);
		
		$cur_row = 1;
		$list_cat = '';
		$this->category_list = '';
		
		foreach ($rows as $categories) {
		
			$this->subcat_list = '';
			
			$this->category_listing($categories['id']);
		
		}
		
		$this->sort_category_list();
		
		$list_cat_arr = array();
		
		foreach($this->sort_array as $id => $value) {
			$list_cat_arr[] = $this->category_list[$id]['main_cat'];
			if (!empty($this->category_list[$id]['sub_cats'])) {
				$list_cat_arr[] = $this->category_list[$id]['sub_cats'];
			}
		}
		
		// pull full state name
		$stes_tbl->lookup_by_acn($set_state);
		
		$list_cat = '<div class="cat_list_hd">Deals in '.$this->set_city.', '.$stes_tbl->state.' ('.$set_state.')</div>';
		$list_cat .= implode(' | ',$list_cat_arr);
		
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
	
	// load category list
	private function category_listing($category_id) {
		global $cats_tbl,$url_nms_tbl,$cities_tbl,$ste_cty_cat_tbl,$zip_cds_tbl;
			
		$cats_tbl->get_db_vars($category_id);
		$cities_tbl->get_db_vars($zip_cds_tbl->city_id);
		
		if ($cats_tbl->url_name != '') {
			$url_nms_tbl->get_db_vars($cats_tbl->url_name);
		}
		
		// category count
		$this->parent_cat_cnt = array();;
		$this->sub_count = '';
		
		// assign link names
		if (isset($_SESSION['cur_zip'])) {
			if($cats_tbl->url_name > 0) {
				$url_nms_tbl->get_db_vars($cats_tbl->url_name);
				$link_name = htmlspecialchars($url_nms_tbl->url_name).'/';
			} else {
				$link_name = 'sections/category_results.deal?cat='.$category_id;
			}
			$zip_cds_tbl->search($_SESSION['cur_zip']);
			$link_text = htmlspecialchars($cities_tbl->city.' '.$cats_tbl->category_name);
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
			$cities_tbl->get_db_vars($_SESSION['city']);
			$link_text = htmlspecialchars($cities_tbl->city.' '.$cats_tbl->category_name);
		}

		
		// parent cat string
		$parent_cat = '<a class="top_cat" href="'.SITE_URL.$link_name.'">'.$link_text;
		
		$sub_cat_listing = $this->sub_category_listing($category_id,$cats_tbl->category_name);
		
		$parent_cat_cnt = count($this->parent_cat_cnt);
		
		$this->category_list[] = array(
										'sub_cats' => implode(' | ',$this->sub_cat_array),
										'main_cat' => $parent_cat.'</a>',
										'sub_cat_cnt' => $this->sub_count
										);
		
		$this->sub_cat_array = array();
				
	}
	
	// load subcategory list
	private function sub_category_listing($category_id,$par_cat_name) {
		global $dbh;
	
		$sql_query = "SELECT
						id, category_name
					 FROM
						categories
					WHERE
						parent_category_id = '".$category_id."' 
					ORDER BY 
						sort_order ASC, category_name ASC;";
		
		$rows = $dbh->queryAll($sql_query);
		
		if (count($rows) > 0) {
			
			foreach($rows as $categories) {
				
				$this->sub_count++;
				
				$this->subcat_list .= $this->subcat_output($categories['id'],$par_cat_name);
				$this->sub_category_listing($categories['id'],$categories['category_name']);
			}
			
		}
	
	return $this->subcat_list;
	}
	
	// sub category output
	private function subcat_output($category_id,$par_cat_name) {
		global $cats_tbl,$url_nms_tbl,$cities_tbl,$ste_cty_cat_tbl,$zip_cds_tbl;
		
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
			$link_text = htmlspecialchars($this->set_city.' '.$cats_tbl->category_name);
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
			$link_text = htmlspecialchars($this->set_city.' '.$cats_tbl->category_name);
		}
		
		$this->sub_cat_array[] = '<a href="'.SITE_URL.$link_name.'">'.$link_text.' '.$par_cat_name.'</a>';
	}
	
}

?>