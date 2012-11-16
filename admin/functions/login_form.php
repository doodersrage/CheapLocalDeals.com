<?PHP
// functions in this page are used for logging in admin users
if (!class_exists('admin_users_table')) {
  // include admin_users_table class
  require(CLASSES_DIR.'tables/admin_users.php');
  $admin_users_table = new admin_users_table;
}

function login_form() {
  global $admin_users_table;

  $def_error_message = '<script type="text/javascript">
				  jQuery(function(){
				  jQuery(".error").fadeIn("slow");  
				  });
				  </script>
				  <div class="error">Supplied login info does not appear valid.</div>';

  // login user
  if(!empty($_POST)) {
	if(!empty($_POST['username']) && !empty($_POST['password'])) {
	  if($admin_users_table->user_login_check() > 0) {
		if($_POST['keep_logged_in'] == 1) {
		  $expire=time()+60*60*24*30;
		  setcookie("keep_logged_in", 1, $expire);		  
		  setcookie("username", $_POST['username'], $expire);		  
		  setcookie("password", $_POST['password'], $expire);	
		}
		if(isset($_COOKIE['previous_page'])) {
		  header("Location: ".$_COOKIE['previous_page']);
		} else {
		  header("Location: ".SITE_ADMIN_SSL_URL);
		}
	  } else {
		$error_message = $def_error_message;
	  }
	} else {
	  $error_message = $def_error_message;
	}
  }
  
  $login_form = (!empty($error_message) ? $error_message : '');
  $login_form .= '<script type="text/javascript" src="../includes/js/popup_box.js"></script> 
<script type="text/javascript" src="../includes/libs/dimensions_1.2/jquery.dimensions.js"></script>
<script type="text/javascript" src="../includes/libs/jquery.color.js"></script>
<script type="text/javascript">
		  
jQuery(function(){
	
	var document_height = jQuery(document).height();
	var document_width = jQuery(document).width();
	var box_height = 130;
	var new_box_height = (document_height - box_height) / 2;
	var new_error_width = (document_width - 325) / 2;
	var new_error_height = (document_height - box_height-90) / 2;

	// sets login half page
	jQuery(\'.error\').css(\'margin-top\',new_error_height+\'px\');
	jQuery(\'.error\').css(\'margin-left\',new_error_width+\'px\');
	jQuery(\'.login_form\').css(\'margin-top\',new_box_height+\'px\');

	jQuery(\'input\').fadeIn("slow");
		
    jQuery("form").submit(function() {
		jQuery(".login_form").animate({ 
		"width" : "0px", 
		"heigth" : "0px",
		"opacity" : "0"
		}, 1000, function() {
        return true;
		});  
    });

});
</script><style>
html,body {
height:100%;
}
input {
padding:4px;
border:1px solid #999999;
color:#333333;
display:none;
}
.error {
position:absolute;
top:0;
background:#FF0000;
color:#FFFFFF;
font-weight:700;
width:300px;
font-size:16px;
padding:5px;
text-align:center;
margin:0 auto;
display:none;
}
.login_form {
border:1px solid #999999;
}
.login_form td {
background:#F4F4F4;
}
.login_form th {
background:#333333;
font-weight:700;
font-size:14px;
color:#FFFFFF;
}
</style>';
  $login_form .= '<form name="login_form" method="post" action="">
  <table align="center" class="login_form">
  <tr>
	<th colspan="2" align="center">Admin Login </th>
	</tr>
  <tr>
	<td align="right">Username:</td>
	<td><input name="username" type="text" maxlength="30" /></td>
  </tr>
  <tr><td align="right">Password:</td><td><input name="password" type="password" maxlength="30" /></td></tr>
  <tr><td align="right">Keep me logged in:<br />(Up to one month.)</td><td><input name="keep_logged_in" type="checkbox" value="1" /> </td></tr>
  <tr><td align="center" colspan="2"><input id="form_submit" type="submit" name="Login" value="Login" /></td></tr>
  </table>
  </form>';

return $login_form;
}
?>