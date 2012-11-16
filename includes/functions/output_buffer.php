<?PHP

function draw_page_header() {

	// add page header
	$header = PAGE_HEADER;

return $header;
}

// compressed and prints page from buffer
function print_page($html) {
	global $session_warning,$categories_bot_list;

	// if template file is not loaded compress output
	if (defined('TEMPLATE_LOADED')) {
		if (TEMPLATE_LOADED != 1) {
			if (COMPRESS_PAGE_OUTPUT == 1) $html = preg_replace('/\>\s+\</', '> <', $html);
		}
	} else {
			if (COMPRESS_PAGE_OUTPUT == 1) $html = preg_replace('/\>\s+\</', '> <', $html);
	}
	
	// prints session timeout warning if customers/advertiser is logged in
	$html = str_replace('SESSION_EXP_WARNING',$session_warning,$html);

	// start output buffer
	ob_start("ob_gzhandler");
	
	if (defined('PRINT_PAGE')) {
		if(PRINT_PAGE == 0) {
			// add page header to template
			echo draw_page_header();
		}
	} else {
		echo draw_page_header();
	}
	
	// print page body
	echo $html;
	
	// flush output buffer
	ob_end_flush();
}

// load existing cache file
function load_cache_file() {
	
	define('TEMPLATE_LOADED',1);
	
	// start output buffer
	ob_start();
		
		// load template
		require(SITE_CACHE_DIR.get_cache_file_name());

		$html = ob_get_contents();
		
	ob_end_clean();
	
return $html;
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

// writes a cache file should it expire or not exist
function write_cache_file($file_name,$page_content) {

	if (COMPRESS_PAGE_OUTPUT == 1) $page_content = preg_replace('/\>\s+\</', '> <', $page_content);

	// write header to file
	$fp = fopen($file_name, 'w');
				
	// start output buffer
	ob_start();
		
		if(defined('DONOT_PRINT_HEADER')) {
			if(DONOT_PRINT_HEADER != 1) {
				// add page header to template
				echo draw_page_header();
			}
		} else {
			// add page header to template
			echo draw_page_header();
		}
		
		// draw page body
		echo $page_content;
				
		// capture outpur buffer to variable
		$theData = ob_get_contents();
	
	// close output buffer
	ob_end_clean();
		
	// write buffer to file		
	fwrite($fp, $theData.LB);
	
} 

// run cache page footer functions
function cache_page_footer($html) {
	
	// get page file name
	$cache_file_name = SITE_CACHE_DIR.get_cache_file_name();
	
	// clear file status cache for cached files
	clearstatcache();
		
	// chech for existing file
	if(file_exists($cache_file_name) === true) {
		
		$cache_file_time = date("F d Y H:i:s.",filemtime($cache_file_name));
		if(strtotime($cache_file_time." +".SITE_CACHING_MINUTES."minutes") <= strtotime(date("F d Y H:i:s."))) {
			write_cache_file($cache_file_name,$html);
		}
		
	// if file does not exist create new file
	} else {
		write_cache_file($cache_file_name,$html);
	}
	
	// clear file status cache for cached files
	clearstatcache();
	
}

// checks for existance of cache file and creates a new one should it be needed
function cache_page_header() {
	
	// get page file name
	$cache_file_name = SITE_CACHE_DIR.get_cache_file_name();

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

?>