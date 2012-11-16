<?PHP

// document creates and modifies advertiser categories

// handles and displays categories admin page
class state_city_category_lst {
	var $bc_data = '';
	
	// generate new categories for selected city
	function gen_cats(){
		global $dbh, $cities_tbl, $stes_tbl, $ste_cty_cat_tbl;
		
		$sql_query = "SELECT
						id
					 FROM
						categories
						";

		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute();
		
		while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		  $cities_tbl->get_db_vars((int)$_GET['cid']);
		  $stes_tbl->lookup_by_acn($cities_tbl->state);
		  $ste_cty_cat_tbl->state = $stes_tbl->id;
		  $ste_cty_cat_tbl->city = $cities_tbl->id;
		  $ste_cty_cat_tbl->category = $row['id'];
		  $ste_cty_cat_tbl->insert();
		}
	}
	
	// display category listing
	function listing($message = '') {
		$cat_view = open_table_listing_form('Category Listing','view_category','','post',$message);
		$cat_view .= $this->listing_content();
		$cat_view .= close_table_form();
		
	return $cat_view;
	}
	
	function category_bg($pid) {
		global $dbh;
				
		$sql_query = "SELECT
						ca.id,
						ca.category_name,
						ca.parent_category_id
					 FROM
						categories ca
					 RIGHT JOIN 
					 	state_city_category scc
					 ON
					 	ca.id = scc.category
					 WHERE
					 	scc.city = '".$_GET['cid']."'
					 AND
						ca.id = '".$pid."';";
		
		$rows = $dbh->queryRow($sql_query);
		
		$this->bc_data[] = ' <a href="'.SITE_ADMIN_SSL_URL.'?sect=citiescategories&mode=view&cid='.$_GET['cid'].'&pid='.$rows['id'].'">'.$rows['category_name'].'</a> ';
		
		if ($rows['parent_category_id'] > 0) {
			$this->category_bg($rows['parent_category_id']);
		}
		
	}
	
	function listing_content() {
			global $dbh, $cats_tbl;
				
		$this->bc_data = '';
		if (isset($_GET['pid'])) {
			$this->category_bg($_GET['pid']);
			krsort($this->bc_data);
			
			$breadcrumbs = implode('::',$this->bc_data);
		}
		
		// table title array							
		$title_array = array(
							'Category Name',
							'Sub Categories',
							'Updated',
							);

		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// draw table header
		$searchbox_head = array('<a href="'.SITE_ADMIN_SSL_URL.'?sect=citiescategories&mode=view&cid='.$_GET['cid'].'">Home</a>'.(!empty($breadcrumbs) ? ' :: ' : '').(isset($breadcrumbs) ? $breadcrumbs : ''));
		$cat_listing = draw_table_header($searchbox_head,$table_boxes_cnt,'center');
							
		// print title boxes
		$cat_listing .= draw_table_header($title_array);
		
		$sql_query = "SELECT
						ca.id,
						ca.category_name,
						ca.parent_category_id,
						ca.last_modified,
						ca.sort_order
					 FROM
						categories ca
					 RIGHT JOIN 
					 	state_city_category scc
					 ON
					 	ca.id = scc.category
					 WHERE
					 	scc.city = '".$_GET['cid']."'
					 	";
		
		if (!empty($cats_tbl->parent_category_id)) {
		$sql_query .= "
				AND
				ca.parent_category_id = '".$cats_tbl->parent_category_id."'";
		} elseif (isset($_GET['pid'])) {
		$sql_query .= "
				AND
				ca.parent_category_id = '".$_GET['pid']."'";
		} else {
		$sql_query .= "
				AND
				ca.parent_category_id = 0 ";
		}
					
		$sql_query .= ";";
		
		$rows = $dbh->queryAll($sql_query);
		
		if(count($rows) > 0) {
		  foreach ($rows as $categories) {
		
			$row_array = array(
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=citiescategories&mode=edit&cid='.$_GET['cid'].'&ccid='.$categories['id'].'">'.$categories['category_name'].'</a>',
								$this->sub_cat_cnt($categories['id']),
								date('n/j/Y h:i:s A',strtotime($categories['last_modified'])),
								);
		
			$cat_listing .= draw_table_contect($row_array,0,'center');
		
		  }
		} else {
		  $row_array = array(
							  '<a href="'.SITE_ADMIN_SSL_URL.'?sect=citiescategories&mode=view&cid='.$_GET['cid'].'&action=gen_cat_list"><strong>Click to generate category list for new city.</strong></a>',
							  );
	  
		  $cat_listing .= draw_table_contect($row_array,3,'center');
		}
				
	return $cat_listing;
	}
	
	// find number of sub-categories
	function sub_cat_cnt($catid) {
			global $dbh;
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						categories ca
					 RIGHT JOIN 
					 	state_city_category scc
					 ON
					 	ca.id = scc.category
					 WHERE
					 	scc.city = '".$_GET['cid']."'
					 AND
						parent_category_id = '".$catid."';";
		
		$rows = $dbh->queryAll($sql_query);
		
		if ($rows[0]['rcount'] > 0) {
		$sub_cat_cnt = '<a href="'.SITE_ADMIN_SSL_URL.'?sect=citiescategories&mode=view&cid='.$_GET['cid'].'&pid='.$catid.'">'.$rows[0]['rcount'].'</a>';
		} else {
		$sub_cat_cnt = '0';
		}
		
	return $sub_cat_cnt;
	}

}

?>