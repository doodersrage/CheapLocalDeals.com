<?PHP

// print previous orders page content
class previous_orders_pg {

	public function list_previous_orders() {
			global $dbh, $odrs_tbl;
			
		$orders_list_output = '';
			
		$orders_list = $odrs_tbl->list_orders_customer($_SESSION['customer_id']);
			
		krsort($orders_list);
			
		if(is_array($orders_list)) {
			if (count($orders_list) > 0) {
				$orders_list_output = '<center><strong>Previous Orders List:</strong></center>';		
				$orders_list_output .= '<table align="center" class="frn_box" style="width: 808px;">';		
				$orders_list_output .= '<tr><th class="shop_header">Order Date/Time</th><th class="shop_header">Order Total</th><th class="shop_header">Payment Type</th></tr>';		
				foreach($orders_list as $cur_order) {
				$orders_list_output .= '<tr><td class="frn_conbox"><a href="javascript:void(0)" onclick="view_order_proc('.$cur_order['id'].')">'.date('m/d/Y g:iA',strtotime($cur_order['date_added'])).'</a></td><td class="frn_conbox">$'.$cur_order['order_total'].'</td><td class="frn_conbox">'.$cur_order['credit_card_type'].'</td></tr>';		
				}
				$orders_list_output .= '</table>';		
			} else {
				$orders_list_output = '<span class="cert-error">You do not appear to have any orders.</span>';		
			}
		} else {
			$orders_list_output = '<span class="cert-error">You do not appear to have any orders.</span>';		
		}
	
	return $orders_list_output;
	}

}

?>