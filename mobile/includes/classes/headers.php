<?PHP
// class created to draw page header
class prnt_header {
	public $page_header_title;
	public $page_meta_description;
	public $page_meta_keywords;
	public $enable_tabs_lib = 0;
	public $no_index = 0;
	  
  // main function returns page header value
  public function print_page_header() {
				  
	  $header_text = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">".LB;
	  $header_text .= "<head>".LB;
	  $header_text .= "<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />".LB;
	  $header_text .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no\"/>".LB;
	  $header_text .= "<meta name=\"verify-v1\" content=\"".GOOG_SITE_AUTHORIZATION."\" />".LB;
	  $header_text .= "<title>".htmlentities($this->get_page_head_title())."</title>" . LB;
	  $header_text .= $this->get_meta_description().LB;
	  $header_text .= $this->get_meta_keywords().LB;
	  $header_text .= "<base href=\"".CUR_MOB_URL."\" />".LB;
	  	 
	  $header_text .= $this->def_page_js();
	   
	  $header_text .= $this->page_css();
	  // load MSIE specific style adjustments if MSIE is being used
	  
	  $header_text .= LB.'</head>'.LB;
	  
  return $header_text;
  }
  
  public function page_css() {
		$header_text = "<link rel=\"stylesheet\" type=\"text/css\" href=\"includes/template/style.css\" media=\"screen\" />" . LB;
		$header_text .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".CONNECTION_TYPE."includes/libs/star-rating/jquery.rating.css\"/>" . LB;
  return $header_text;
  }
  
  public function def_page_js() {
	  $header_text = "<script type=\"text/javascript\" src=\"".CONNECTION_TYPE."js_load.deal?js_doc[]=".urlencode("includes/libs/jquery-1.4.2.min.js")."&amp;js_doc[]=".urlencode("includes/libs/jquery-ui-1.7.2.custom.min.js")."&amp;js_doc[]=".urlencode("includes/libs/thickbox/thickbox-compressed.js")."\" ></script>".LB;
  return $header_text;
  }
  
  // prints links to javascript docs for page footers
  public function page_javascript() {
	  
	 $header_text = "<script type=\"text/javascript\" src=\"".CONNECTION_TYPE."js_load.deal?js_doc=".urlencode("includes/js/popup_box.js")."\" defer=\"defer\"></script>".LB;
	 
  return $header_text;
  }
  
  // get page header title
  public function get_page_head_title() {
	  
	  // if static page header title is assigned load that
	  if (!empty($this->page_header_title)) {
		  $set_page_header = $this->page_header_title;
	  } else {
		  $set_page_header = '';
	  }
	  
  return $set_page_header;
  }
  
  // get page meta description
  public function get_meta_description() {
	
	// if static meta description is assigned load that
	if (!empty($this->page_meta_description)) {
	  $set_page_description = $this->page_meta_description;
	} else {
	  $set_page_description = '';
	}
	
	$meta_description = !empty($set_page_description) ? "<meta name=\"description\" content=\"".str_replace('&','%amp;',$set_page_description)."\" />" : "";
	
  return $meta_description;
  }
  
  // get page meta keywords
  public function get_meta_keywords() {
	
	// if static meta value is assigned load that
	if (!empty($this->page_meta_keywords)) {
	  $set_meta_keywords = $this->page_meta_keywords;
	} else {
	  $set_meta_keywords = '';
	}
	
	$meta_keywords = !empty($set_meta_keywords) ? "<meta name=\"keywords\" content=\"".str_replace('&','%amp;',$set_meta_keywords)."\" />" : "";
	
  return $meta_keywords;
  }
}
?>