<?PHP

// send customers and advertisers emails

// class for writing admin emails
class admin_email_frm {
  
  function customers_email() {
	global $dbh;
	
	// draw customers drop down
	$sql_query = "SELECT
					id,
					first_name,
					last_name
				 FROM
					customer_info
				 ;";
	$rows = $dbh->queryAll($sql_query);
	$customer_options = '<option value="all">Email All</option>';
	foreach($rows as $customer) {
		$customer_options .= '<option value="'.$customer['id'].'" '.($_POST['customer_id'] == $customer['id'] ? 'selected="selected"' : '').' >'.$customer['first_name'].' '.$customer['last_name'].'</option>';
	}
	
	$oFCKeditor = new FCKeditor('message_content') ;
	$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
	$oFCKeditor->Height = 400;
	$oFCKeditor->Value = $_POST['message_content'];

	$email_page = '<center>
	<form action="" method="post">
	<table align="center" width="800">
	<tr><th align="center" colspan="2"></th></tr>
	<tr><td align="right"><strong>Customer Name:</strong></td><td><select name="customer_id">'.$customer_options.'</select></td></tr>
	<tr><td align="right"><strong>Email Title:</strong></td><td><input name="email_title" type="text" size="70" maxlength="200" value="'.$_POST['email_title'].'" /></td></tr>
	<tr><td align="center" colspan="2"><strong>Message:</strong></td></tr>
	<tr><td align="center" colspan="2">'.$oFCKeditor->Create().'</td></tr>
	<tr><td align="center" colspan="2"><input name="contact_customer" type="hidden" value="1"><input name="submit" type="submit" value="Send Email"><input name="submit" type="submit" value="Preview Email"></td></tr>
	</table>
	</form>
	</center>';

  return $email_page;
  }
  
  function advertisers_email() {
	global $dbh;

	// draw customers drop down
	$advertiser_options .= gen_state_dd($_POST['state_select']);

	$select_ajax = '<script type="text/javascript"> 
					jQuery(function(){
					 jQuery("#state_select").change(function () {
						var selected_state = jQuery("#state_select").val();
						
						 $.ajax({
						   type: "POST",
						   url: "ajax_calls/city_select.deal",
						   data: "state="+selected_state,
						   success: function(msg){
							 jQuery(\'#city_select\').html(msg);
							 jQuery(\'#advertiser_select\').html(\'\');
						   }
						 });
					 });
					}); 
					</script>'.LB;
		
	$oFCKeditor = new FCKeditor('message_content') ;
	$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
	$oFCKeditor->Height = 400;
	$oFCKeditor->Value = $_POST['message_content'];

	$email_page = '<center>
	<form action="" method="post">
	<table align="center" width="800">
	<tr><th align="center" colspan="2"></th></tr>
	<tr><td align="right"><strong>State:</strong></td><td>'.$select_ajax.'<select name="state_select" id="state_select">'.$advertiser_options.'</select></td></tr>
	<tr><td align="right"><strong>City:</strong></td><td><div id="city_select"></div></td></tr>
	<tr><td align="right"><strong>Advertiser:</strong></td><td><div id="advertiser_select_bx"></div></td></tr>
	<tr><td align="right"><strong>Email Title:</strong></td><td><input name="email_title" type="text" size="70" maxlength="200" value="'.$_POST['email_title'].'" /></td></tr>
	<tr><td align="center" colspan="2"><strong>Message:</strong></td></tr>
	<tr><td align="center" colspan="2">'.$oFCKeditor->Create().'</td></tr>
	<tr><td align="center" colspan="2"><input name="contact_advertiser" type="hidden" value="1"><input name="submit" type="submit" value="Send Email"><input name="submit" type="submit" value="Preview Email"></td></tr>
	</table>
	</form>
	</center>';

  return $email_page;
  }
  
  function customers_state_email() {
	global $dbh;
		
	// draw city/state drop down
	$state_options .= gen_state_dd($_POST['state_select']);
	
	$oFCKeditor = new FCKeditor('message_content') ;
	$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
	$oFCKeditor->Height = 400;
	$oFCKeditor->Value = $_POST['message_content'];

	$email_page = '<center>
	<form action="" method="post">
	<table align="center" width="800">
	<tr><th align="center" colspan="2"></th></tr>
	<tr><td align="right"><strong>State:</strong></td><td><select name="state_select">'.$state_options.'</select></td></tr>
	<tr><td align="right"><strong>Email Title:</strong></td><td><input name="email_title" type="text" size="70" maxlength="200" value="'.$_POST['email_title'].'" /></td></tr>
	<tr><td align="center" colspan="2"><strong>Message:</strong></td></tr>
	<tr><td align="center" colspan="2">'.$oFCKeditor->Create().'</td></tr>
	<tr><td align="center" colspan="2"><input name="state_customer" type="hidden" value="1"><input name="submit" type="submit" value="Send Email"><input name="submit" type="submit" value="Preview Email"></td></tr>
	</table>
	</form>
	</center>';

  return $email_page;
  }
  
  function advertisers_state_email() {
	global $dbh;
	
	// draw city/state drop down
	$state_options .= gen_state_dd($_POST['state_select']);
	
	$oFCKeditor = new FCKeditor('message_content') ;
	$oFCKeditor->BasePath = '../includes/libs/fckeditor/' ;
	$oFCKeditor->Height = 400;
	$oFCKeditor->Value = $_POST['message_content'];

	$email_page = '<center>
	<form action="" method="post">
	<table align="center" width="800">
	<tr><th align="center" colspan="2"></th></tr>
	<tr><td align="right"><strong>State:</strong></td><td><select name="state_select">'.$state_options.'</select></td></tr>
	<tr><td align="right"><strong>Email Title:</strong></td><td><input name="email_title" type="text" size="70" maxlength="200" value="'.$_POST['email_title'].'" /></td></tr>
	<tr><td align="center" colspan="2"><strong>Message:</strong></td></tr>
	<tr><td align="center" colspan="2">'.$oFCKeditor->Create().'</td></tr>
	<tr><td align="center" colspan="2"><input name="state_advertiser" type="hidden" value="1"><input name="submit" type="submit" value="Send Email"><input name="submit" type="submit" value="Preview Email"></td></tr>
	</table>
	</form>
	</center>';

  return $email_page;
  }
  
}

?>