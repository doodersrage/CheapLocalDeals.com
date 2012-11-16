<?PHP

// this document provides the output for the shopping cart page
class shopping_cart_pg {
	private $list_rows;


	// displays shopping cart list
	public function shopping_cart_list() {
		global $shopping_cart_manage;

		$page_val = '<table class="shopping_cart_table">';
		
		$page_val .= $this->non_modify_cart_listing_header();
		$page_val .= $this->non_modify_cart_listing();

		$page_val .= '<tr><td colspan="'.($this->list_rows).'" align="right" class="shopping_box"> Total: $'.$shopping_cart_manage->sub_total.'</td></tr>';
	
		$page_val .= '</table>';

	return $page_val;
	}

	// output shopping carts content
	public function display_shopping_cart() {
		global $shopping_cart_manage, $cust_promo_cds_tbl, $customer_info_table;
		
		if ($shopping_cart_manage->contents_count > 0) {
			
			$page_val = '<form action="" method="post" name="shopping_cart_form">
						<table align="center" class="frn_box_checkout">';
			
			$page_val .= $this->cart_listing_header();
			$page_val .= $this->cart_listing();

			$page_val .= '<tr><td colspan="'.($this->list_rows).'" align="right" class="frn_conbox"> <div class="subtotal">';

			// draws totals area
			$page_val .= draw_order_totals();
			
//			$page_val .= '<tr><td colspan="'.($this->list_rows).'" class="shopping_box warn_txt"><strong>WARNING!!! Deals cannot be combined unless otherwise specified!</strong></td></tr>';
						
			$page_val .= '<tr>
							<td class="shopping_box" colspan="'.($this->list_rows).'" align="right">'.(!empty($_SESSION['previous_page']) ? ' <a href="'.$_SESSION['previous_page'].'" class="continue-shopping-btn">Continue Shopping</a> ' : '').'<input class="submit_btn" name="submit" type="submit" value="Update Cart"> <input class="submit_btn" name="submit" type="submit" value="Clear Cart"></td>
							<td align="right" class="shopping_box"></td>
						 </tr>';
						 
			$page_val .= '<tr><td colspan="'.($this->list_rows).'"><table id="agreement_txt_block"><tr><td align="center" valign="top" class="shopping_box" '.(!isset($_POST['agree_check']) ? 'bgcolor=""' : '').' ><div class="terms_agree">I agree to the terms and conditions listed below <input id="agree_check" name="agree_check" type="checkbox" value="1" '.($_POST['agree_check'] == 1 ? 'checked' : '').' /></div></td></tr><tr><td class="shopping_box"><iframe width="880" height="170" src="'.SITE_URL.'includes/popups/cert_agree_popup.deal"></iframe></td></tr>
			</table></td></tr>';
						 
			$page_val .= '</table>
						</form>
						<div id="co_pay_opt" align="right" style="display:none">
						<table align="right">';
			
			if(ENABLE_PAYPAL == 1) { 
					$page_val .= '<tr>
									<td class="shopping_box" align="right">
										<form action=\'checkout/expresscheckout.deal\' METHOD=\'POST\'>
										<div class="checkout_btns">
						<input type=\'image\' name=\'submit\' src=\'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif\' border=\'0\' align=\'top\' alt=\'PayPal\'/>
										</div>
										<input type="hidden" name="promo_code" value="'.$_SESSION['promo_code'].'"/>
						</form>
									</td>
								</tr>
								<tr>
									<td class="shopping_box" align="center">
									<div class="checkout_btns">	Or </div>
									</td>
								</tr>';
			}
			
			$page_val .= '<tr>
								<td class="shopping_box" align="center">
									<div class="checkout_btns"><form action="" method="post" name="shopping_cart_form"><input class="submit_btn" name="submit" type="submit" value="Checkout"></form> (Requires Account)</div>
								</td>
							</tr>
						</table>						
						<table class="credit_card_entry" align="right">
						<tr><th class="shop_header" align="center" colspan="2"><strong>Promo Code</strong></th></tr>
						<tr><td>Entering a valid promo code will allow<br /> you save money on your purchase.</td></tr>
						<tr><td align="center">
						<form action="" method="post" name="promo_code_form">
						<input name="promo_code" type="text" size="10" maxlength="10" value="'.(isset($_SESSION['promo_code']) ? $_SESSION['promo_code'] : '').'"><br/>
						<input type="submit" name="promo_code_sub" value="Add Discount" class="submit_btn" />
						</form>
						</td></tr>
						</table>
						</div><div class="clear">&nbsp;</div>';
		
		} else {
			
			$page_val = '<center>Your shopping cart is currently empty.<center>';
			
		}
	
	return $page_val;
	}
	
