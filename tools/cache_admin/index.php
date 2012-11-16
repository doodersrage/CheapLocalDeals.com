<?PHP
// load application header
require('../../includes/application_top.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Site Cache Admin</title>
</head>
<body>
<?PHP
// pull current cache directory size
function du( $dir )
{
    $res = `du -sk $dir`;             // Unix command
    preg_match( '/\d+/', $res, $KB ); // Parse result
    $MB = round( $KB[0] / 1024, 1 );  // From kilobytes to megabytes
    return $MB;
}

/**
 * Recursively delete a directory
 *
 * @param string $dir Directory name
 * @param boolean $deleteRootToo Delete specified top-level directory as well
 */
function unlinkRecursive($dir)
{
    if(!$dh = @opendir($dir))
    {
        return;
    }
    while (false !== ($obj = readdir($dh)))
    {
        if($obj == '.' || $obj == '..')
        {
            continue;
        }

        if (!@unlink($dir . '/' . $obj))
        {
            unlinkRecursive($dir.'/'.$obj, true);
        }
    }

    closedir($dh);
   
    return;
} 

function unlinkexpiredRecursive($dir)
{
    if(!$dh = @opendir($dir))
    {
        return;
    }
    while (false !== ($obj = readdir($dh)))
    {
        if($obj == '.' || $obj == '..')
        {
            continue;
        }

		$cache_file_time = date("F d Y H:i:s.",filemtime($dir.'/'.$obj));
		if(strtotime($cache_file_time." +".SITE_CACHING_MINUTES."minutes") <= strtotime(date("F d Y H:i:s."))) {
			if (!@unlink($dir . '/' . $obj))
			{
				unlinkRecursive($dir.'/'.$obj, true);
			}
		}
    }

    closedir($dh);
   
    return;
} 

switch($_GET['action']) {
	case 'delete_cache':
		unlinkRecursive(SITE_CACHE_DIR);
		unlinkRecursive(TEMP_DIR.'thumbs/');
	break;
	case 'delete_cache_expired':
		unlinkexpiredRecursive(SITE_CACHE_DIR);
		unlinkexpiredRecursive(TEMP_DIR.'thumbs/');
	break;
}

$size_in_k = du(SITE_CACHE_DIR);
$image_size_in_k = du(TEMP_DIR.'thumbs/');

?>
<table align="center" cellpadding="4" cellspacing="4">
	<tr>
		<td align="center"><strong>
		  Site Cache Administration
		</strong></td>
	</tr>
	<tr>
	  <td align="center">Page Cache Directory Size: <?PHP echo round($size_in_k,2); ?> MB</td>
  </tr>
	<tr>
	  <td align="center">Image Cache Directory Size: <?PHP echo round($image_size_in_k,2); ?> MB</td>
  </tr>
	<tr>
	  <td align="center"><a href="?action=delete_cache">Delete Current Cache Data </a></td>
  </tr>
	<tr>
	  <td align="center"><a href="?action=delete_cache_expired">Delete Only Expired Cache Data</a> </td>
  </tr>
</table>
</body>
</html>
