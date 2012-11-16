<?PHP

// the functions within this script handles memcached calls

# Connect to memcache:
global $memcache;
$memcache = new Memcache;
$memcache->addServer("localhost",11211) or die ("Memcachedd connection failed!");

// new memcache functions
function chk_memche($key){
  global $memcache;
	  
  $get_result = $memcache->get(md5($key));
  if ($get_result) {
	  $found = true;
  } else {
	  $found = false;
  }
  
return $found;
}

// store values within memcached
function set_memche($key,$value){
  global $memcache;
  $memcache->set(md5($key), $value, TRUE, 60);
}

// retrieve values stored within memcached
function get_memche($key){
  global $memcache;
  $get_result = $memcache->get(md5($key));	
return $get_result;
}

// store string values in memcached
function str_memc($key,$string=''){
	
  if(chk_memche($key) == false) {
	// store new memcached value
	$results = array($string);
	if(!empty($string)) set_memche($key,$results);
	$results = $results[0];
  } else {
	// retrieve existing memcached value
	$results = get_memche($key);
	$results = $results[0];
  }
  
return $results;
}

// store database values within memcached
function db_memc_str($sql_query='',$sql_values=''){
  global $dbh;

  // set cache value id
  $cache_nme = $sql_query.serialize($sql_values);
  // check for existing cache value and if not found generate a new one 
  if(chk_memche($cache_nme) == false) {
	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute($sql_values);
	$results = array();
	while($cur_itm = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		$results[] = $cur_itm;
	}
	// clear result set
	$result->free();	
	// store new memcached value
	set_memche($cache_nme,$results);
  } else {
	// retrieve existing memcached value
	$results = get_memche($cache_nme);
  }
  // if only one result is found truncate array keys
  if(count($results) == 1) $results = $results[0];

return $results;
}
?>