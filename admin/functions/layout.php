<?PHP

// write page header
function page_header($content) {

	$page_header = '<div class="page_header">'.$content.'</div>'.LB;

return $page_header;
}

function open_table_form($table_name,$form_name,$action,$method = 'post',$message = '') {
	
	$table_header = '<form enctype="multipart/form-data" name="'.$form_name.'" method="'.$method.'" action="'.$action.'">'.LB;
	$table_header .= (!empty($message) ? '<center>'.$message.'</message>'.LB : '');
	$table_header .= '<table align="center" width="98%" class="admin_form">'.LB;
	$table_header .= T.'<tr>'.LB;
	$table_header .= T.T.'<th colspan="2">'.$table_name.'</th>'.LB;
	$table_header .= T.'</tr>'.LB;
	
return $table_header;
}


function open_table_listing_form($table_name,$form_name,$action = '',$method = 'post',$message = '',$colspan=5) {
	
	$table_header = '<form id="list_form" name="'.$form_name.'" method="'.$method.'" action="'.$action.'">'.LB;
	$table_header .= (!empty($message) ? '<center>'.$message.'</message>'.LB : '');
	$table_header .= '<table align="center" width="98%" class="admin_form">'.LB;
	$table_header .= T.'<tr>'.LB;
	$table_header .= T.T.'<th colspan="'.$colspan.'">'.$table_name.'</th>'.LB;
	$table_header .= T.'</tr>'.LB;
	
return $table_header;
}


function close_table_form() {
	
	$close_table = '</table>'.LB;
	$close_table .= '</form>'.LB;
	
return $close_table;
}

function table_form_field($field_title,$field_value) {
	
	$table_form_field = T.'<tr>'.LB;
	$table_form_field .= T.T.'<td class="form_title" valign="top">'.$field_title.'</td>'.LB;
	$table_form_field .= T.T.'<td class="form_field">'.$field_value.'</td>'.LB;
	$table_form_field .= T.'</tr>'.LB;
	
return $table_form_field;
}

function table_form_header($field_title) {
	
	$table_form_field = T.'<tr>'.LB;
	$table_form_field .= T.T.'<td class="form_title_header" valign="top" colspan="2">'.$field_title.'</td>'.LB;
	$table_form_field .= T.'</tr>'.LB;
	
return $table_form_field;
}

function table_span_form_field($field_value) {
	
	$table_form_field = T.'<tr>'.LB;
	$table_form_field .= T.T.'<td class="form_field" colspan="2">'.$field_value.'</td>'.LB;
	$table_form_field .= T.'</tr>'.LB;
	
return $table_form_field;
}

function table_listing_span_form_field($field_value) {
	
	$table_form_field = T.'<tr>'.LB;
	$table_form_field .= T.T.'<td class="form_field" colspan="5">'.$field_value.'</td>'.LB;
	$table_form_field .= T.'</tr>'.LB;
	
return $table_form_field;
}

// draw table header box
function draw_table_header($title_array,$colspans = 0,$align = '') {

	// determine header colspan split
	if (count($title_array) > 1 && !empty($colspans) ) {
		$colspan_split = ceil($colspans / count($title_array));
	}
	
	// set colspan val
	$max_colspans = $colspans;
	
	// reset total colspan count
	$tot_colspan = 0;

	// draw table header row
	$table_head = '<tr>'.LB;
	foreach($title_array as $id => $cur_title) {
		if (isset($colspan_split)) {
			// determine remaining colspans
			$tot_colspan += $colspan_split;
			if($max_colspans < $tot_colspan) {
				$colspans = $colspan_split - ($tot_colspan - $max_colspans);
			} else {
				// copy to colspan var
				$colspans = $colspan_split;
			}
		}
		$table_head .= '<th'.($colspans > 0 ? ' colspan="'.$colspans.'" ' : '').($align != '' ? ' align="'.$align.'" ' : '' ).'>'.$cur_title.'</th>';
	}
	$table_head .= '</tr>'.LB;

return $table_head;
}

// draw dynamic table content box
function draw_table_contect($title_array,$colspans = 0,$align = '',$class='') {

	// determine header colspan split
	if (count($title_array) > 1 && !empty($colspans) ) {
		$colspan_split = ceil($colspans / count($title_array));
	}
	
	// set colspan val
	$max_colspans = $colspans;
	
	// reset total colspan count
	$tot_colspan = 0;

	$table_head = '<tr class="admin_form_tr">'.LB;
	foreach($title_array as $cur_title) {
		if (isset($colspan_split)) {
			// determine remaining colspans
			$tot_colspan += $colspan_split;
			if($max_colspans < $tot_colspan) {
				$colspans = $colspan_split - ($tot_colspan - $max_colspans);
			} else {
				// copy to colspan var
				$colspans = $colspan_split;
			}
		}
		$table_head .= '<td'.(!empty($class) ? ' class="'.$class.'" ' : ' class="form_field" ').($colspans > 0 ? ' colspan="'.$colspans.'" ' : '').($align != '' ? ' align="'.$align.'" ' : '' ).'>'.$cur_title.'</td>';
	}
	$table_head .= '</tr>'.LB;

return $table_head;
}

?>