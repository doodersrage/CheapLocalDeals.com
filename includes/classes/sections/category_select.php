<?PHP

// classes used for populating category_select page
class category_select_pg {
	private $category_list, $subcat_list, $full_cat_list, $sub_count, $sort_array, $last_child_id;
	public $selected_array = array();
	
	// list categories
	public function list_categories() {
		global $dbh;
	
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
			$list_cat .= '<style>
						.sub_cat_list{
							width:100%;
							border-collapse:collapse;
							}
						.sub_cat_list td{
							padding:2px;
						}
						</style>';
		
		foreach($this->sort_array as $id => $value) {
			// reset last child id
			$list_cat .= '
						<div class="category_list_box" ><script type="text/javascript">
						jQuery(function(){
						 jQuery(".sub_cat_list td").css("cursor","pointer");
						  jQuery(\'#sub_cat_list'.$id.' td\').click(function(event) {
							jQuery(this).toggleClass(\'selected\');
							if (event.target.type !== \'checkbox\') {
							  jQuery(\':checkbox\', this).trigger(\'click\');
							}
						  });
						});
						</script>
						<table class="sub_cat_list" id="sub_cat_list'.$id.'">'.LB;
									$list_cat .= $this->category_list[$id]['main_cat'];
									$list_cat .= $this->category_list[$id]['sub_cats'];
									$list_cat .= '</table></div>'.LB;
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
	
	// load category list
	private function category_listing($category_id) {
		global $cats_tbl,$url_nms_tbl,$form_write;
				
		// category count
		$this->sub_count = '';
		$this->last_child_id = '';
		
		$subcat_listing = $this->sub_category_listing($category_id);
			
		$cats_tbl->get_db_vars($category_id);
	
		// parent cat string
		$parent_cat = '<tr><td>'.(empty($this->last_child_id) ? $form_write->input_checkbox('category_select['.$category_id.']',1,$this->selected_array[$category_id]) : '').' <strong>'.htmlspecialchars($cats_tbl->category_name).'</strong></td></tr>';
		
		$this->category_list[] = array(
										'sub_cats' => $subcat_listing,
										'main_cat' => $parent_cat.LB,
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
					ORDER BY sort_order ASC, category_name ASC;";
		
		$rows = $dbh->queryAll($sql_query);
		
		if (count($rows) > 0) {
		
		$this->subcat_list = ''.LB;
		
		foreach($rows as $categories) {
			
			$this->sub_count++;
			
			$this->subcat_list .= $this->subcat_output($categories['id']);
			$this->sub_category_listing($categories['id']);
		}
	
		$this->subcat_list .= ''.LB;
	
		}
	
	return $this->subcat_list;
	}
	
	// sub category output
	private function subcat_output($category_id) {
		global $cats_tbl,$url_nms_tbl,$form_write;
		
		$cats_tbl->get_db_vars($category_id);
		
		if ($cats_tbl->url_name != '') {
			$url_nms_tbl->get_db_vars($cats_tbl->url_name);
		}
		
		$category_list = '<tr><td>'.$form_write->input_checkbox('category_select['.$category_id.']',1,$this->selected_array[$category_id]).' '.htmlspecialchars($cats_tbl->category_name).'</td></tr>'.LB;
		
		$this->last_child_id = $category_id;
	
	return $category_list;
	}
	
}

?>