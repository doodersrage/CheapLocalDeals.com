<?PHP

if (isset($_POST['submit'])) {
  if ($_POST['submit'] === 'Checkout') {
	  
	  // check if customer is logged in
	  if ($_SESSION['customer_logged_in'] == 1) {
		  header("Location: ".MOB_URL."?action=ordPrevPay");	
	  } else {
		  header("Location: ".MOB_URL."?action=userLogin");
	  }
	  
  }
}

if ($shopping_cart_manage->contents_count > 0) {
  $page_val = '<form action="" method="post" name="shopping_cart_form">
  				<table class="shopping_cart_table" align="center">';
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

  $page_val .= '</td></tr><tr>
				  <td class="shopping_box" colspan="'.$list_rows.'" align="right"><input class="submit_btn" name="submit" type="submit" value="Clear Cart"> <input class="submit_btn" name="submit" type="submit" value="Update Cart"></td>
				  </tr>
				  <tr><td class="shopping_box" colspan="'.$list_rows.'" align="right">'.(!empty($_SESSION['previous_page']) ? ' <a href="'.$_SESSION['previous_page'].'" class="continue-shopping-btn">Continue Shopping</a> ' : '').'</td>
				  <td align="right" class="shopping_box"></td>
			   </tr>';

  $page_val .= '<tr><td colspan="'.$list_rows.'"><table id="agreement_txt_block"><tr><th align="center" valign="top" class="shopping_box" '.(!isset($_POST['agree_check']) ? 'bgcolor=""' : '').' ><div class="terms_agree">I agree to the terms and conditions listed below <input id="agree_check" name="agree_check" type="checkbox" value="1" '.($_POST['agree_check'] == 1 ? 'checked' : '').' /></div></th></tr><tr><td class="shopping_box">'.CERTIFICATE_AGREEMENT.'</td></tr>
  </table></td></tr>';

  $page_val .= '</table></form><table id="co_pay_opt" align="center" style="display:none">
  				<tr>
					  <td class="shopping_box" align="center">
						  <table class="credit_card_entry">
						  <tr><th class="shop_header" align="center" colspan="2"><strong>Promo Code</strong></th></tr>
						  <tr><td>Entering a valid promo code will allow<br /> you save money on your purchase.</td></tr>
						  <tr><td align="center">
						  <form action="" method="post" name="promo_code_form">
						  <input name="promo_code" type="text" size="10" maxlength="10" value="'.(isset($_SESSION['promo_code']) ? $_SESSION['promo_code'] : '').'"><br/>
						  <input type="submit" name="promo_code_sub" value="Add Discount" class="submit_btn" />
						  </form>
						  </td></tr>
						  </table>
					  </td>
					  <td class="shopping_box" align="center">
						  <div class="checkout_btns"><form action="" method="post" name="shopping_cart_form"><input class="submit_btn" name="submit" type="submit" value="Checkout"></form> (Requires Account)</div>
					  </td>
				  </tr>
			  </table>';
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
$page_header_title = 'CheapLocalDeals.com - Shopping Cart';
$page_meta_description = 'shopping cart';
$page_meta_keywords = 'shopping cart';

// this script writes the content for the sites logoff page and handles search form submissions
$selTemp = 'pages.php';
$selHedMetTitle = $page_header_title;
$selHedMetDesc = $page_meta_description;
$selHedMetKW = $page_meta_keywords;
?>