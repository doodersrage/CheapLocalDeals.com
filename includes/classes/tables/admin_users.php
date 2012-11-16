<?PHP

// class for interacting with admin_users table
class admin_users_table {
	public $id;
	public $username;
	public $password;
	public $allowed_access;
	// table name used throughout queries within page
	private $tbl_nme = "admin_users";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->username = NULL;
		$this->password = NULL;
		$this->allowed_access = NULL;
	}
	
	// insert new admin_users
	public function insert() {
		global $dbh;
		
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
						username,
						password,
						created,
						allowed_access
					 )
					 VALUES
					 (
						?,
						?,
						?,
						?
					 );";
				 
		$update_vals = array(
							$this->username,
							$this->password,
							$today,
							$this->allowed_access
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
	
	// update existing admin_users
	public function update() {
		global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						username = ?,
						password = ?,
						allowed_access = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->username,
							$this->password,
							$this->allowed_access,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}
		
	// write post vars to class variables
	public function get_post_vars() {
			
		$this->id = (isset($_POST['id']) ? $_POST['id'] : '');
		$this->username = (isset($_POST['username']) ? $_POST['username'] : '');
		$this->password = (!empty($_POST['password']) ? encrypt_password($_POST['password']) : $this->pull_existing_password($_POST['id']));
		$this->allowed_access = serialize($_POST['allowed_access']);
			
	}

	// check for an existing admin_users password
	public function pull_existing_password($cust_id) {
		global $dbh;
	
		$sql_query = "SELECT
						password
					 FROM
						".$this->tbl_nme."
					 WHERE
						id = ?
					  LIMIT 1;";
		$values = array($cust_id);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
			
		$password = $row['password'];

		// clear result set
		$result->free();
		
	return $password;
	}
	
	// check for existing username
	public function username_check() {
		global $dbh;
		
		$values = array();
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE username = ? ";

		$values[] = $_POST['username'];
				 
		if (!empty($_POST['id'])) {
			$sql_query .= "AND id <> ? ";
			$values[] = $_POST['id'];
		}
				 
		$sql_query .= " LIMIT 1;";
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$rowscount = $result->fetchRow(MDB2_FETCHMODE_ASSOC);

		$row_count = $rowscount['rcount'];
		
		$username_rtn = $row_count;

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	
	return $username_rtn;
	}
	
	// user login check
	public function user_login_check() {
		global $dbh;
		
		$sql_query = "SELECT
						id,
						allowed_access
					 FROM
						".$this->tbl_nme."
					 WHERE
						username = ? AND
						password = ?
					  LIMIT 1;";

		$values = array(
						$_POST['username'],
						encrypt_password($_POST['password'])
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		if ($row['id'] > 0) {
			$_SESSION['admin_id'] = $row['id'];
			$_SESSION['allowed_access'] = unserialize($row['allowed_access']);
			$this->get_db_vars($row['id']);
			
			// update admin user data
			$sql_query = "UPDATE
							".$this->tbl_nme."
						 SET
							current_session = ?,
							last_login = ?,
							login_ip = ?
						 WHERE
							id = ?
						 ;";
					 
			$update_vals = array(
								session_id(),
								date("Y-m-d"),
								$_SERVER['REMOTE_ADDR'],
								$row['id']
								);
								
			$stmt = $dbh->prepare($sql_query);					 
			$stmt->execute($update_vals);
			
		}
		
	return $row['id'];
	}
	
	// user login session check
	public function user_login_session_check() {
		global $dbh;
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE
						current_session = ? 
					 AND
						id = ?
					 AND
						login_ip = ?
					  LIMIT 1;";

		$values = array(
						session_id(),
						(isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : ''),
						$_SERVER['REMOTE_ADDR']
						);
//		print_r($values);
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
				
	return $row['rcount'];
	}
		
	// get vars from database
	public function get_db_vars($id) {
		global $dbh;
		
		$sql_query = "SELECT
						id,
						username,
						password,
						created,
						allowed_access
					 FROM
						".$this->tbl_nme."
					 WHERE
						id = ?
					  LIMIT 1;";

		$values = array(
						$id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$this->id = $row['id'];
		$this->username = $row['username'];
		$this->password = $row['password'];
		$this->created = $row['created'];
		$this->allowed_access = unserialize($row['allowed_access']);

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
}

?>