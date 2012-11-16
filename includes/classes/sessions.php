<?PHP
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);

//// start session
$session_timeout_val = SESSION_TIMEOUT*60;
ini_set("session.gc_maxlifetime", $session_timeout_val);
//$cookie_path = '/';
//session_set_cookie_params($session_timeout_val, $cookie_path);
ini_set('session.save_path', TEMP_DIR.'sessions/');
session_start();

// this class handles session values
class sessions {
	public $session_exists = '';
	public $session_count = '';
	
	public function __construct() {
		$this->timeCheck();
		$this->updateStatus();
		$this->load_vars();
	}
	
	// clears current session
	function clear() {
			global $dbh;
			
		$sql_query = "DELETE FROM
						sessions
					 WHERE
						session_id = ?
					 ;";
				 
		$update_vals = array(
							session_id()
							);
		$stmt = $dbh->prepare($sql_query);					 
		$stmt->execute($update_vals);
		session_destroy();
		session_start();

	}
	
	// checks session time and kills it if timed over set value
	function timeCheck() {
			global $dbh;
			
			$sql_query = "SELECT
							id,
							time
						 FROM
							sessions
						 WHERE
							session_id = ?
						 ;";

		$update_vals = array(
							session_id()
							);

		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($update_vals);
		$rows = $result->fetchRow();
				
		if (count($rows) > 0) {
			if ((time() - (SESSION_TIMEOUT*60)) >= $rows['time']) {
				$this->clear_old();
				session_destroy();
				session_start();
			}
		}

		// clear result set
		$result->free();

	}
	
	// loads set session vars
	function load_vars() {
		global $dbh;
		
		$sql_query = "SELECT
						session_vars
					 FROM
						sessions
					 WHERE
						session_id = ?
					 ;";

		$update_vals = array(
							session_id()
							);

		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($update_vals);
		$rows = $result->fetchRow();
		
		session_decode($rows['session_vars']);

		// clear result set
		$result->free();
	}
	
	// check for existing session
	function check() {
		global $dbh;
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						sessions
					 WHERE
						session_id = ?
					 ;";

		$update_vals = array(
							session_id()
							);

		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($update_vals);
		$rows = $result->fetchRow();
		
		$this->session_exists = $rows['rcount'];

		// clear result set
		$result->free();
	}
	
	// check for existing session
	function sesCount() {
		global $dbh;
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						sessions
					 ;";
		$rows = $dbh->queryRow($sql_query);
		
		$this->session_count = $rows['rcount'];
	}
	
	// update and clear sessions
	function updateStatus() {
		global $dbh, $geo_data;
		
		$this->sesCount();
		$this->check();
		
		$pageURL = 'http';
		 if ($_SERVER['SERVER_PORT'] == 443) {$pageURL .= "s";}
		 $pageURL .= "://";
		 if ($_SERVER["SERVER_PORT"] != "80") {
		  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		 } else {
		  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		 }; 
				
		// update existsing and create new sessions
		if ($this->session_exists == 1) {
			$sql_query = "UPDATE
							sessions
						 SET
							time = ?,
							cur_page = ?,
							session_vars = ?
						 WHERE
							session_id = ?
						 ;";
					 
			$update_vals = array(
								time(),
								$pageURL,
								session_encode(),
								session_id()
								);
			$stmt = $dbh->prepare($sql_query);					 
			$stmt->execute($update_vals);
		} else {
			$sql_query = "INSERT INTO
							sessions
						 (
							time,
							session_id,
							session_vars,
							cur_page,
							geo_loc,
							ip_address,
							referrer
						 )
						 VALUES
						 (
							 ?,
							 ?,
							 ?,
							 ?,
							 ?,
							 ?,
							 ?
						 )
						 ;";
					 
			$update_vals = array(
								time(),
								session_id(),
								serialize($_SESSION),
								$pageURL,
								$geo_data->city.', '.$geo_data->region,
								$_SERVER['REMOTE_ADDR'],
								(!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '')
								);
			
			$stmt = $dbh->prepare($sql_query);					 
			$stmt->execute($update_vals);
		}
		
		$this->clear_old();		
	}
	
	function clear_old() {
			global $dbh;
			
//		 remove timed out sessions
		$sql_query = "DELETE FROM
						sessions
					 WHERE
					   time <= ?
					 ;";
				 
		$update_vals = array(
							(time() - (SESSION_TIMEOUT*60))
							);
		$stmt = $dbh->prepare($sql_query);					 
		$stmt->execute($update_vals);

	}
	
}

?>