	// write listing header
	private function cart_listing_header() {
		
		// assign table header values
		$header_array = array('','Company Name','Requirements','Certificate Amount','Price','Quantity','Remove','Total');
		
		// set list rows to be used in other functions
		$this->list_rows = count($header_array);
		
		$list_head = '<tr>'.LB;
		
		foreach($header_array as $value) {
		$list_head .= '<th class="shop_header">'.$value.'</th>';
		}
		
		$list_head .= '</tr>'.LB;
		
	return $list_head;
	}
	
	// write cart contents
	private function cart_listing() {
		global $shopping_cart_manage,$table_master,$adv_info_tbl;
		
		// make sure contents arrays is at first key value
		reset($shopping_cart_manage->contents);
		
		$list_array = array();
		$list_contents = '';
		
		// write listing array
		foreach($shopping_cart_manage->contents as $id => $value) {
			
			$quantity_box = $table_master->table(
								$table_master->row(
									$table_master->td('<input class="cart_quan_ent" name="quantity_items['.$id.']" id="quantity_items'.$id.'" type="text" size="2" maxlength="2" value="'.$value['item_quantity'].'">','rowspan="2"').
									$table_master->td('<a onclick="update_quant(\'quantity_items'.$id.'\',1);" href="javascript: void(0);"><img border="0" src="images/up_arrow.gif"></a>')).
								$table_master->row(
									$table_master->td('<a onclick="update_quant(\'quantity_items'.$id.'\',-1);" href="javascript: void(0);"><img border="0" src="images/down_arrow.gif"></a>'))
								);
			
			// get company info
			$adv_info_tbl->get_db_vars($value['company_id']);
			
			$list_array[] = array('<a href="'.SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/">'.$value['company_image'].'</a>','<a href="'.SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$adv_info_tbl->id.'/">'.$value['company_name'].'</a>', $value['requirements'], $value['item_name'], '$'.$value['item_price'],$quantity_box,'<input name="delete_item[]" type="checkbox" value="'.$id.'">','<strong>$'.format_currency($value['item_total']).'</strong>');
		}
				
		foreach($list_array as $value) {
			$list_contents .= '<tr>'.LB;
			foreach($value as $row) {
				$list_contents .= '<td class="frn_conbox" align="center">'.$row.'</td>';
			}
			$list_contents .= '</tr>'.LB;
		}
		
	return $list_contents;
	}
	
	// write listing header
	private function non_modify_cart_listing_header() {
		
		// assign table header values
		$header_array = array('','Company Name','Requirements','Certificate Amount','Price','Quantity','Total');
		
		// set list rows to be used in other functions
		$this->list_rows = count($header_array);
		
		$list_head = '<tr>'.LB;
		
		foreach($header_array as $value) {
		$list_head .= '<th>'.$value.'</th>';
		}
		
		$list_head .= '</tr>'.LB;
		
	return $list_head;
	}
	
	// write non-modify cart contents
	private function non_modify_cart_listing() {
		global $shopping_cart_manage;
		
		// make sure contents arrays is at first key value
		reset($shopping_cart_manage->contents);
		
		$list_array = array();
		$list_contents = '';
		
		// write listing array
		foreach($shopping_cart_manage->contents as $id => $value) {
			$list_array[] = array($value['company_image'],$value['company_name'], $value['requirements'], $value['item_name'], '$'.$value['item_price'],$value['item_quantity'],'$'.$value['item_total']);
		}
				
		foreach($list_array as $value) {
			$list_contents .= '<tr>'.LB;
			foreach($value as $row) {
				$list_contents .= '<td align="center">'.$row.'</td>';
			}
			$list_contents .= '</tr>'.LB;
		}
		
	return $list_contents;
	}

}

?>