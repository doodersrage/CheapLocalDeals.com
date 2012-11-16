<?PHP
global $customer_info_table, $adv_info_tbl, $dbh, $stes_tbl, $url_nms_tbl, $prnt_header;

// load account login page class
if (!class_exists('account_login_pg')) {
	require(CLASSES_DIR.'pages/account_login.php');
	$account_login_pg = new account_login_pg;
}

function cust_log_redir() {
	global $shopping_cart_manage;

  if ($shopping_cart_manage->contents_count > 0) {
	  header("Location: ".SITE_SSL_URL."checkout/");							
  } else {
	  header("Location: ".SITE_SSL_URL."customer_admin/");			
  }
  
}

$page_output = '<div align="center" class="login_text_bx">Are you a <a href="javascript: void(0)" onclick="jQuery(\'#cust_box\').toggle()">Customer Buying Gift Certificates</a></div>';
$page_output .= '<table border="0" align="center" class="login_frn_box" id="cust_box">';
$page_output .= '<tr><td class="frn_header">Customer Login (certificate shoppers)</td></tr>';
$page_output .= '<tr>
					<td class="frn_con">';
$page_output .= '<div class="shopping_cart_box">';
if (isset($_SESSION['customer_logged_in'])) {
  $page_output .= '<center>You appear to already be logged in.</center>';
} else {
  if (isset($_POST['submit'])) {
	if ($_POST['submit'] === 'Customer Sign In') {
		// check if customer is logged in
		if (!empty($_POST['email_address']) && !empty($_POST['password'])) {
		  
		  $customer_info_table->user_login_check();
		  if ($_SESSION['customer_logged_in'] != 1) {
			$warning .= create_warning_box('Either your e-mail address or password are invalid.');
			$page_output .= $account_login_pg->draw_login_form();		
		  } else {
			if ($_GET['mode'] == 'review') {
			  if(!empty($_SESSION['previous_page'])) {
				header("Location: ".$_SESSION['previous_page']);
			  } else {
				cust_log_redir();
			  }
			} else {
			  cust_log_redir();
			}
		  }
		  
		} else {
		  $warning .= create_warning_box('You did not provide either a email address or a password.');
		  $page_output .= $account_login_pg->draw_login_form();
		}
	} else {
	  $page_output .= $account_login_pg->draw_login_form();
	}
	
  } else {
	$page_output .= $account_login_pg->draw_login_form();
  }
}
$page_output .= '</div>';
$page_output .= '</td>
					  </tr>
					</table>
					';

// load account login page class
if (!class_exists('advertiser_login_pg')) {
  require(CLASSES_DIR.'pages/advertiser_login.php');
  $advertiser_login_pg = new advertiser_login_pg;
}

$page_output .= '<div align="center" class="login_text_bx">-----or-----</div>';
$page_output .= '<div align="center" class="login_text_bx">Are you an <a href="javascript: void(0)" onclick="jQuery(\'#advert_box\').toggle()">Advertiser on the website</a></div>';
$page_output .= '<table border="0" align="center" class="login_frn_box" id="advert_box">';
$page_output .= '<tr><td class="frn_header">Advertiser Login</td></tr>';

$page_output .= '<tr>
					<td class="frn_con">';

$page_output .= '<div class="shopping_cart_box">';
if (isset($_SESSION['advertiser_logged_in'])) {
  $page_output .= '<center>You appear to already be logged in.</center>';
} else {
  if (isset($_POST['submit'])) {
	  if ($_POST['submit'] === 'Advertiser Sign In') {
		
		// check if customer is logged in
		if (!empty($_POST['username']) && !empty($_POST['password'])) {
		  
		  $adv_info_tbl->user_login_check();
		  if ($_SESSION['advertiser_logged_in'] != 1) {
			$warning .= create_warning_box('Either the username or password provided was invalid.');
			$page_output .= $advertiser_login_pg->draw_login_form();		
		  } else {
			if($adv_info_tbl->email_authorized == 1) {
			  if($adv_info_tbl->customer_level > 0) {
				$_SESSION['just_logged_in'] = 1;
				header("Location: ".SITE_SSL_URL."advertiser_admin/");		
			  } else {
				header("Location: ".SITE_SSL_URL."advertiser_admin/create_account_user_level_select.deal");		
			  }
			} else {
			  $warning .= create_warning_box('You have not yet authorized your account. Please authorize your account before logging in.');
			  unset($_SESSION['advertiser_logged_in']);
			  unset($_SESSION['advertiser_id']);
			  unset($_SESSION['approved']);
			  unset($_SESSION['customer_level']);
			  unset($_SESSION['allow_multiple_logins']);
			  $page_output .= $advertiser_login_pg->draw_login_form();
			}
		  }
		  
	  } else {
		$warning .= create_warning_box('You did not provide either a username or a password.');
		$page_output .= $advertiser_login_pg->draw_login_form();
	  }
	  
	} else {
	  $page_output .= $advertiser_login_pg->draw_login_form();
	}
  } else {
	$page_output .= $advertiser_login_pg->draw_login_form();
  }
}
$page_output .= '</div>';
$page_output .= '</td>
					  </tr>
					</table>
					'.$warning;
$page_output .= '<div align="center" style="padding-top:25px;"><img src="images/categories.jpg" alt="" width="604" height="130"></div>';
//$page_output .= '<div class="sgn_up_txt">
//<p>Each week we\'ll email you <strong>insane   discounts</strong><span class="im_blue"> of up to 90%</span> off from local   restaurants, spas, entertainment and fun things to do and see in your city. </p>
//<a href="https://www.cheaplocaldeals.com/customer_admin/create_account.deal">Click here to Sign up.</a>
//</div>';
//$page_output .= '<div class="sgn_up_txt">
//<p><strong>FOR BUSINESS OWNERS: </strong></p>
//<p>Interested in advertising with <span class="im_blue">NO OUT OF POCKET COSTS</span>? Let us help   you attract hundreds of NEW, First time customers. It\'s fast, It\'s easy and best   of all, it\'s free.</p>
//<a href="https://www.cheaplocaldeals.com/new-advertiser/">Click here to Sign up.</a>
//</div>';
					

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Account Login Page';
$page_meta_description = 'Account Login Page';
$page_meta_keywords = 'Account Login Page';

$content_arr = array();
$content_arr['$page_output$'] = $page_output;
$this->template_constants = $content_arr;

// set page header -- only assign for static header data
$this->page_header_title = $page_header_title;
$this->page_meta_description = $page_meta_description;
$this->page_meta_keywords = $page_meta_keywords;
$this->footer_js = '';
$this->template_file = 'blank.php';

?>