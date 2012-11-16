<?PHP
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

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

$key = 'IRKEY';
$test = array('this is annoying!');

set_memche($key,$test);
$ret = get_memche($key);

print_r($ret);

?>

