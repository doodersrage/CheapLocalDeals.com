<?PHP

// check if customer is logged in
if ($_SESSION['customer_logged_in'] != 1 || count($shopping_cart_manage->contents) == 0) {
	header("Location: ".MOB_URL."checkout");
}

$error = '';
	
// check for order submission
if ($_POST['submit'] === 'Process Order') {
	
	$customer_info_table->get_db_vars($_SESSION['customer_id']);
	
	// if order total equals zero do not check payment information
	if (get_order_total() > 0) {
		  
	  // check if cc type is set
	  if (empty($_POST['credit_card_type'])) {
		  $error .= 'You have not chosen a credit card type.<br>';
	  }
	  
	  // check credit card value submission length
	  $cc_number = str_replace('-','',$_POST['cc_number']);
	  $cc_number = str_replace(' ','',$cc_number);
	  
	  if (strlen($cc_number) < 15) {
		  $error .= 'Credit Card number does not appear to be valid.<br>';
	  }
	  
	  // checks credit card date
	  if($_POST['cc_exp_month'] < date('n') && $_POST['cc_exp_year'] == date('Y')) {
		  $error .= 'Your credit card expiration date appears to be invalid.<br>';
	  }
	  
	  if(empty($_POST['cvv'])) {
		  $error .= 'Card verification value has been left blank.<br>';
	  }
	  
	  if(!empty($_POST['promo_code']) && $cust_promo_cds_tbl->promo_code_chk($_POST['promo_code']) == 0) {
		  $error .= 'The promo code entered does not appear to be valid.<br>';
	  }
	  
	  if(empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['address_1']) || empty($_POST['city']) || empty($_POST['state']) || empty($_POST['zip']) || empty($_POST['phone_number'])){
		  $error .= 'Some billing information appears to be missing.<br>';
	  } else {
		  $customer_info_table->first_name = $_POST['first_name'];
		  $customer_info_table->last_name = $_POST['last_name'];
		  $customer_info_table->address_1 = $_POST['address_1'];
		  $customer_info_table->address_2 = $_POST['address_2'];
		  $customer_info_table->city = $_POST['city'];
		  $customer_info_table->state = $_POST['state'];
		  $customer_info_table->zip = $_POST['zip'];
		  $customer_info_table->phone_number = $_POST['phone_number'];
		  $customer_info_table->update();
	  }
	}
	
//	if(empty($_POST['agree_check'])) {
//		$error .= 'You did not agree to the GIFT CERTIFICATE TERMS & CONDITIONS.<br>';
//	}
	
	// submit order if no errors were found
	if (empty($error)) {
		require(MOB_FUNC.'orderProc.php');
	}
}

