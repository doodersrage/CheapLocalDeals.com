<?PHP
require_once('appTop.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CLD Affiliate Trackinig System</title>
<!-- CSS -->
<link href="style/css/transdmin.css" rel="stylesheet" type="text/css" media="screen" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" media="screen" href="style/css/ie6.css" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="style/css/ie7.css" /><![endif]-->
<!-- JavaScripts-->
<link type="text/css" href="style/css/ui-lightness/jquery-ui-1.8.2.custom.css" rel="stylesheet" />
<script type="text/javascript" src="style/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="style/js/jquery-ui-1.8.2.custom.min.js"></script>
<script type="text/javascript" src="style/js/jNice.js"></script>
</head>
<body>
<div id="wrapper">
  <!-- h1 tag stays for the logo, you can use the a tag for linking the index page -->
  <h1><a href="#"><span>Cheap Local Deals</span></a></h1>
<?PHP
if(!empty($error)){
	echo '<div id="error">'.$error.'</div>';
}
if(!$_SESSION['logged_in']){
?>
  <div id="containerHolder">
    <div id="container">
      <div id="main">
<?PHP
  require_once('sections/loginFRM.php');
?>
      </div>
      <!-- // #main -->
      <div class="clear"></div>
    </div>
    <!-- // #container -->
  </div>
  <!-- // #containerHolder -->
<?PHP
} else {
  require_once('sections/topnav.php');
?>
  <div id="containerHolder">
    <div id="container">
<?PHP
  require_once('sections/leftnav.php');
  require_once('sections/bc.php');
?>
      <div id="main">
<?PHP
  require_once('procs/sectHndl.php');
?>
      </div>
      <!-- // #main -->
      <div class="clear"></div>
    </div>
    <!-- // #container -->
  </div>
  <!-- // #containerHolder -->
<?PHP
}
?>
</div>
<!-- // #wrapper -->
</body>
</html>
