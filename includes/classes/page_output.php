<?PHP

class page_output {
  public $html;
  public $file_name;
  public $page_content;
  public $template_file;
  public $header;
  public $footer;
  public $page_header_title;
  public $page_meta_description;
  public $page_meta_keywords;
  public $footer_js;
  public $site_cont_rep = array();
  public $template_constants;
  public $page_script = '';
  private $cache_file_name;
  private $page_header;
  private $page_footer;
  
  // run class initialization routine
  public function __construct() {
	global $dbh, $stes_tbl, $url_nms_tbl, $api_load, $sessions;
	
	// start output buffer
	ob_start();
	  
	  require(TEMPLATE_DIR.'page_sects/new_advert_bx.php');
		  
	  // capture outpur buffer to variable
	  $new_advert_bx = ob_get_contents();
	
	// close output buffer
	ob_end_clean();
	
	// start output buffer
	ob_start();
	  
	  if($api_load->hide_header != 1) {
		require(TEMPLATE_DIR.'page_footer.php');
	  }
	  if($api_load->hide_footer != 1) {
		require(TEMPLATE_DIR.'cached_page_header.php'); 
	  }
				
	  // capture outpur buffer to variable
	  $footer_content = ob_get_contents();
	
	// close output buffer
	ob_end_clean();
		  
	// assign cache file name
	$this->cache_file_name = $this->get_cache_file_name();
	$this->site_cont_rep = array(
								 '$pg_footer_content$' => $footer_content,
								 '$new_advert_bx$' => $new_advert_bx,
								 'OVERRIDE_SITE_URL' => OVERRIDE_SITE_URL,
								 'SITE_URL' => SITE_URL,
								 'STD_TEMPLATE_DIR' => STD_TEMPLATE_DIR,
								 );
	$this->after_ld_constants = array(
									 '$dynamic_header_area$' => draw_dynamic_header_area(),
									 '$active_searches$' => number_format($sessions->session_count),
									  );
	
  }
  
  function proc_template() {
	  global $api_load;
	  
	// added for page cache handeling
	if (ENABLE_SITE_CACHING == 1) {
		define('PRINT_PAGE',cache_page_header());
	} else {
		define('PRINT_PAGE',0);
	}
	// draws page content
	if (PRINT_PAGE == 0) {

		require(INCLUDES_DIR.$this->page_script);
		
		// add page header to template
		$this->prnt_doc_head();

	if($api_load->status != 1) {
		// add page header to template
		$this->application_bottom();
	} else {
		$this->site_cont_rep['$new_advert_bx$'] = '';
	}
		// start output buffer
		ob_start();
			
			// load template
			require(TEMPLATE_DIR.$this->template_file);
			
			$this->html = ob_get_contents();
			
		ob_end_clean();
		
		// replace template constants
		$sit_cont_rep_keys = array_keys($this->site_cont_rep);
		$sit_cont_rep_values = array_values($this->site_cont_rep);
		$this->html = str_replace($sit_cont_rep_keys,$sit_cont_rep_values,$this->html);
							
		// write site constants to page output
		$template_constants_keys = array_keys($this->template_constants);
		$template_constants_values = array_values($this->template_constants);
		$this->html = str_replace($template_constants_keys,$template_constants_values,$this->html);
			
		$this->html = $this->page_header.$this->html.$this->footer_js.$this->page_footer;
		
		// if not set to draw new page content load cached file
	} else {
		// load existing cache file
		$this->html = $this->load_cache_file();
	}

	// added for page cache handeling
	if (ENABLE_SITE_CACHING == 1) {
		$this->cache_page_footer($this->html);
	}

	// load dynamic variables after assigning static page output
	$after_ld_constants_keys = array_keys($this->after_ld_constants);
	$after_ld_constants_values = array_values($this->after_ld_constants);
	$this->html = str_replace($after_ld_constants_keys,$after_ld_constants_values,$this->html);
	
	$this->print_page();
	
  }
  
  // assignes cache file name
  function get_cache_file_name() {
	
	$post_vars = '';
	$get_vars = '';
	
	// writes post vars to a string
	foreach($_POST as $id => $value) {
		if($id != 'image-x' || $id != 'image-y') {
			$post_vars .= preg_replace("/[^a-zA-Z0-9s]/", "", $id).'='.preg_replace("/[^a-zA-Z0-9s]/", "", $value);
		}
	}
	
	$get_vars = array();
	$final_get_vars = array();
	// writes post vars to a string
	foreach($_GET as $id => $value) {
		if(!empty($value)) $get_vars[preg_replace("/[^a-zA-Z0-9s]/", "", $id)] = preg_replace("/[^a-zA-Z0-9s]/", "", $value);
	}
	foreach($get_vars as $id => $value) {
		if(!empty($value)) $final_get_vars[] = $id.'='.$value;
	}
	$get_vars = implode('',$final_get_vars);
	
	// add extra string for view all pages
	if(isset($_GET['view'])) {
		if($_GET['view'] == 'all') {
			$get_vars .= 'curzip='.(isset($_SESSION['cur_zip']) ? $_SESSION['cur_zip'] : '');
		}
	}
	
	// clean url string
	$uri_string = $_SERVER["REQUEST_URI"];
	
	// add in newly created post and get strings
	$uri_string = preg_replace("/[^a-zA-Z0-9s]/", "", $uri_string . $get_vars . $post_vars);
	
	// set file name extenrion
	$uri_string .= '.cache';

  return $uri_string;	
  }
  
