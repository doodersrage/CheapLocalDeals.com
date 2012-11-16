<?PHP

// loads and gzips js docs

// open selected CSS document

$output = '';
if(!isset($_GET['type'])) $_GET['type'] = '';

function js_op() {
  global $file, $output;
  
  switch($_GET['type']){
  case 'curl':
	$curl_handle=curl_init();
	curl_setopt($curl_handle,CURLOPT_URL,$file);
	curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
	curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,TRUE);
	$output .= trim(curl_exec($curl_handle));
	curl_close($curl_handle);
  break;
  default:
	$fh = fopen($file, 'r+');
	$output .= fread($fh, filesize($file));
	fclose($fh);
  break;
  }
}

if(is_array($_GET['js_doc'])) {
  foreach($_GET['js_doc'] as $cur_file) {
	$file = $cur_file;
	js_op();
  }
} else {
  $file = $_GET['js_doc'];
  js_op();
}

// set header type
// seconds, minutes, hours, days
$expires = 60*60*24*14;
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
header('Content-type: text/javascript');
// start output buffer
ob_start("ob_gzhandler");

  echo $output;

// flush output buffer
ob_end_flush();
?>