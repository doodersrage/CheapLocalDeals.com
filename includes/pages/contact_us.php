<?PHP

global $pgs_tbl, $category_results_pg, $dbh, $stes_tbl, $url_nms_tbl;

require_once(LIBS_DIR.'recaptchalib.php');

// Get a key from http://recaptcha.net/api/getkey
$privatekey = RECAPTCHA_PRIVATE_KEY;

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

// draws contact us form
function draw_contact_form() {
	global $pgs_tbl, $privatekey, $error;
	
	$publickey = RECAPTCHA_PUBLIC_KEY;

	$page_output .= '<form action="" method="post">
						<table class="frn_box" align="center">
							<tr>
								<th class="frn_header" align="right" valign="top" colspan="2">'.($pgs_tbl->display_name != 1 ? $pgs_tbl->name : '').'</th>
							<tr>
								<td class="frn_conbox" align="right" valign="top">Email Address: </td><td class="frn_conbox"><input name="email_address" type="text" size="45" value="'.$_POST['email_address'].'"></td>
							</tr>
							<tr>
								<td class="frn_conbox" align="right" valign="top">Subject: </td><td class="frn_conbox"><input name="subject" type="text" size="45" value="'.$_POST['subject'].'"></td>
							</tr>
							<tr>
								<td align="right" valign="top" class="frn_conbox">Message: </td><td class="frn_conbox"><textarea name="body" cols="40" rows="5">'.$_POST['body'].'</textarea></td>
							</tr>
							<tr>
								<td align="center" valign="top" class="frn_conbox" colspan="2"><script>
var RecaptchaOptions = {
   theme : \'clean\'
};
</script>';
	$page_output .= recaptcha_get_html($publickey, $error);
	$page_output .= '
</td>
							</tr>
							<tr>
								<td colspan="2" align="center" class="frn_conbox"><input class="submit_btn" type="submit" name="submit" value="Submit"></td>
							</tr>
						</table>
						</form>';
return $page_output;
}

// set page values
$pgs_tbl->get_db_vars(6);

// set page header -- only assign for static header data
// set header title
if ($pgs_tbl->header_title != '') {
	$page_header_title = $pgs_tbl->header_title;
} else {
	$page_header_title = DEF_PAGE_HEADER_TITLE;
}

// set meta description
if ($pgs_tbl->meta_description != '') {
	$page_meta_description = $pgs_tbl->meta_description;
} else {
	$page_meta_description = DEF_PAGE_META_DESC;
}

// set meta keywords
if ($pgs_tbl->meta_keywords != '') {
	$page_meta_keywords = $pgs_tbl->meta_keywords;
} else {
	$page_meta_keywords = DEF_PAGE_META_KEYWORDS;
}

// page output
$page_output = $pgs_tbl->header_content;

// check for submitted contact form
if(isset($_POST['subject']) && isset($_POST['body']) && isset($_POST['email_address'])) {
			
	# was there a reCAPTCHA response?
	if (isset($_POST["recaptcha_response_field"])) {
			$resp = recaptcha_check_answer ($privatekey,
											$_SERVER["REMOTE_ADDR"],
											$_POST["recaptcha_challenge_field"],
											$_POST["recaptcha_response_field"]);
	
			if ($resp->is_valid) {
//					echo "You got it!";
			} else {
					# set the error code so that we can display it
					$error = $resp->error;
			}
	}
	
	if(!empty($_POST['subject']) && !empty($_POST['body']) && !empty($_POST['email_address']) && empty($error)) {
		// assign new email class
		$message = new Mail_mime();
		
		// sends payment email to advertiser
		$html = nl2br($_POST['body']);
		
		//$message->setTXTBody($text);
		$message->setHTMLBody($html);
		$body = $message->get();
		$extraheaders = array("From"=>$_POST['email_address'], "Subject"=>$_POST['subject']);
		$headers = $message->headers($extraheaders);
		
		$mail = Mail::factory("mail");
		$mail->send(SITE_CONTACT_EMAILS, $headers, $body);
		$page_output .= '<table class="frn_box" align="center">
							<tr>
								<th class="frn_header" align="right" valign="top">'.($pgs_tbl->display_name != 1 ? $pgs_tbl->name : '').'</th>
							<tr>
								<td class="frn_conbox" align="center" valign="top">Thank you for contacting us.</td>
							</tr>
						</table>';
	} else {
		$error_message = create_warning_box('<center><strong>Email Address, Subject, and Body fields must be populated.</strong></center>');
		$page_output .= $error_message;
		$page_output .= draw_contact_form();
	}	
		
} else {
	$page_output .= draw_contact_form();
}

$page_output .= $pgs_tbl->footer_content;

$content_arr = array();
$content_arr['$page_output$'] = $page_output;
$this->template_constants = $content_arr;

// set page header -- only assign for static header data
$this->page_header_title = $page_header_title;
$this->page_meta_description = $page_meta_description;
$this->page_meta_keywords = $page_meta_keywords;
$this->template_file = 'blank-new.php';


?>