  function prnt_doc_head() {
	global $api_load, $prnt_header;
	
	$prnt_header = new prnt_header();
	if($api_load->status != 1) {
	  // assign header constant
	  $prnt_header->page_header_title = $this->page_header_title;
	  $prnt_header->page_meta_description = $this->page_meta_description;
	  $prnt_header->page_meta_keywords = $this->page_meta_keywords;
	  $this->page_header = $prnt_header->print_page_header();
	  // add page header
	  $this->page_header .= '<body>';
//		  <div class="container">';
	} else {
	  $this->page_header = $prnt_header->page_boxnohead_css();
	  $this->page_header .= $prnt_header->page_css();
	  $this->page_header .= $prnt_header->def_page_js();
	}
		
 	$this->footer_js .= $prnt_header->page_javascript();
 }
  
  // compressed and prints page from buffer
  function print_page() {
	global $session_warning,$categories_bot_list;
	
	// prints session timeout warning if customers/advertiser is logged in
	$this->html = str_replace('SESSION_EXP_WARNING',$session_warning,$this->html);

	// start output buffer
	ob_start("ob_gzhandler");
	
	// if template file is not loaded compress output
	if (defined('TEMPLATE_LOADED')) {
		if (TEMPLATE_LOADED != 1) {
			if (COMPRESS_PAGE_OUTPUT == 1) $this->html = preg_replace('/\>\s+\</', '> <', $this->html);
		}
	} else {
			if (COMPRESS_PAGE_OUTPUT == 1) $this->html = preg_replace('/\>\s+\</', '> <', $this->html);
	}
	echo $this->html;
	
	// flush output buffer
	ob_end_flush();
  }
  
  // load existing cache file
  function load_cache_file() {
	
	define('TEMPLATE_LOADED',1);
	
	// start output buffer
	ob_start();
		
		// load template
		require(SITE_CACHE_DIR.$this->cache_file_name);

		$this->html = ob_get_contents();
		
	ob_end_clean();
	
  return $this->html;
  }
  
  // writes a cache file should it expire or not exist
  function write_cache_file() {

	if (COMPRESS_PAGE_OUTPUT == 1) $this->page_content = preg_replace('/\>\s+\</', '> <', $this->page_content);

	// write header to file
	$fp = fopen($this->file_name, 'w');
				
	// start output buffer
	ob_start();
		
		// draw page body
		echo $this->html;
				
		// capture outpur buffer to variable
		$theData = ob_get_contents();
	
	// close output buffer
	ob_end_clean();
		
	// write buffer to file		
	fwrite($fp, $theData.LB);
	
  } 
  
  // run cache page footer functions
  function cache_page_footer() {
	
	// get page file name
	$cache_file_name = SITE_CACHE_DIR.$this->cache_file_name;
	
	// clear file status cache for cached files
	clearstatcache();
		
	// chech for existing file
	if(file_exists($cache_file_name) === true) {
		
		$cache_file_time = date("F d Y H:i:s.",filemtime($cache_file_name));
		if(strtotime($cache_file_time." +".SITE_CACHING_MINUTES."minutes") <= strtotime(date("F d Y H:i:s."))) {
			$this->write_cache_file($cache_file_name,$this->html);
		}
		
	// if file does not exist create new file
	} else {
		$this->write_cache_file($cache_file_name,$this->html);
	}
	
	// clear file status cache for cached files
	clearstatcache();
	
  }
  
  // checks for existance of cache file and creates a new one should it be needed
  function cache_page_header() {
	
	// get page file name
	$cache_file_name = SITE_CACHE_DIR.$this->cache_file_name;

	// clear file status cache for cached files
	clearstatcache();

	// chech for existing file
	if(file_exists($cache_file_name) === true) {
		$cache_file_time = date("F d Y H:i:s.",filemtime($cache_file_name));
		if(strtotime($cache_file_time." +".SITE_CACHING_MINUTES."minutes") <= strtotime(date("F d Y H:i:s."))) {
			$draw_page = 0;
		} else {
			$draw_page = 1;
		}
	} else {
		$draw_page = 0;
	}

	// clear file status cache for cached files
	clearstatcache();

  return $draw_page;
  }
  
  // assigns document bottom
  function application_bottom() {
	global $prnt_header, $dbh;
	  
	$this->page_footer .= '<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push([\'_setAccount\', \''.GOOG_ANALYTICS_ACC.'\']);
  _gaq.push([\'_trackPageview\']);

  (function() {
    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
	</body>
	</html>';
  }
  
}

?>