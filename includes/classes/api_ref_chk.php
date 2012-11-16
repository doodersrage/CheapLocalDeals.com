<?PHP
// this class checks the referrer variable and if it matches those of an API assignment it then facilitates capturing of the users whereabouts.
class api_ref_chk {
  public $ref;
  public $api_id;
  
  // initiates referrer checking and calls other functions should it be needed
  public function __construct() {
	  global $api_ref_track_tbl, $api_acc_tbl, $geo_data, $customer_info_table;
	  
	  $not_allowed = array(
						 'gif',
						 'jpg',
						 'jpeg',
						 'js'
						 );
	  
	  // gather referrer data
	  $ref = $this->gt_cln_ref();
	  // if referrer data is found then process the request
	  if(!empty($ref)) {
		  // check for ref dom in api users table
		  $api_acc_tbl->reset_vars();
		  $api_acc_tbl->api_usr_dom_get($ref);
//		  die($ref);
		  if($api_acc_tbl->id > 0){
			  if(!in_array(strtolower(findexts(curPageURL())),$not_allowed) && !stristr(curPageURL(),'goog_maps')){
				// if api user domain is found then initiate user data capture
				$api_ref_track_tbl->api_id = $api_acc_tbl->id;
				$api_ref_track_tbl->sess_id = session_id();
				$api_ref_track_tbl->customer_id = $customer_info_table->id;
				$api_ref_track_tbl->ref_page = $_SESSION['HTTP_REFERER'];
				$api_ref_track_tbl->page = curPageURL();
				$api_ref_track_tbl->cart = serialize($_SESSION['cart_contents']);
				$api_ref_track_tbl->cart_total = (isset($shopping_cart_manage) ? $shopping_cart_manage->sub_total : 0);
				$api_ref_track_tbl->longit = ($customer_info_table->id > 0 ? $customer_info_table->longitude : $geo_data->longitude);
				$api_ref_track_tbl->latt = ($customer_info_table->id > 0 ? $customer_info_table->latitude : $geo_data->latitude);
				$api_ref_track_tbl->insert();
			  }
//	  die(print_r($api_ref_track_tbl));
			  
			  $this->api_id = $api_acc_tbl->id;
		  }
	  }
  }
  
  private function gt_cln_ref(){

	// check for set referrer status
	if(!empty($_SERVER['HTTP_REFERER']) || !empty($_SESSION['HTTP_REFERER'])){
	  
	  // if server referrer variable is assigned set a session variable to easily track customer actions
	  if(!empty($_SERVER['HTTP_REFERER']) && stristr($_SERVER['HTTP_REFERER'],"//www.cheaplocaldeals.com") == false) {
	  	$_SESSION['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
	  } else {
	  	$_SESSION['HTTP_REFERER'] = 'NA';
	  }
	  
	  // assign ref var
	  $ref = $_SESSION['HTTP_REFERER'];
	  
	  // get domain only
	  $ref = $this->getdomain($ref);
	} else {
	  // if referrer is not set then assign a blank referrer value
	  $ref = '';
	}
	
  return $ref;
  }
  
  // gather domain info only
  private function getdomain($url) {
	 $url = str_replace("http://", "", str_replace("https://", "", $url));
	 $url = substr($url, 0, strpos($url, "/"));
	 return $url;
  }
	
}
?>