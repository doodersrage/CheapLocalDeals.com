<?PHP

// draws mobile page layout
class mobLayout {
  // vars related to header generation
  public $page_header_title;
  public $page_meta_description;
  public $page_meta_keywords;
  public $enable_tabs_lib;
  // vars related to body gen
  public $template;
  // rendered data
  public $page;

	// draws page op
	public function renderPage(){
		
		$this->page = $this->prntHead();
		$this->page .= dynaHead();
		$this->page .= $this->prntBody();
		$this->page .= "<script type=\"text/javascript\">
		document.write(unescape(\"%3Cscript src='https://www.cheaplocaldeals.com/js_load.deal?type=curl&amp;js_doc=https%3A%2F%2Fssl.google-analytics.com%2Fga.js' type='text/javascript'%3E%3C/script%3E\"));
		</script>
		<script type=\"text/javascript\">
		try {
		var pageTracker = _gat._getTracker(\"UA-8358041-1\");
		pageTracker._trackPageview();
		} catch(err) {}</script>
		</body>
		</html>";
	return $this->page;
	}
	
	// draws page header
	private function prntHead(){
	  // load sep header class
	  require_once(MOB_CLASS.'headers.php');
	  $prnt_header = new prnt_header;
	  
	  // generate header
	  $prnt_header->page_header_title = $this->page_header_title;
	  $prnt_header->page_meta_description = $this->page_meta_description;
	  $prnt_header->page_meta_keywords = $this->page_meta_keywords;
	  $prnt_header->enable_tabs_lib = $this->enable_tabs_lib;
	  $op = $prnt_header->print_page_header();
		
	return $op;
	}
	
	// draws pages body
	private function prntBody(){
		// load selected template
		ob_start();
		  
		  require(MOB_TEMPLATE.$this->template); 
					
		  // capture outpur buffer to variable
		  $tempData = ob_get_contents();
		
		// close output buffer
		ob_end_clean();
		
	return $tempData;
	}
}
?>