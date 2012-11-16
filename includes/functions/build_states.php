<?PHP

// pull states info from states table
$sql_query = "SELECT
				id
			 FROM
				states
			 ;";
$states_op = db_memc_str($sql_query);

// jump through each state
foreach($states_op as $sel_state) {

	// pull current state info
	$stes_tbl->get_db_vars($sel_state['id']);
	$id = $stes_tbl->acn;
	$cur_state = $stes_tbl->state;
	
	// check for search friendly url name entry
	if($stes_tbl->url_name > 0) {
		$url_nms_tbl->get_db_vars($stes_tbl->url_name);
		$link_name = $url_nms_tbl->url_name.'/';
	} else {
		$link_name = 'state-browse/?state='.$sel_state['id'];
	}

	// print city list if state is selected
	$states_array[] = '<a href="'.SITE_URL.$link_name.'">'.$cur_state.'</a>';		
}

echo implode(' | ',$states_array);
?>