<?PHP

function input_value($type,$name,$value = '') {
	
	switch ($type) {
	case 'inputbox':
		$input_text = '<input name="'.$name.'" type="text" size=="55" value="'.$value.'">';
	break;
	case 'textbox':
		$input_text = '<textarea name="'.$name.'" cols="55" rows="5">'.$value.'</textarea>';
	break;
	case 'wysiwyg':
		$oFCKeditor = new FCKeditor($name) ;
		$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
		$oFCKeditor->Height = 400;
		$oFCKeditor->Width = 625;
		$oFCKeditor->Value = $value;
		$input_text = $oFCKeditor->Create();
	break;
	case 'yndd':
		$yn_arr = array("No","Yes");
	
		$input_text = '<select name="'.$name.'">';
		
		foreach($yn_arr as $id => $value_dd) {
		$input_text .= '<option value="'.$id.'"'.($id == $value ? ' selected="selected"' : '').'>'.$value_dd.'</option>';
		}
		
		$input_text .= '</select>';
	break;
	}
	
return $input_text;
}

?>