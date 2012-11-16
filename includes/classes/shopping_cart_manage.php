<?PHP

// this class maanges the customers shopping cart
class shopping_cart_manage {
	public $contents, $contents_count, $sub_total;
	// cart submission values
	public $certificate_amount_id, $quantity, $advertiser_id, $action, $savings;
	
	public function __construct() {
		$this->set_values();
	}
	
	// set public values
	public function set_values() {
		
		// pull post values
		$this->certificate_amount_id = (isset($_POST['certificate_amount_id']) ? $_POST['certificate_amount_id'] : '');
		$this->quantity = (isset($_POST['quantity']) ? $_POST['quantity'] : '');
		$this->advertiser_id = (isset($_POST['advertiser_id']) ? $_POST['advertiser_id'] : '');
		$this->action = (isset($_POST['action']) ? $_POST['action'] : '');
		
		// set cart variables
		$this->contents = (!empty($_SESSION['cart_contents']) ? $_SESSION['cart_contents'] : '');
			
		// run posted function
		$this->run_selected_action();
		
		if (isset($_POST['submit'])) {
			if ($_POST['submit'] === 'Clear Cart') {
					$this->action = 'clear';
					// run posted function
					$this->run_selected_action();
			} else {
				// update order quantities
			  if (isset($_POST['quantity_items'])) {
				  if (!empty($_POST['quantity_items'])) {
					  $this->action = 'update_quantity';
					  // run posted function
					  $this->run_selected_action();
				  }
			  }
			  // delete checked item
			  if (isset($_POST['delete_item'])) {
				  if (!empty($_POST['delete_item'])) {
					  $this->action = 'remove';
					  // run posted function
					  $this->run_selected_action();
				  }
			  }
			}
		}
		
		// clear assigned values
		$this->clear_values();
	}

	// add item to customers shopping cart
	private function add_item() {
		global $cert_amt_tbl, $adv_info_tbl;
		
		$adv_info_tbl->get_db_vars($this->advertiser_id);
		
		// check for existing items in cart
		if (is_array($this->contents)) {
			foreach($this->contents as $id => $value) {
				if ($value['company_id'] == $this->advertiser_id && $value['certificate_amount_id'] == $this->certificate_amount_id) {
					$update_quantity = $id;
				}
			}
		}
		
		if (!isset($update_quantity)) {
			$cert_amt_tbl->get_db_vars($this->certificate_amount_id);

			// set cert req string
			$requirement_value = set_cert_agreement_str($adv_info_tbl->certificate_requirements[$this->certificate_amount_id]['type'],$adv_info_tbl->certificate_requirements[$this->certificate_amount_id]['value']);

			if (!empty($adv_info_tbl->certificate_requirements[$this->certificate_amount_id]['excludes'])) {
				$requirement_value .= '<br/>Excludes: '.$adv_info_tbl->certificate_requirements[$this->certificate_amount_id]['excludes'];
			}
			
			if(is_numeric($cert_amt_tbl->cost)) {
			  $cert_cost_val = $cert_amt_tbl->cost;
			} else {
			  $cert_cost_val = $adv_info_tbl->certificate_requirements[$cert_amt_tbl->id]['blank_cost'];
			}
			if(is_numeric($cert_amt_tbl->discount_amount)) {
			  $cert_disc_amt = $cert_amt_tbl->discount_amount;
			} else {
			  $cert_disc_amt = $adv_info_tbl->certificate_requirements[$cert_amt_tbl->id]['blank_val'];
			}

			$this->contents[] = array(
									  'company_id' => $adv_info_tbl->id,
									  'company_image' => '<img border="0" src="'.SITE_SSL_URL.'includes/resize_image.deal?image='.urlencode(($adv_info_tbl->image == '' ? '' : 'customers/'.$adv_info_tbl->image)).'&new_width=85&new_height=85" alt="' . $adv_info_tbl->company_name . '" />',
									  'company_name' => $adv_info_tbl->company_name,
									  'requirements' => $requirement_value,
									  'certificate_amount_id' => $this->certificate_amount_id,
									  'item_name' => $certificates_table->title . ' $' .format_currency($cert_disc_amt),
									  'item_quantity' => $this->quantity,
									  'item_price' => format_currency($cert_cost_val),
									  'item_total' => format_currency(($cert_cost_val*$this->quantity))
									);
		} else {
			$this->contents[$update_quantity]['item_quantity'] += $this->quantity;
			$this->contents[$update_quantity]['item_total'] = format_currency(($this->contents[$update_quantity]['item_quantity']*$this->contents[$update_quantity]['item_price']));
		}
	}
	
	// remove item from customers shopping cart
	private function remove_item() {
		
		foreach($_POST['delete_item'] as $value) {
			unset($this->contents[$value]);
		}
			
	}
	
	// update items quantity
	private function update_item_quantity() {
		
		foreach($_POST['quantity_items'] as $id => $value) {
			$this->contents[$id]['item_quantity'] = $value;
			$this->contents[$id]['item_total'] = round(($value*$this->contents[$id]['item_price']),2);
		}
			
	}
	
	// clears cart contents
	public function clear_contents() {
		
		$this->contents = array();
		$_SESSION['cart_contents'] = array();
		
	}
	
	// counts distinct items in shopping cart
	private function count_cart_contents() {
	
		$this->contents_count = '';
	
		if (is_array($this->contents)) {
			foreach($this->contents as $id => $value) {
				$this->contents_count += $value['item_quantity'];
			}
		}

	}
	
	// gets current cart contenst sub total
	private function cart_sub_total() {
		
		$this->sub_total = 0;
		
		if (is_array($this->contents)) {
			reset($this->contents);
			foreach($this->contents as $id => $value) {
				$this->sub_total += $value['item_total'];
			}
		}
		
		$this->sub_total = format_currency($this->sub_total);
		$_SESSION["Payment_Amount"] = $this->sub_total;
		
	}
	
	// calculates carts savings value
	private function cart_savings_total() {
		global $cert_amt_tbl;
		
		$this->savings = 0;
		$total_cert_amt = 0;
		
		if (is_array($this->contents)) {
			reset($this->contents);
			foreach($this->contents as $id => $value) {
				$cert_amt_tbl->get_db_vars($value['certificate_amount_id']);
				$cur_save = $cert_amt_tbl->discount_amount;
				$total_cert_amt += ($cur_save * $value['item_quantity']);
			}
		}
		
	$this->savings = format_currency($total_cert_amt - $this->sub_total);
	}
	
	// runs submitted action
	private function run_selected_action() {
		
		switch($this->action) {
		case 'add':
			$this->add_item();
		break;
		case 'update_quantity':
			$this->update_item_quantity();
		break;
		case 'remove':
			$this->remove_item();
		break;
		case 'clear':
			$this->clear_contents();
		break;
		}
	}
	
	// clears set values and reassigns session variables
	private function clear_values() {
		
		// pull post values
		$this->certificate_amount_id = '';
		$this->quantity = '';
		$this->certificate_id = '';
		$this->action = '';
		
		// set cart variables
		$this->count_cart_contents();
		$this->cart_sub_total();
		$this->cart_savings_total();
		$_SESSION['cart_contents'] = $this->contents;
	}

}

?>