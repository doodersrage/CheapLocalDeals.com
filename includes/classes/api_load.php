<?PHP
// this class handles api requests and sets required parameters
class api_load{
	public $api_key;
	public $status = 0;
	public $name;
	public $image;
	public $website;
	public $show_address;
	public $address;
	public $address1;
	public $city;
	public $state;
	public $zip;
	public $enabled;
	public $current_page;
	public $show_all;
	public $hide_header;
	public $hide_footer;

	public function load_api() {
		global $api_acc_tbl;
		
		$api_acc_tbl->key_search($this->api_key);
		if($api_acc_tbl->id > 0) {
		  // if api access has been enabled load api data
		  $this->status = $api_acc_tbl->enabled;
		  $this->hide_header = $api_acc_tbl->hide_header;
		  $this->hide_footer = $api_acc_tbl->hide_footer;
		  $this->name = $api_acc_tbl->name;
		  $this->image = $api_acc_tbl->image;
		  $this->website = $api_acc_tbl->website;
		  $this->show_address = $api_acc_tbl->show_address;
		  $this->address = $api_acc_tbl->address;
		  $this->address1 = $api_acc_tbl->address1;
		  $this->city = $api_acc_tbl->city;
		  $this->state = $api_acc_tbl->state;
		  $this->zip = $api_acc_tbl->zip;
		  $this->show_all = $api_acc_tbl->show_all;
		  
//		  die($api_acc_tbl->show_all);
		  
		  $this->current_page = $_SERVER["REQUEST_URI"];
		  		  
		} else {
		  // API data does not match that stored within the database kill the request and inform of invalid id
		  die('API key provided does not appear to be valid!');
		}
		
	}
	
}

?>