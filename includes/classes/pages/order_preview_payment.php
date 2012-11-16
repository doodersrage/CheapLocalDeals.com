<?PHP

// this document provides the output for the shopping cart page
class order_preview_payment_pg {
	private $list_rows, $cust_promo_cds_tbl;

	// output shopping carts content
	public function display_shopping_cart() {
		global $shopping_cart_manage, $customer_info_table, $cust_promo_cds_tbl;
		
		$customer_info_table->get_db_vars($_SESSION['customer_id']);
		
		if (count($shopping_cart_manage->contents) > 0) {
			
			$page_val = '<form action="" method="post" name="order_payment_form"><table align="center" class="frn_box_checkout">'.$error;
			
			$page_val .= $this->cart_listing_header();
			$page_val .= $this->cart_listing();
			
			$page_val .= '<tr><td colspan="'.($this->list_rows).'" align="right" class="frn_conbox"> <div class="subtotal">';			

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
  
			  $page_val .= '<tr><td colspan="'.($this->list_rows).'" class="shopping_box" align="center">';
			  
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
			
			$page_val .= '<tr><td colspan="'.($this->list_rows).'" class="shopping_box" align="center"><input class="submit_btn" name="submit" type="submit" value="Process Order"></td></tr>';
			
			$page_val .= '</table></form>';
		
		} else {
			
			$page_val = '<center>Your shopping cart is currently empty.<center>';
			
		}
	
	return $page_val;
	}
	
	// write listing header
	private function cart_listing_header() {
		
		// assign table header values
		$header_array = array('','Company Name','Requirements','Certificate Amount','Price','Quantity','Total');
		
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
				$list_contents .= '<td class="frn_conbox" align="center">'.$row.'</td>';
			}
			$list_contents .= '</tr>'.LB;
		}
		
	return $list_contents;
	}

}

?>