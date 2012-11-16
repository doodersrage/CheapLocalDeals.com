<?php
/**
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a MySQL database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurflDatabase
 * @author Steve Kamerman <stevekamerman AT gmail.com>
 * @version Stable 2.1.2 $Date: 2010/05/14 15:53:02
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 */
/**
 * Provides connectivity from Tera-WURFL to Microsoft SQL Server 2005/2008
 * @package TeraWurflDatabase
 */
class TeraWurflDatabase_MSSQL2005 extends TeraWurflDatabase{
	
	// Properties
	public $errors;
	public $db_implements_ris = true;
	public $db_implements_ld = false;
	public $numQueries = 0;
	public $connected = false;
	
	protected $dbcon;
	
	public $maxquerysize = 0;
	/**
	 * The maximum number of new rows that the database can handle in one INSERT statement
	 * @var unknown_type
	 */
	protected static $DB_MAX_INSERTS = 500;
	
	public function __construct(){
		parent::__construct();
	}
	
	// Device Table Functions (device,hybrid,patch)
	public function getDeviceFromID($wurflID){
		$this->numQueries++;
		$res = sqlsrv_query($this->dbcon,"SELECT * FROM ".TeraWurflConfig::$MERGE." WHERE deviceID=".$this->SQLPrep($wurflID)) or die($this->lastDBError());
		if(!sqlsrv_has_rows($res)){
			sqlsrv_free_stmt($res);
			throw new Exception("Tried to lookup an invalid WURFL Device ID: $wurflID");
		}
		$data = sqlsrv_fetch_array($res);
		sqlsrv_free_stmt($res);
		return unserialize($data['capabilities']);
	}
	public function getActualDeviceAncestor($wurflID){
		if($wurflID == "" || $wurflID == WurflConstants::$GENERIC)
			return WurflConstants::$GENERIC;
		$device = $this->getDeviceFromID($wurflID);
		if($device['actual_device_root']){
			return $device['id'];
		}else{
			return $this->getActualDeviceAncestor($device['fall_back']);
		}
	}
	public function getFullDeviceList($tablename){
		$this->numQueries++;
		$res = sqlsrv_query($this->dbcon,"SELECT deviceID, user_agent FROM $tablename");
		$data = array();
		if(!sqlsrv_has_rows($res)){
			sqlsrv_free_stmt($res);
			return $data;
		}
		while($row = sqlsrv_fetch_array($res)){
			$data[$row['deviceID']]=$row['user_agent'];
		}
		sqlsrv_free_stmt($res);
		return $data;
	}
	// Exact Match
	public function getDeviceFromUA($userAgent){
		$this->numQueries++;
		$query = "SELECT deviceID FROM ".TeraWurflConfig::$MERGE." WHERE user_agent=".$this->SQLPrep($userAgent);
		$res = sqlsrv_query($this->dbcon,$query);
		if(!sqlsrv_has_rows($res)){
			sqlsrv_free_stmt($res);
			return false;
		}
		$data = sqlsrv_fetch_array($res);
		sqlsrv_free_stmt($res);		
		return $data['deviceID'];
	}
	// RIS == Reduction in String (reduce string one char at a time)
	public function getDeviceFromUA_RIS($userAgent,$tolerance,UserAgentMatcher &$matcher){
		$this->numQueries++;
		$query = sprintf("EXEC TeraWurfl_RIS %s,%s,%s",$this->SQLPrep($userAgent),$tolerance,$this->SQLPrep($matcher->tableSuffix()));
		$result = sqlsrv_query($this->dbcon,$query);
		if(!$result){
			throw new Exception(sprintf("Error in DB RIS Query: %s. \nQuery: %s\n",$this->lastDBError(),$query));
			exit();
		}
		$data = sqlsrv_fetch_array($result);
		sqlsrv_free_stmt($result);
		$wurflid = $data['DeviceID'];
		return ($wurflid == 'NULL' || is_null($wurflid))? WurflConstants::$GENERIC: $wurflid;
	}
	// TODO: Implement with Stored Proc
	// LD == Levesthein Distance
	public function getDeviceFromUA_LD($userAgent,$tolerance,UserAgentMatcher &$matcher){
		throw new Exception("Error: this function (LD) is not yet implemented in MySQL");die();
		$safe_ua = $this->SQLPrep($userAgent);
		$this->numQueries++;
		//$res = sqlsrv_query($this->dbcon,"call TeraWurfl_LD($safe_ua,$tolerance)");
		// TODO: check for false
		$data = array();
		while($row = sqlsrv_fetch_array($res)){
			$data[]=$row;
		}
		sqlsrv_free_stmt($res);
		return $data;
	}
	public function loadDevices(&$tables){
		$insert_errors = array();
		$insertcache = array();
		$insertedrows = 0;
		$this->createIndexTable(TeraWurflConfig::$INDEX);
		$this->clearMatcherTables();
		$this->createProcedures();
		foreach($tables as $table => $devices){
			// insert records into a new temp table until we know everything is OK
			$temptable = $table . (self::$DB_TEMP_EXT);
			$parts = explode('_',$table);
			$matcher = array_pop($parts);
			$this->createGenericDeviceTable($temptable);
			foreach($devices as $device){
				sqlsrv_query($this->dbcon,"INSERT INTO ".TeraWurflConfig::$INDEX." (deviceID,matcher) VALUES (".$this->SQLPrep($device['id']).",".$this->SQLPrep($matcher).")");
				// convert device root to tinyint format (0|1) for db
				if(strlen($device['user_agent']) > 255){
					$insert_errors[] = "Warning: user agent too long: \"".($device['id']).'"';
				}
				$insertcache[] = sprintf("SELECT %s,%s,%s,%s,%s \n",
					$this->SQLPrep($device['id']),
					$this->SQLPrep($device['user_agent']),
					$this->SQLPrep($device['fall_back']),
					$this->SQLPrep((isset($device['actual_device_root']))?$device['actual_device_root']:''),
					$this->SQLPrep(serialize($device))
				);
				// This batch of records is ready to be inserted
				if(count($insertcache) >= self::$DB_MAX_INSERTS){
					$query = "INSERT INTO $temptable (deviceID, user_agent, fall_back, actual_device_root, capabilities) ".implode(" UNION ALL ",$insertcache);
					$res = sqlsrv_query($this->dbcon,$query) or $insert_errors[] = "DB server reported error on id \"".$device['id']."\": ".$this->lastDBError();
					$insertedrows += sqlsrv_rows_affected($res);
					sqlsrv_free_stmt($res);
					$insertcache = array();
					$this->numQueries++;
					$this->maxquerysize = (strlen($query)>$this->maxquerysize)? strlen($query): $this->maxquerysize;
				}
			}
			// some records are probably left in the insertcache
			if(count($insertcache) > 0){
				$query = "INSERT INTO $temptable (deviceID, user_agent, fall_back, actual_device_root, capabilities) ".implode(" UNION ALL ",$insertcache);
					$res = sqlsrv_query($this->dbcon,$query) or $insert_errors[] = "DB server reported error on id \"".$device['id']."\": ".$this->lastDBError();
					$insertedrows += sqlsrv_rows_affected($res);
					sqlsrv_free_stmt($res);
					$insertcache = array();
					$this->numQueries++;
					$this->maxquerysize = (strlen($query)>$this->maxquerysize)? strlen($query): $this->maxquerysize;
			}
			if(count($insert_errors) > 0){
				// Roll back changes
				// leave the temp table in the DB for manual inspection
				$this->errors = array_merge($this->errors,$insert_errors);
				return false;
			}
			$this->numQueries++;
			$this->dropTableIfExists($table);
			$this->numQueries++;
			$this->renameTable($temptable,$table);
		}
		// Create Merge Table
		$this->createMergeTable(array_keys($tables));
		return true;
	}
	/**
	 * Drops and creates the given device table
	 *
	 * @param string Table name (ex: TeraWurflConfig::$HYBRID)
	 * @return boolean success
	 */
	public function createGenericDeviceTable($tablename){
		$createtable = "CREATE TABLE [dbo].[{$tablename}](
	[deviceID] [nvarchar](128) NOT NULL,
	[user_agent] [nvarchar](255) NULL,
	[fall_back] [nvarchar](128) NULL,
	[actual_device_root] [tinyint] NULL,
	[capabilities] [ntext] NULL,
 CONSTRAINT [PK_{$tablename}] PRIMARY KEY CLUSTERED 
(
	[deviceID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]";
		$createkeys = "ALTER TABLE [dbo].[{$tablename}] ADD  CONSTRAINT [DF_{$tablename}_actual_device_root]  DEFAULT ((0)) FOR [actual_device_root]
CREATE NONCLUSTERED INDEX [IDX_{$tablename}_fall_back] ON [dbo].[{$tablename}] 
(
	[fall_back] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
CREATE NONCLUSTERED INDEX [IDX_{$tablename}_user_agent] ON [dbo].[{$tablename}] 
(
	[user_agent] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
		";
		$this->numQueries++;
		$this->dropTableIfExists($tablename);
		$this->numQueries++;
		sqlsrv_query($this->dbcon,$createtable);
		$this->numQueries++;
		sqlsrv_query($this->dbcon,$createkeys);
		return true;
	}
	/**
	 * Drops then creates all the UserAgentMatcher device tables
	 * @return boolean success
	 */
	protected function clearMatcherTables(){
		foreach(UserAgentFactory::$matchers as $matcher){
			$table = TeraWurflConfig::$DEVICES."_".$matcher;
			$this->createGenericDeviceTable($table);
		}
		return true;
	}
	/**
	 * Drops and creates the MERGE table
	 *
	 * @param array Table names
	 * @return boolean success
	 */
	public function createMergeTable($tables){
		$tablename = TeraWurflConfig::$MERGE;
		foreach($tables as &$table){$table="SELECT * FROM $table";}
		$this->createGenericDeviceTable($tablename);
		$createtable = "INSERT INTO $tablename ".implode(" UNION ALL ",$tables);
		$this->numQueries++;
		sqlsrv_query($this->dbcon,$createtable) or die("ERROR: ".$this->lastDBError());
		return true;
	}
	/**
	 * Drops and creates the given device table
	 *
	 * @param string Table name (ex: TeraWurflConfig::$INDEX)
	 * @return boolean success
	 */
	public function createIndexTable(){
		$tablename = TeraWurflConfig::$INDEX;
		$createtable = "CREATE TABLE [dbo].[{$tablename}](
	[deviceID] [nvarchar](128) NOT NULL,
	[matcher] [nvarchar](64) NOT NULL,
 CONSTRAINT [PK_{$tablename}] PRIMARY KEY CLUSTERED 
(
	[deviceID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]";
		$this->numQueries++;
		$this->dropTableIfExists($tablename);
		$this->numQueries++;
		sqlsrv_query($this->dbcon,$createtable);
		return true;
	}
	
	// Cache Table Functions
	
	// should return (bool)false or the device array
	public function getDeviceFromCache($userAgent){
		$tablename = TeraWurflConfig::$CACHE;
		$this->numQueries++;
		$res = sqlsrv_query($this->dbcon,"SELECT * FROM $tablename WHERE user_agent=".$this->SQLPrep($userAgent)) or die("Error: ".$this->lastDBError());
		if(!sqlsrv_has_rows($res)){
			sqlsrv_free_stmt($res);
			//echo "[[UA NOT FOUND IN CACHE: $userAgent]]";
			return false;
		}
		$data = sqlsrv_fetch_array($res);
		sqlsrv_free_stmt($res);
		return unserialize($data['cache_data']);
		
	}
	public function saveDeviceInCache($userAgent,$device){
		$tablename = TeraWurflConfig::$CACHE;
		$ua = $this->SQLPrep($userAgent);
		$packed_device = $this->SQLPrep(serialize($device));
		$this->numQueries++;
		$res = sqlsrv_query($this->dbcon,"INSERT INTO $tablename (user_agent,cache_data) VALUES ($ua,$packed_device)");
		if(sqlsrv_rows_affected($res) > 0){
			sqlsrv_free_stmt($res);
			return true;
		}
		sqlsrv_free_stmt($res);
		return false;
	}
	public function createCacheTable(){
		return $this->createGenericCacheTable(TeraWurflConfig::$CACHE);
	}
	public function createTempCacheTable(){
		return $this->createGenericCacheTable(TeraWurflConfig::$CACHE.self::$DB_TEMP_EXT);
	}
	protected function createGenericCacheTable($tablename){
		$createtable = "CREATE TABLE [dbo].[{$tablename}](
	[user_agent] [nvarchar](255) NOT NULL,
	[cache_data] [ntext] NOT NULL,
 CONSTRAINT [PK_{$tablename}] PRIMARY KEY CLUSTERED 
(
	[user_agent] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]";
		$this->numQueries++;
		$this->dropTableIfExists($tablename);
		$this->numQueries++;
		$test = sqlsrv_query($this->dbcon,$createtable);
		return true;
	} 
	public function rebuildCacheTable(){
		// We'll use this instance to rebuild the cache and to facilitate logging
		$rebuilder = new TeraWurfl();
		$cachetable = TeraWurflConfig::$CACHE;
		$temptable = TeraWurflConfig::$CACHE.self::$DB_TEMP_EXT;
		$this->numQueries++;
		if(!$this->tableExists($cachetable)){
			// This can only happen if the table doesn't exist
			$this->createCacheTable();
			$this->numQueries++;
			// This table must be empty, so we're finished
//			$rebuilder->toLog($query,LOG_ERR,"rebuildCacheTable");
			$rebuilder->toLog("Created empty cache table",LOG_NOTICE,"rebuildCacheTable");
			return true;
		}
		$this->numQueries++;
		$this->dropTableIfExists($temptable);
		$this->renameTable($cachetable,$temptable);
		$this->numQueries++;
		$this->createCacheTable();
		$query = "SELECT user_agent FROM $temptable";
		$this->numQueries++;
		$res = sqlsrv_query($this->dbcon,$query);
		if(!sqlsrv_has_rows($res)){
			// No records in cache table == nothing to rebuild
			$rebuilder->toLog("Rebuilt cache table, existing table was empty - this is very unusual.",LOG_WARNING,"rebuildCacheTable");
			return true;
		}
		while($dev = sqlsrv_fetch_array($res)){
			// Just looking the device up will force it to be cached
			$rebuilder->GetDeviceCapabilitiesFromAgent($dev['user_agent']);
			// Reset the number of queries since we're not going to re-instantiate the object
			$this->numQueries += $rebuilder->db->numQueries;
			$rebuilder->db->numQueries = 0;
		}
		$this->numQueries++;
		$this->dropTableIfExists($temptable);
		$rebuilder->toLog("Rebuilt cache table.",LOG_NOTICE,"rebuildCacheTable");
		return true;
	}
	// Supporting DB Functions
	
	// truncate or drop+create given table
	public function clearTable($tablename){
		if($tablename == TeraWurflConfig::$CACHE){
			$this->createCacheTable();
		}else{
			$this->createGenericDeviceTable($tablename);
		}
	}
	public function createProcedures(){
		$TeraWurfl_RIS = "CREATE PROCEDURE [dbo].[TeraWurfl_RIS] 
	@ua nvarchar(255),
	@tolerance int,
	@matcher nvarchar(64)
AS
BEGIN
SET NOCOUNT ON;

DECLARE @curlen int
DECLARE @wurflid nvarchar(128)
DECLARE @curua nvarchar(255)

SET @wurflid = NULL
SET @curlen = LEN(@ua)

WHILE @curlen >= @tolerance
BEGIN
	SET @curua = dbo.TeraWurfl_EscapeForLike(LEFT(@ua, @curlen))+'%'
	SELECT TOP 1 @wurflid=idx.DeviceID
		FROM ".TeraWurflConfig::$INDEX." idx INNER JOIN ".TeraWurflConfig::$MERGE." mrg ON idx.DeviceID = mrg.DeviceID
		WHERE idx.matcher = @matcher
		AND mrg.user_agent LIKE @curua
	IF @wurflid IS NOT NULL BREAK
	SET @curlen = @curlen - 1
END

SELECT @wurflid as DeviceID

END";
		$TeraWurfl_EscapeForLike = "CREATE FUNCTION TeraWurfl_EscapeForLike 
(
	@value nvarchar(300)
)
RETURNS nvarchar(300)
AS
BEGIN
	SET @value = REPLACE(@value,'[','[[]');
	SET @value = REPLACE(@value,'%','[%]');
	SET @value = REPLACE(@value,'_','[_]');
	RETURN @value
END";
		if($this->procedureExists('TeraWurfl_RIS')){sqlsrv_query($this->dbcon,"DROP PROCEDURE TeraWurfl_RIS");}
		sqlsrv_query($this->dbcon,$TeraWurfl_RIS);
		if($this->functionExists('TeraWurfl_EscapeForLike')){sqlsrv_query($this->dbcon,"DROP PROCEDURE TeraWurfl_EscapeForLike");}
		sqlsrv_query($this->dbcon,$TeraWurfl_EscapeForLike);
		return true;
	}
	/**
	 * Establishes connection to database (does not check for DB sanity)
	 */
	public function connect(){
		$this->numQueries++;
		$connectionInfo = array(
			"UID"=>TeraWurflConfig::$DB_USER,
			"PWD"=>TeraWurflConfig::$DB_PASS,
			"Database"=>TeraWurflConfig::$DB_SCHEMA
		);
		/* Connect using SQL Server Authentication. */
		$this->dbcon = sqlsrv_connect( TeraWurflConfig::$DB_HOST, $connectionInfo);
		if($this->dbcon === false){
			$error_array = sqlsrv_errors(SQLSRV_ERR_ALL);
			foreach($error_array as $err){$this->errors[]=$err['message'];}
			$this->connected = false;
			return false;
		}
		$this->connected = true;
		return true;
	}

	// prep raw text for use in queries (adding quotes if necessary)
	public function SQLPrep($value){
		if($value == '') $value = 'NULL';
		else if (!is_numeric($value) || $value[0] == '0') $value = "'" . str_replace("'","''",$value) . "'"; //Quote if not integer
		return $value;
	}
	protected function SQLEscapeForLike($value){
		// http://msdn.microsoft.com/en-us/library/ms179859.aspx
		$value = str_replace('[','[[]',$value);
		$value = str_replace('%','[%]',$value);
		$value = str_replace('_','[_]',$value);
		return $value;
	}
	public function getTableList(){
		$tableres = sqlsrv_query($this->dbcon,"SELECT TABLE_NAME FROM information_schema.tables WHERE Table_Type = 'BASE TABLE'");
		$tables = array();
		while($table = sqlsrv_fetch_array($tableres,SQLSRV_FETCH_NUMERIC))$tables[]=$table[0];
		sqlsrv_free_stmt($tableres);
		return $tables;
	}
	public function getMatcherTableList(){
		$tableres = sqlsrv_query($this->dbcon,"SELECT TABLE_NAME FROM information_schema.tables WHERE Table_Type = 'BASE TABLE' AND TABLE_NAME LIKE ".$this->SQLPrep($this->SQLEscapeForLike('TeraWurfl_').'%'));
		$tables = array();
		while($table = sqlsrv_fetch_array($tableres,SQLSRV_FETCH_NUMERIC))$tables[]=$table[0];
		sqlsrv_free_stmt($tableres);
		return $tables;
	}
	protected function tableExists($tablename){
		$tableres = sqlsrv_query($this->dbcon,"SELECT COUNT(TABLE_NAME) FROM information_schema.tables WHERE Table_Type = 'BASE TABLE' AND TABLE_NAME = ".$this->SQLPrep($tablename));
		$row = sqlsrv_fetch_array($tableres);
		sqlsrv_free_stmt($tableres);
		return ($row[0]>0)? true: false;
	}
	protected function functionExists($func){
		$query = "SELECT COUNT(name) FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[{$func}]') AND type in (N'FN', N'IF', N'TF', N'FS', N'FT')";
		$res = sqlsrv_query($this->dbcon,$query);
		$row = sqlsrv_fetch_array($res);
		return ($row[0]>0)? true: false;
	}
	protected function procedureExists($proc){
		$query = "SELECT COUNT(name) FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[{$proc}]') AND type in (N'P', N'PC')";
		$res = sqlsrv_query($this->dbcon,$query);
		$row = sqlsrv_fetch_array($res);
		return ($row[0]>0)? true: false;
	}
	protected function dropTableIfExists($tablename){
		if(!$this->tableExists($tablename)) return true;
		$res = sqlsrv_query($this->dbcon,"DROP TABLE $tablename");
		sqlsrv_free_stmt($res);
		return true;
	}
	protected function renameTable($from,$to){
		$query = "{CALL sp_rename( ?, ?)}";
		$params = array(
			array($from, SQLSRV_PARAM_IN),
			array($to, SQLSRV_PARAM_IN)
		);
		$res = sqlsrv_query($this->dbcon,$query,$params);
		@sqlsrv_free_stmt($res);
		return true;
	}
	//TODO: MSSQL
	public function getTableStats($table){
		$stats = array();
return $stats;
		$fields = array();
		$fieldnames = array();
		$fieldsres = sqlsrv_query($this->dbcon,"SHOW COLUMNS FROM ".$table);
		while($row = sqlsrv_fetch_array($fieldsres)){
			$fields[] = 'CHAR_LENGTH('.$row['Field'].')';
			$fieldnames[]=$row['Field'];
		}
		sqlsrv_free_stmt($fieldsres);
		$bytesizequery = "SUM(".implode('+',$fields).") AS bytesize";
		$query = "SELECT COUNT(*) AS rowcount, $bytesizequery FROM $table";
		$res = sqlsrv_query($this->dbcon,$query);
		$rows = sqlsrv_fetch_array($res);
		$stats['rows'] = $rows['rowcount'];
		$stats['bytesize'] = $rows['bytesize'];
		sqlsrv_free_stmt($res);
		if(in_array("actual_device_root",$fieldnames)){
			$res = sqlsrv_query($this->dbcon,"SELECT COUNT(*) AS devcount FROM $table WHERE actual_device_root=1");
			$row = sqlsrv_fetch_array($res);
			$stats['actual_devices'] = $row['devcount'];
			sqlsrv_free_stmt($res);
		}
		return $stats;
	}
	public function getCachedUserAgents(){
		$uas = array();
		$cacheres = sqlsrv_query($this->dbcon,"SELECT user_agent FROM ".TeraWurflConfig::$CACHE." ORDER BY user_agent");
		while($ua = sqlsrv_fetch_array($cacheres,SQLSRV_FETCH_NUMERIC))$uas[]=$ua[0];
		sqlsrv_free_stmt($cacheres);
		return $uas;
	}
	protected function lastDBError(){
		$errors = sqlsrv_errors();
		return isset($errors[0])? $errors[0]['message']: "none";
	}
	public function getServerVersion(){
		$res = sqlsrv_query($this->dbcon,"SELECT SERVERPROPERTY('productversion') AS server_version");
		$row = sqlsrv_fetch_array($res);
		sqlsrv_free_stmt($res);
		return $row['server_version'];
	}
}
