<?PHP

// this class is used for writing html tables
class table_master {

	// draws a table box
	public function table($data,$other = '') {
		
		$tbl = '<table'.(!empty($other) ? ' '.$other.' ' : '').'>'.$data.'</table>';
		
		return $tbl;
	}

	// draws a table row
	public function row($data,$other = '') {
		
		$tbl = '<tr'.(!empty($other) ? ' '.$other.' ' : '').'>'.$data.'</tr>';
		
		return $tbl;
	}

	// draws a table box
	public function td($data,$other = '') {
		
		$tbl = '<td'.(!empty($other) ? ' '.$other.' ' : '').'>'.$data.'</td>';
		
		return $tbl;
	}

	// draws a table box
	public function th($data,$other = '') {
		
		$tbl = '<th'.(!empty($other) ? ' '.$other.' ' : '').'>'.$data.'</th>';
		
		return $tbl;
	}

}

?>