<?PHP

if(!empty($_GET['id'])){
	
	$api_ref_track_tbl->get_db_vars($_GET['id']);
	if($api_ref_track_tbl->id > 0) {
		
		if($api_ref_track_tbl->customer_id > 0){
			$customer_info_table->get_db_vars($api_ref_track_tbl->customer_id);
			$cust_id = $customer_info_table->first_name.' '.$customer_info_table->last_name;
		} else {
			$cust_id = '';
		}
		
		$cart_contents = unserialize($api_ref_track_tbl->cart);
		if(count($cart_contents) > 0){
			$cart_op = '<table>
							<tr>
							<th>Advertiser</th>
							<th>Requirements</th>
							<th>Value</th>
							<th>Price</th>
							<th>Quantity</th>
							</tr>';
			foreach($cart_contents as $cart_item){
				$cart_op .= '<tr>
								<td>'.$cart_item['company_image'].'<br/>'.$cart_item['company_name'].'</td>
								<td>'.$cart_item['requirements'].'</td>
								<td>'.$cart_item['item_name'].'</td>
								<td>$'.$cart_item['item_price'].'</td>
								<td>'.$cart_item['item_quantity'].'</td>
							</tr>';
			}
			$cart_op .= '</table>';
		} else {
			$cart_op = '';
		}
?>
<table border="0">
  <tr>
    <td style="text-align:right">Date/Time:</td>
    <td><?PHP echo date('n/j/Y h:i:s A',strtotime($api_ref_track_tbl->date_time)); ?></td>
  </tr>
  <tr>
    <td style="text-align:right">Session ID:</td>
    <td><?PHP echo $api_ref_track_tbl->sess_id; ?></td>
  </tr>
  <tr>
    <td style="text-align:right">Customer Name:</td>
    <td><?PHP echo $cust_id; ?></td>
  </tr>
  <tr>
    <td style="text-align:right">Referring Page:</td>
    <td><a href="<?PHP echo $api_ref_track_tbl->ref_page; ?>" target="_blank"><?PHP echo $api_ref_track_tbl->ref_page; ?></a></td>
  </tr>
  <tr>
    <td style="text-align:right">Page:</td>
    <td><a href="<?PHP echo $api_ref_track_tbl->page; ?>" target="_blank"><?PHP echo $api_ref_track_tbl->page; ?></a></td>
  </tr>
  <tr>
    <td style="text-align:right">Cart Contents:</td>
    <td><?PHP echo $cart_op; ?></td>
  </tr>
  <tr>
    <td style="text-align:right">Cart Total</td>
    <td>$<?PHP echo format_currency($api_ref_track_tbl->cart_total); ?></td>
  </tr>
</table>
<?php
	} else {
		echo '<center>We are sorry but the requested session data could not be found.</center>';
	}
}
?>
<center><a href="javascript: history.go(-1)"><--Back</a></center>