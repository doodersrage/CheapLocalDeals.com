<?PHP

// this class is used for writing form fields
class forms_write {
	
 // opens new form 
 public function open_form($name,$method,$action,$id,$class) {
	
	$field = '<form'.(!empty($id) ? ' id="'.$id.'"' : '').(!empty($class) ? ' class="'.$class.'"' : '').' name="'.$name.'" method="'.$method.'" action="'.$action.'">';
	
	return $field;
 }
	
	// draws an input text box
 public function input_text($name,$value = '',$size = '',$maxlength = '',$tabindex = '',$id = '') {
	 
	$field = '<input'.(!empty($tabindex) ? ' tabindex="'.$tabindex.'"' : '').' type="text" name="'.$name.'" size="'.$size.'"'.(!empty($maxlength) ? ' maxlength="'.$maxlength.'"' : '').(!empty($id) ? ' id="'.$id.'"' : '').' value="'.$value.'" />';
	
	return $field;
 }
	
	// draws an input password box
 public function input_password($name,$value = '',$size = '',$maxlength = '',$tabindex = '',$id = '') {
	 
	$field = '<input'.(!empty($tabindex) ? ' tabindex="'.$tabindex.'"' : '').' type="password" name="'.$name.'" size="'.$size.'"'.(!empty($maxlength) ? ' maxlength="'.$maxlength.'"' : '').(!empty($id) ? ' id="'.$id.'"' : '').' value="'.$value.'" />';
	
	return $field;
 }
 
 // draws an input hidden field
 public function input_hidden($name,$value) {
	
	$field = '<input name="'.$name.'" type="hidden" value="'.$value.'" />';
	
	return $field;
 }
	
	// draws an input checkbox
  public function input_checkbox($name,$value,$selected_val = '',$id = '') {
	
	$field = '<input name="'.$name.'" type="checkbox" value="1"'.(!empty($id) ? ' id="'.$id.'"' : '').' '.($selected_val == $value ? 'checked' : '').' />';
	
	return $field;
  }
  
  // draws an input text area
  public function textarea($name,$value = '',$rows = '',$cols = '',$tabindex = ''){
	  
	$field = '<textarea'.(!empty($tabindex) ? ' tabindex="'.$tabindex.'"' : '').' name="'.$name.'"'.(!empty($rows) ? ' rows="'.$rows.'"' : '').(!empty($cols) ? ' cols="'.$cols.'"' : '').' >'.$value.'</textarea>';
	
	return $field;
  }
  
  // draws an input radio button
  public function input_radio($name,$value = '',$selected_val = '',$id = '',$class = ''){
	
	$field = '<input name="'.$name.'" type="radio" value="'.$value.'"'.(!empty($id) ? ' id="'.$id.'"' : '').(!empty($class) ? ' class="'.$class.'"' : '').' '.($selected_val == $value ? 'checked' : '').' />';
	
	return $field;
  }
  
  // draws an input file field
  public function input_file($name,$tab_index = '',$id = ''){
	
	$field = '<input'.(!empty($tab_index) ? ' tabindex="'.$tab_index.'"' : '').' name="image" type="file" />';
	
	return $field;
  }
  
  // draws an input select drop down
  public function select_dd($name,$options) {
	
	$field = '<select name="'.$name.'">';
	$field .= implode('',$options);
	$field .= '</select>';
	
	return $field;
  }
  
  // draws submit button
  public function submit($name,$value,$id,$class) {
	
	$field = '<input'.(!empty($class) ? ' class="'.$class.'"' : '').(!empty($class) ? ' id="'.$id.'"' : '').' type="submit" name="'.$name.'" value="'.$value.'" />';
	
	return $field;
  }
	
}

?>