<?PHP

require_once("MDB2.php");
$dsn = array(
    'phptype'  => DB_TYPE,
    'hostspec' => DB_HOST,
    'database' => DB_NAME,
    'username' => DB_USERNAME,
    'password' => DB_PASSWORD
			);

$options = array ( 
	'persistent' => false,
	'result_buffering' => false,
 );

$dbh = MDB2::factory($dsn);
$dbh->setFetchMode(MDB2_FETCHMODE_ASSOC);

if(PEAR::isError($dbh)) {
    die("Error while connecting : " . $dbh->getMessage());
}

// disconnect from database
function db_disconnect() {
	global $dbh, $dsn;

	$dbh->disconnect();
}

// connect to database
function db_connect() {
  global $dbh, $dsn;
  
  $dbh = MDB2::factory($dsn);
  $dbh->setFetchMode(MDB2_FETCHMODE_ASSOC);
  
  if(PEAR::isError($dbh)) {
	  die("Error while connecting : " . $dbh->getMessage());
  }
  
}

function db_check_conn() {
  global $dbh, $dsn;
	
	// if connection is found cycle connection
	if(MDB2::isConnection($dbh)) {
		db_disconnect();
		db_connect();
	// if connection is not found connect to db
	} else {
		db_connect();
	}
	
}
?>