if ($shopping_cart_manage->contents_count > 0) {
  $page_val = '<form action="" method="post" name="shopping_cart_form">'.$error.'
  				<table class="shopping_cart_table">';
  // assign table header values
  $header_array = array('Location','Amt/Price','Quant','Del','Total');
  
  // set list rows to be used in other functions
  $list_rows = count($header_array);
  
  $list_head = '<tr>'.LB;
  
  foreach($header_array as $value) {
  $list_head .= '<th class="shop_header">'.$value.'</th>';
  }
  
  $list_head .= '</tr>'.LB;
  $page_val .= $list_head; 
  
  // make sure contents arrays is at first key value
  reset($shopping_cart_manage->contents);
  
  $list_array = array();
  $list_contents = '';
  
  // write listing array
  foreach($shopping_cart_manage->contents as $id => $value) {
	  
	  $quantity_box = $table_master->table(
						  $table_master->row(
							  $table_master->td('<input class="cart_quan_ent" name="quantity_items['.$id.']" id="quantity_items'.$id.'" type="text" size="2" maxlength="2" value="'.$value['item_quantity'].'">','rowspan="2"').
							  $table_master->td('<a onclick="update_quant(\'quantity_items'.$id.'\',1);" href="javascript: void(0);"><img border="0" src="includes/template/images/up_arrow.gif"></a>')).
						  $table_master->row(
							  $table_master->td('<a onclick="update_quant(\'quantity_items'.$id.'\',-1);" href="javascript: void(0);"><img border="0" src="includes/template/images/down_arrow.gif"></a>'))
						  );
	  
	  // get company info
	  $adv_info_tbl->get_db_vars($value['company_id']);
	  
	  $list_array[] = array('<a href="'.MOB_URL.'?action=adview&aid='.$adv_info_tbl->id.'">'.$value['company_image'].'<br/>'.$value['company_name'].'</a>', $value['item_name'] .'<br/>$'.$value['item_price'],$quantity_box,'<input name="delete_item[]" type="checkbox" value="'.$id.'">','<strong>$'.format_currency($value['item_total']).'</strong>');
  }
		  
  foreach($list_array as $value) {
	  $list_contents .= '<tr>'.LB;
	  foreach($value as $row) {
		  $list_contents .= '<td class="frn_conbox" align="center">'.$row.'</td>';
	  }
	  $list_contents .= '</tr>'.LB;
  }
  
  $page_val .= $list_contents; 

  $page_val .= '<tr><td colspan="'.$list_rows.'" align="right" class="frn_conbox"> <div class="subtotal">';

  // draws totals area
  $page_val .= draw_order_totals();

  // if order total is zero allow processing without payment data entry
  if (get_order_total() > 0) {
	$cctype_dd = '';
	$field_type_array = unserialize(CC_TYPES);
	foreach ($field_type_array as $id) {
		$cctype_dd .= '<option value="'.$id.'" '.($id == $_POST['credit_card_type'] ? 'selected="selected"' : '').'>'.$id.'</option>'.LB; 
	}
	$set_expiration = explode("/",$_POST['cc_exp']);
	
	// print exp dd
	$years_dd = '';
	$cur_year = date("Y");
	$fut_year = $cur_year+10;
	while($cur_year <= $fut_year) {
		$years_dd .= '<option '.($_POST['cc_exp_year'] == $cur_year ? 'selected' : '').'>'.$cur_year.'</option>';
		$cur_year++;
	}

	$month = 1;
	$months_dd = '';
	while($month <= 12) {
		$months_dd .= '<option '.($_POST['cc_exp_month'] == $month ? 'selected' : '').'>'.$month.'</option>';
		$month++;
	}

	$page_val .= '<tr><td colspan="'.$list_rows.'" class="shopping_box" align="center">';
	
	$states_dd = gen_state_dd($customer_info_table->state);
	
	$page_val .= '<table class="credit_card_entry" align="right">
	  <tr>
		<th class="shop_header" align="center" colspan="2">Billing Address</th>
	  </tr>
	  <tr>
		<td align="center" colspan="2">We would love for you to keep this information to yourself<br /> but unfortunately our credit card processor requires it.</td>
	  </tr>
	  <tr>
		<td><table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td valign="top">First Name:<span class="newuser_required">*</span><br />
			  
			  <input name="first_name" value="'.$customer_info_table->first_name.'" type="text" id="first_name" size="30" maxlength="100" />
			  <br />
			  Last Name:<span class="newuser_required">*</span><br />
			  <input name="last_name" value="'.$customer_info_table->last_name.'" type="text" id="last_name" size="30" maxlength="100" />          </td>
		  </tr>
		</table></td>
		</tr>
	  <tr>
		<td><table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td>Address 1:<span class="newuser_required">*</span><br />
				<input name="address_1" value="'.$customer_info_table->address_1.'" type="text" id="address_1" size="30" maxlength="120" />        </td>
			<td>Address 2:<br />
			  <input name="address_2" value="'.$customer_info_table->address_2.'" type="text" id="address_2" size="30" maxlength="120" /></td>
			</tr>
		</table></td>
		</tr>
	  <tr>
		<td><table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td>City:<span class="newuser_required">*</span><br />
				<input name="city" value="'.$customer_info_table->city.'" type="text" id="city" size="30" maxlength="100" />        </td>
			<td>State:<span class="newuser_required">*</span><br />
				<select name="state" id="state">
							'.$states_dd.'
							</select>        </td>
			<td>Zip:<span class="newuser_required">*</span><br />
				<input name="zip" value="'.$customer_info_table->zip.'" type="text" id="zip" size="5" maxlength="5" />        </td>
		  </tr>
		</table></td>
	  </tr>
	  <tr>
		<td><table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td valign="top">Phone Number:<span class="newuser_required">*</span><br />
			  
			  <input name="phone_number" value="'.$customer_info_table->phone_number.'" type="text" id="phone_number" size="15" maxlength="15" />          </td>
		  </tr>
		</table></td>
		</tr>
	</table>';
					  
	$page_val .= '<table class="credit_card_entry" align="right">
				<tr><th class="shop_header" align="center" colspan="2"><strong>Payment Options</strong></th></tr>
				<tr><td align="right">Credit Card Type:</td><td align="left"><select name="credit_card_type">'.$cctype_dd.'</select></td></tr>
				<tr><td align="right">Credit Card Number:</td><td align="left"><input name="cc_number" type="text" size="20" maxlength="20" value="'.(isset($_POST['cc_number']) ? $_POST['cc_number'] : '').'"></td></tr>
				<tr><td align="right"><script type="text/javascript" src="includes/libs/jquery.popupwindow.js"></script>
				<script type="text/javascript" src="includes/js/popwindow.js"></script><a id="cert_req_link" href="includes/popups/cvvinfo.html?popupwindow" class="popupwindow" rel="height:500,width:750,toolbar:0,scrollbars:0,status:0,resizable:0,left:150,top:100">Where is this?</a> CVV:</td><td align="left"><input name="cvv" type="text" size="4" maxlength="4" value="'.(isset($_POST['cvv']) ? $_POST['cvv'] : '').'"></td></tr>
				<tr><td align="right">Expiration:</td><td>Month:<select name="cc_exp_month">'.$months_dd.'</select> / Year: <select name="cc_exp_year">'.$years_dd.'</select></td></tr>
				</table>';
				
				
	$page_val .= '<table class="credit_card_entry" align="right">
				<tr><th class="shop_header" align="center" colspan="2"><strong>Promo Code</strong></th></tr>
				<tr><td>Entering a valid promo code will allow<br /> you save money on your purchase.</td></tr>
				<tr><td align="center"><input name="promo_code" type="text" size="10" maxlength="10" value="'.(isset($_SESSION['promo_code']) ? $_SESSION['promo_code'] : '').'"></td></tr>
				</table>
				</td></tr>';
  }
  
//			$page_val .= '<tr><td colspan="'.($this->list_rows).'"><table><tr><td align="center" valign="top" class="shopping_box" '.(!isset($_POST['agree_check']) ? 'bgcolor=""' : '').' ><div class="terms_agree">I agree to the terms and conditions listed below <input name="agree_check" type="checkbox" value="1" '.($_POST['agree_check'] == 1 ? 'checked' : '').' /></div></td></tr><tr><td class="shopping_box"><iframe width="880" height="170" src="includes/popups/cert_agree_popup.deal"></iframe></td></tr>
//			</table></td></tr>';
  
  $page_val .= '<tr><td colspan="'.$list_rows.'" class="shopping_box" align="center"><input class="submit_btn" name="submit" type="submit" value="Process Order"></td></tr>';
  
  $page_val .= '</table></form>';
  
  $page_val .= '<script type="text/javascript">
  function update_quant(field_name,amt) {
	var field_value = parseFloat(jQuery(\'#\'+field_name).val());
	var update_value = parseFloat(amt);
	var new_amt = field_value+update_value;
	
	if(field_value !== 0 && update_value !== 0) {
	  jQuery(\'#\'+field_name).val(new_amt);
	} else if(field_value == 0 && update_value > 0) {
	  jQuery(\'#\'+field_name).val(new_amt);
	}
}
jQuery(function(){
  $(\'#agree_check\').click( function() {
	  jQuery(\'#agreement_txt_block\').toggle();
	  jQuery(\'#co_pay_opt\').toggle();
  })

});
</script>';  
  
} else {
  $page_val = '<center>Your shopping cart is currently empty.<center>';
}
$page_output = $page_val;

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Order Payment';
$page_meta_description = 'shopping cart payment';
$page_meta_keywords = 'shopping cart payment';

// this script writes the content for the sites logoff page and handles search form submissions
$selTemp = 'pages.php';
$selHedMetTitle = $page_header_title;
$selHedMetDesc = $page_meta_description;
$selHedMetKW = $page_meta_keywords;
?>