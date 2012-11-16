<?PHP

// document modifies and adds advertisers

//// load certificates amount page
//require(SITE_ADMIN_CLASSES_DIR.'certificate_amount.php');
//$certificate_amount_page = new certificate_amount;

// load certificates amount page
require(SITE_ADMIN_CLASSES_DIR.'listings/advertiser_levels.php');
$advertiser_levels_lst = new advertiser_levels_lst;

// load certificates amount page
require(SITE_ADMIN_CLASSES_DIR.'forms/advertiser_levels.php');
$advertiser_levels_frm = new advertiser_levels_frm;

//// load ad_payment_methods page
//require(SITE_ADMIN_CLASSES_DIR.'ad_payment_methods.php');
//$ad_payment_methods_page = new ad_payment_methods;

// retail customers management class
class retail_customers {
	private $last_child_id;
	
	// delete advertiser and move them to backup table
	function undelete_advertiser() {
		global $dbh, $adv_info_tbl, $adv_info_bu_tbl;
	
		// deleted selected items
		if(isset($_POST['undelete_advertiser'])) {
			if(is_array($_POST['undelete_advertiser'])) {
				foreach($_POST['undelete_advertiser'] as $id => $del_advert) {
					// pull current advertiser info
					$adv_info_bu_tbl->get_db_vars($del_advert);
					
					// transfer advert values to advert backup table
					$adv_info_tbl->id = $adv_info_bu_tbl->id;
					$adv_info_tbl->approved = $adv_info_bu_tbl->approved;
					$adv_info_tbl->approval_date = $adv_info_bu_tbl->approval_date;
					$adv_info_tbl->update_approval = $adv_info_bu_tbl->update_approval;
					$adv_info_tbl->company_name = $adv_info_bu_tbl->company_name;
					$adv_info_tbl->customer_description = $adv_info_bu_tbl->customer_description;
					$adv_info_tbl->longitude = $adv_info_bu_tbl->longitude;
					$adv_info_tbl->latitude = $adv_info_bu_tbl->latitude;
					$adv_info_tbl->username = $adv_info_bu_tbl->username;
					$adv_info_tbl->password = $adv_info_bu_tbl->password;
					$adv_info_tbl->hours_operation = serialize($adv_info_bu_tbl->hours_operation);
					$adv_info_tbl->customer_level = $adv_info_bu_tbl->customer_level;
					$adv_info_tbl->customer_level_exp = $adv_info_bu_tbl->customer_level_exp;
					$adv_info_tbl->customer_level_renewal_date = $adv_info_bu_tbl->customer_level_renewal_date;
					$adv_info_tbl->website = $adv_info_bu_tbl->website;
					$adv_info_tbl->category = $adv_info_bu_tbl->category;
					$adv_info_tbl->bbb_member = $adv_info_bu_tbl->bbb_member;
					$adv_info_tbl->link_partner = $adv_info_bu_tbl->link_partner;
					$adv_info_tbl->affiliate_code = $adv_info_bu_tbl->affiliate_code;
					$adv_info_tbl->link_affiliate_code = $adv_info_bu_tbl->link_affiliate_code;
					$adv_info_tbl->products_services = $adv_info_bu_tbl->products_services;
					$adv_info_tbl->payment_options = serialize($adv_info_bu_tbl->payment_options);
					$adv_info_tbl->payment_method = $adv_info_bu_tbl->payment_method;
					$adv_info_tbl->certificate_levels = serialize($adv_info_bu_tbl->certificate_levels);
					$adv_info_tbl->certificate_requirements = serialize($adv_info_bu_tbl->certificate_requirements);
					$adv_info_tbl->credit_card_type = $adv_info_bu_tbl->credit_card_type;
					$adv_info_tbl->cc_number = $adv_info_bu_tbl->cc_number;
					$adv_info_tbl->cvv = $adv_info_bu_tbl->cvv;
					$adv_info_tbl->cc_exp = $adv_info_bu_tbl->cc_exp;
					$adv_info_tbl->bank_name = $adv_info_bu_tbl->bank_name;
					$adv_info_tbl->bank_state = $adv_info_bu_tbl->bank_state;
					$adv_info_tbl->drivers_license_num = $adv_info_bu_tbl->drivers_license_num;
					$adv_info_tbl->drivers_license_state = $adv_info_bu_tbl->drivers_license_state;
					$adv_info_tbl->check_routing_num = $adv_info_bu_tbl->check_routing_num;
					$adv_info_tbl->check_account_num = $adv_info_bu_tbl->check_account_num;
					$adv_info_tbl->hide_address = $adv_info_bu_tbl->hide_address;
					$adv_info_tbl->first_name = $adv_info_bu_tbl->first_name;
					$adv_info_tbl->last_name = $adv_info_bu_tbl->last_name;
					$adv_info_tbl->address_1 = $adv_info_bu_tbl->address_1;
					$adv_info_tbl->address_2 = $adv_info_bu_tbl->address_2;
					$adv_info_tbl->city = $adv_info_bu_tbl->city;
					$adv_info_tbl->state = $adv_info_bu_tbl->state;
					$adv_info_tbl->zip = $adv_info_bu_tbl->zip;
					$adv_info_tbl->phone_number = $adv_info_bu_tbl->phone_number;
					$adv_info_tbl->fax_number = $adv_info_bu_tbl->fax_number;
					$adv_info_tbl->email_address = $adv_info_bu_tbl->email_address;
					$adv_info_tbl->account_enabled = $adv_info_bu_tbl->account_enabled;
					$adv_info_tbl->image = $adv_info_bu_tbl->image;
					$adv_info_tbl->last_ip = $adv_info_bu_tbl->last_ip;
					$adv_info_tbl->last_login = $adv_info_bu_tbl->last_login;
					$adv_info_tbl->last_session_id = $adv_info_bu_tbl->last_session_id;
					$adv_info_tbl->allow_multiple_logins = $adv_info_bu_tbl->allow_multiple_logins;
					$adv_info_tbl->authorization_code = $adv_info_bu_tbl->authorization_code;
					$adv_info_tbl->email_authorized = $adv_info_bu_tbl->email_authorized;
					
					// insert new backup record
					$adv_info_tbl->insert();
					
					// delete advertiser from advertiser info table
					$stmt = $dbh->prepare("DELETE FROM advertiser_info_backup WHERE id = '".$del_advert."';");
					$stmt->execute();
					
					// transfer selected categories for deleted advertiser
					$sql_query = "SELECT
									id,
									advertiser_id,
									category_id
								 FROM
									advertiser_categories_backup
								 WHERE
									advertiser_id = ?
								;";
					
					$values2 = array(
									$del_advert
									);
	
					$stmt2 = $dbh->prepare($sql_query);					 
					$result = $stmt2->execute($values2);
					
					while($rows_check = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
						$sql_query = "INSERT INTO advertiser_categories (category_id,advertiser_id) VALUES (?,?);";
						$update_vals3 = array(
											$rows_check['category_id'],
											$rows_check['advertiser_id']
											);
											
						$stmt3 = $dbh->prepare($sql_query);
						$stmt3->execute($update_vals3);						
					}
					
					// delete advertiser categories 
					$stmt = $dbh->prepare("DELETE FROM advertiser_categories_backup WHERE advertiser_id = '".$del_advert."';");
					$stmt->execute();
					
				}
			}
		}
		
	}
	
	// display retail customers listing
	function retail_customers_listing($message = '') {
		$retail_customers_view = open_table_listing_form('Deleted Advertisers Listing','view_retail_customers',SITE_ADMIN_SSL_URL.'?sect=retcustomerbackup&mode=view','post',$message,10);
		$retail_customers_view .= $this->retail_customers_listing_content();
		$retail_customers_view .= close_table_form();
		
		return $retail_customers_view;
	}
		
	// list retail customers
	function retail_customers_listing_content() {
			global $dbh;
			
		// sets record limit per page	
		$page_limiter = ADMIN_PER_PAGE_RESULTS; 
		
		// table title array							
		$title_array = array(
							'#',
							'Company Name <br><a href="?sect=retcustomerbackup&mode=view&sort=cnameasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=cnamedesc">DESC</a>',
							'Username <br><a href="?sect=retcustomerbackup&mode=view&sort=unameasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=unamedesc">DESC</a>',
							'Contact Last Name <br><a href="?sect=retcustomerbackup&mode=view&sort=clnasc">ASC</a> <a href="?sect=retcustomer&mode=view&sort=clndesc">DESC</a>',
							'Affiliate Code',
							'Link Affiliate Code',
							'Affiliates',
							'Updated <br><a href="?sect=retcustomerbackup&mode=view&sort=updatedasc">ASC</a> <a href="?sect=retcustomerbackup&mode=view&sort=updateddesc">DESC</a>',
							'Undelete<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].undelete_advertiser\').attr(\'checked\', \'checked\')">Select All</a>',
							'Delete<br><a href="javascript:void(0);" onclick="jQuery(\'input[@type=checkbox].delete_advertiser\').attr(\'checked\', \'checked\')">Select All</a>',
							);
		
		// gets table boxes count
		$table_boxes_cnt = count($title_array);

		// draw table header
		$searchbox_head = array('Search: <input name="search_box" type="text" size="35"><input name="submit" type="submit" value="Submit">');
		$retail_customers_listing .= draw_table_header($searchbox_head,$table_boxes_cnt,'center');
		
		// prints page links
		if (empty($_POST['search_box'])) {
			$sql_query = "SELECT
							count(*) as rcount
						 FROM
							advertiser_info_backup
						 ";
		
		if(!empty($_POST['search_box'])) {
		$sql_query .= "
				WHERE (company_name LIKE
				'%".str_replace("'","''",$_POST['search_box'])."%' OR username LIKE '%".str_replace("'","''",$_POST['search_box'])."%' OR last_name LIKE '%".str_replace("'","''",$_POST['search_box'])."%') ";
		}
			
			$rowscount = $dbh->queryRow($sql_query);
			
			$row_count = $rowscount['rcount'];
			$page_count = (int)$row_count/$page_limiter;
			
			for($i = 0;$i <= $page_count;$i++) {
				$pages_array[] = '<a href="?sect=retcustomerbackup&mode=view&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
			}
			
			$pages_links = implode(', ',$pages_array);
			
			$retail_customers_listing .= draw_table_contect(array('Total Advertisers: '.number_format($row_count).'<br><center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		}
							
		// print title boxes
		$retail_customers_listing .= draw_table_header($title_array);	
		
		// set sort option
		if (!empty($_GET['sort'])) $_SESSION['advert_sort'] = $_GET['sort'];
		
		$sql_query = "SELECT
						id,
						company_name,
						username,
						last_name,
						date_updated,
						affiliate_code,
						link_affiliate_code
					 FROM
						advertiser_info_backup
					 ";
		
		if(!empty($_POST['search_box'])) {
		$sql_query .= "
				WHERE (company_name LIKE
				'%".str_replace("'","''",$_POST['search_box'])."%' OR username LIKE '%".str_replace("'","''",$_POST['search_box'])."%' OR last_name LIKE '%".str_replace("'","''",$_POST['search_box'])."%') ";
		}
		
		// sets output order by
		if(isset($_SESSION['advert_sort'])) {
			switch($_SESSION['advert_sort']) {
			case 'cnameasc':
				$order_sel = 'company_name ASC';
			break;
			case 'cnamedesc':
				$order_sel = 'company_name DESC';
			break;
			case 'unameasc':
				$order_sel = 'username ASC';
			break;
			case 'unamedesc':
				$order_sel = 'username DESC';
			break;
			case 'clnasc':
				$order_sel = 'last_name ASC';
			break;
			case 'clndesc':
				$order_sel = 'last_name DESC';
			break;
			case 'updatedasc':
				$order_sel = 'date_updated ASC';
			break;
			case 'updateddesc':
				$order_sel = 'date_updated DESC';
			break;
			}
			
			$sql_query .= "
					ORDER BY ".$order_sel;
		
		} else {
			$sql_query .= "
					ORDER BY
						company_name ASC ";
		}
			
		if (!empty($_GET['page_val']) && empty($_POST['search_box'])) {
		$sql_query .= "
				LIMIT  ".$_GET['page_val'].", ".($page_limiter)." ";
		} elseif (empty($_POST['search_box'])) {
		$sql_query .= "
				LIMIT
				".$page_limiter." ";
		}
					
		$sql_query .= ";";
		
		$rows = $dbh->queryAll($sql_query);
		
		$item_num = 0;
		$item_num += $_GET['page_val'];
		
		foreach ($rows as $retail_customers) {
			
			$item_num++;
			
			$row_array = array(
								$item_num.'.',
								'<a href="'.SITE_ADMIN_SSL_URL.'?sect=retcustomerbackup&mode=edit&cid='.$retail_customers['id'].'">'.$retail_customers['company_name'].'</a>',
								$retail_customers['username'],
								$retail_customers['last_name'],
								$retail_customers['affiliate_code'],
								$retail_customers['link_affiliate_code'],
								$this->advert_affiliate_count($retail_customers['affiliate_code']),
								(!empty($retail_customers['date_updated']) ? date('n/j/Y h:i:s A',strtotime($retail_customers['date_updated'])) : ''),
								'<input class="undelete_advertiser" name="undelete_advertiser[]" type="checkbox" value="'.$retail_customers['id'].'">',
								'<input class="delete_advertiser" name="delete_advertiser[]" type="checkbox" value="'.$retail_customers['id'].'">',
								);
		
			$retail_customers_listing .= draw_table_contect($row_array,0,'center');
		
		}
		
		// print page links
		$retail_customers_listing .= draw_table_contect(array('Total Advertisers: '.number_format($row_count).'<br><center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		
		$retail_customers_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Perform Selected"></center>'),$table_boxes_cnt,'center');
		
		return $retail_customers_listing;
	}
	
	private function advert_affiliate_count($affiliate_code) {
			global $dbh;
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						advertiser_info_backup
					 WHERE 
						link_affiliate_code = '".$affiliate_code."'
						 ;";
		$rows = $dbh->queryRow($sql_query);

	return $rows['rcount'];
	}
	
	// load add retail customers page
	function edit_retail_customer($message = '') {
		
		$add_retail_customer = open_table_form('Edit Deleted Advertiser','edit_retail_customer',SITE_ADMIN_SSL_URL.'?sect=retcustomerbackup&mode=editcheck','post',$message);
		$add_retail_customer .= $this->retail_customer_form();
		$add_retail_customer .= close_table_form();
		
	return $add_retail_customer;
	}
	
	// customer level drop down menu
	function customer_level_drop_down($selected_id = '') {
			global $dbh;
		
		$parent_drop_down = '<select name="customer_level" id="parent_cat_dd">'.LB;
		
		$sql_query = "SELECT
						id,
						level_name
					 FROM
						advertiser_levels
					 ORDER BY level_weight ASC
					 ;";
		$rows = $dbh->queryAll($sql_query);
		
		foreach ($rows as $customer_level) {
		$ind = '--';
		
		$parent_drop_down .= '<option value="'.$customer_level['id'].'" '.($selected_id == $customer_level['id'] ? 'selected="selected" ' : '').'>'.$customer_level['level_name'].'</option>'.LB;
		
		}
		
		$parent_drop_down .= '</select>'.LB;
		
	return $parent_drop_down;
	}
	
	// draw retail customers form
	function retail_customer_form() {
			global $adv_info_bu_tbl, $cert_amt_tbl, $adv_pmt_mtds_tbl, $dbh;
		
		$retail_customer_form = table_form_header('* indicates required field');
		$retail_customer_form .= table_form_field('Account Approved:','<input name="approved" type="checkbox" value="1" '.(!empty($adv_info_bu_tbl->approved) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('Approval Date:',$adv_info_bu_tbl->approval_date.'<input name="'.$adv_info_bu_tbl->approval_date.'" type="hidden" value="" />');
		$retail_customer_form .= table_form_field('Update Approved:','<input name="update_approval" type="checkbox" value="1" '.(!empty($adv_info_bu_tbl->update_approval) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('Account Enabled:','<input name="account_enabled" type="checkbox" value="1" '.(!empty($adv_info_bu_tbl->account_enabled) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('Allow Multiple Logins:','<input name="allow_multiple_logins" type="checkbox" value="1" '.(!empty($adv_info_bu_tbl->allow_multiple_logins) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('<span class="required">*Company Name:</span>','<input name="company_name" type="text" size="60" value="'.$adv_info_bu_tbl->company_name.'">');
		$retail_customer_form .= table_form_field('Company Image:','<input name="image" type="file">'.(!empty($adv_info_bu_tbl->image) ? ' <script type="text/javascript"> 
				jQuery(function(){
				 jQuery("#del_image_lnk").click(function () {
					var current_advertiser_id = jQuery("#advert_id").val();
					var current_old_image = jQuery("#old_image").val();
					
					 $.ajax({
					   type: "POST",
					   url: "ajax_calls/delete_image.deal",
					   data: "advert_id="+current_advertiser_id+"&image="+current_old_image,
					   success: function(msg){
						 jQuery("#del_image_lnk").css("display","none");
						 jQuery("#old_image").val("");
						 jQuery("#image_text").html("Image Deleted");
					   }
					 });
				 });
				}); 
				</script>
				<br><strong>Current Image:</strong> <span id="image_text"><a href="'.SITE_URL.'images/customers/' . urlencode($adv_info_bu_tbl->image) . '" target="blank">' . $adv_info_bu_tbl->image . '</a></span><input id="old_image" name="old_image" type="hidden" value="'.$adv_info_bu_tbl->image.'"> <a id="del_image_lnk" href="javascript:void;"><font color="red">Delete</font></a>' : ''));
		$retail_customer_form .= table_form_field('<span class="required">*Username:</span>','<input name="username" type="text" size="30" maxlength="50" value="'.$adv_info_bu_tbl->username.'">');
		$retail_customer_form .= table_form_field('Password:','<input name="password" type="password" size="30" maxlength="50" value="">'.(!empty($adv_info_bu_tbl->password) ? ' - Password exists' : ' - Password has not been set. If you do not assign one the customer will not be allowed to login.'));
		$retail_customer_form .= table_form_field('Affiliate Code:', '<input name="affiliate_code" type="text" value="'.$adv_info_bu_tbl->affiliate_code.'" />');
		$retail_customer_form .= table_form_field('Link Affiliate Code:','<input name="link_affiliate_code" type="text" size="8" maxlength="8" value="'.$adv_info_bu_tbl->link_affiliate_code.'">');
		
		$retail_customer_form .= '<tr id="slideboxhead" ><td class="expand_title_header" align="right"><a href="javascript:void(0);" id="a" class="slide6" name="1"><font color="red">Expand</font></a></td><td class="form_title_header" align="center">Information </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead").css("cursor","pointer");
jQuery(\'#slidebox6\').hide();
jQuery(\'a.slide6\').text(\'Expand\');
			
jQuery(\'#slideboxhead\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		if (jQuery(\'a.slide6\').text() == \'Expand\') {
			jQuery(\'a.slide6\').text(\'Collapse\');
		} else {
			jQuery(\'a.slide6\').text(\'Expand\');
		}
		
     jQuery(\'#slidebox6\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox6" width="100%">';
		
		$retail_customer_form .= table_form_field('Customer Description:','<textarea name="customer_description" cols="75" rows="9">'.$adv_info_bu_tbl->customer_description.'</textarea>');
		$retail_customer_form .= table_form_field('Products And Services Mini Description:','<textarea name="products_services" cols="50" rows="6">'.$adv_info_bu_tbl->products_services.'</textarea>');
		
		$retail_customer_form .= table_form_field('Website:','<input name="website" type="text" size="60" value="'.$adv_info_bu_tbl->website.'">');
		$retail_customer_form .= table_form_field('BBB Member:','<input name="bbb_member" type="checkbox" value="1" '.(!empty($adv_info_bu_tbl->bbb_member) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('Links Back:','<input name="link_partner" type="checkbox" value="1" '.(!empty($adv_info_bu_tbl->link_partner) ? 'checked' : '').'>');
		
$retail_customer_form .= '</table></td></tr>';		
		
		$retail_customer_form .= '<tr id="slideboxhead1"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide1" name="1"><font color="red">Expand</font></a></td><td class="form_title_header">Certificate Amounts </td></tr>';

		$certificate_amounts = $cert_amt_tbl->get_certificate_amounts();
		
		$cert_amt = '<table>';
		foreach($certificate_amounts as $value) {
			
			$requirements = '<script type="text/javascript">
jQuery(function(){
 jQuery(".certificate_amount_requirements td").css("cursor","pointer");
  jQuery(\'#certificate_amount_requirements'.$value['id'].' td\').click(function(event) {
	if (event.target.type !== \'radio\') {
	  jQuery(\':radio\', this).trigger(\'click\');
	}
  });
  
  jQuery(\'#certificate_levels'.$value['id'].'\').click(function(event) {
	if (jQuery(\'#certificate_amount_requirements'.$value['id'].'\').css("display") == \'none\') {
	  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeIn("slow");
	} else {
	  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeOut("slow");
	}
  });

  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').css("display","none");

'.(is_array($adv_info_bu_tbl->certificate_levels) ? $adv_info_bu_tbl->certificate_levels[$value['id']] == 1 ? 'jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeIn("slow");' : '' : '').'

});
</script><table class="certificate_amount_requirements" id="certificate_amount_requirements'.$value['id'].'">';
			
			$requirements .= '<tr><td align="left"><input name="requirements['.$value['id'].']" type="radio" value="1" '.(is_array($adv_info_bu_tbl->certificate_requirements) ? $adv_info_bu_tbl->certificate_requirements[$value['id']]['type'] == 1 ? 'checked ' : '' : '').' /> Purchase Of ... <br><textarea name="requirement_text['.$value['id'].'][1]" cols="40" rows="3">'.(is_array($adv_info_bu_tbl->certificate_requirements) ? $adv_info_bu_tbl->certificate_requirements[$value['id']]['type'] == 1 ? $adv_info_bu_tbl->certificate_requirements[$value['id']]['value'] : '' : '').'</textarea></td></tr>';
			
			$requirements .= '<tr><td align="left"><input name="requirements['.$value['id'].']" type="radio" value="2" '.(is_array($adv_info_bu_tbl->certificate_requirements) ? $adv_info_bu_tbl->certificate_requirements[$value['id']]['type'] == 2 ? 'checked ' : '' : '').'/> Minimum Spend Of $<br><textarea name="requirement_text['.$value['id'].'][2]" cols="40" rows="3">'.(is_array($adv_info_bu_tbl->certificate_requirements) ? $adv_info_bu_tbl->certificate_requirements[$value['id']]['type'] == 2 ? $adv_info_bu_tbl->certificate_requirements[$value['id']]['value'] : '' : '').'</textarea></td></tr>';
			
			$requirements .= '<tr><td align="left"><input name="requirements['.$value['id'].']" type="radio" value="3" '.(is_array($adv_info_bu_tbl->certificate_requirements) ? $adv_info_bu_tbl->certificate_requirements[$value['id']]['type'] == 3 ? 'checked ' : '' : '').'/> <textarea name="requirement_text['.$value['id'].'][3]" cols="40" rows="3">'.(is_array($adv_info_bu_tbl->certificate_requirements) ? $adv_info_bu_tbl->certificate_requirements[$value['id']]['type'] == 3 ? $adv_info_bu_tbl->certificate_requirements[$value['id']]['value'] : '' : '').'</textarea></td></tr>';

			$requirements .= '<tr><td align="left"><input name="requirements['.$value['id'].']" type="radio" value="4" '.(is_array($adv_info_bu_tbl->certificate_requirements) ? $adv_info_bu_tbl->certificate_requirements[$value['id']]['type'] == 4 ? 'checked ' : '' : '').'/>No Requirements<input name="requirement_text['.$value['id'].'][4]" type="hidden" value="" /></td></tr>';
			
			$requirements .= '</table>';
			
			$cert_amt .= '<tr><td align="right" valign="top">$'.$value['discount_amount'].':</td><td valign="top"> <input name="certificate_levels['.$value['id'].']" id="certificate_levels'.$value['id'].'" type="checkbox" value="1" '.(is_array($adv_info_bu_tbl->certificate_levels) ? $adv_info_bu_tbl->certificate_levels[$value['id']] == 1 ? 'checked ' : '' : '' ).'/></td><td>'.$requirements.'</td></tr>'.LB; 
		}
		$cert_amt .= '</table>';
		
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead1").css("cursor","pointer");
jQuery(\'#slidebox1\').hide();
jQuery(\'a.slide1\').text(\'Expand\');
			
jQuery(\'#slideboxhead1\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		if (jQuery(\'a.slide1\').text() == \'Expand\') {
			jQuery(\'a.slide1\').text(\'Collapse\');
		} else {
			jQuery(\'a.slide1\').text(\'Expand\');
		}
		
     jQuery(\'#slidebox1\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox1" width="100%"><tr><td class="form_title" valign="top">Certificate Amounts:</td><td class="form_field">'.$cert_amt.'</td></tr></table></td></tr>';
		
		$retail_customer_form .= '<tr id="slideboxhead2"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide2" name="1"><font color="red">Expand</font></a></td><td class="form_title_header">Payment Information </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead2").css("cursor","pointer");
jQuery(\'#slidebox2\').hide();
jQuery(\'a.slide2\').text(\'Expand\');
			
jQuery(\'#slideboxhead2\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		if (jQuery(\'a.slide2\').text() == \'Expand\') {
			jQuery(\'a.slide2\').text(\'Collapse\');
		} else {
			jQuery(\'a.slide2\').text(\'Expand\');
		}
		
     jQuery(\'#slidebox2\').slideToggle("medium");
        // alert(id);
     return false;
});


jQuery(\'#payment_method\').change(function() {
		 
		if (jQuery(\'#payment_method\').val() == \'Check\') {
			jQuery(\'#check_payment\').css(\'display\',\'\');
			jQuery(\'#credit_card_payment\').css(\'display\',\'none\');
		}
		if (jQuery(\'#payment_method\').val() == \'Credit Card\') {
			jQuery(\'#credit_card_payment\').css(\'display\',\'\');
			jQuery(\'#check_payment\').css(\'display\',\'none\');
		}
		if (jQuery(\'#payment_method\').val() == \'\') {
			jQuery(\'#credit_card_payment\').css(\'display\',\'none\');
			jQuery(\'#check_payment\').css(\'display\',\'none\');
		}
});

';
if ($adv_info_bu_tbl->payment_method != '') {
	if($adv_info_bu_tbl->payment_method == 'Check') {
		$retail_customer_form .= 'jQuery(\'#check_payment\').css(\'display\',\'\');'.LB;
	} elseif ($adv_info_bu_tbl->payment_method == 'Credit Card') {
		$retail_customer_form .= 'jQuery(\'#credit_card_payment\').css(\'display\',\'\');'.LB;
	}
}
		$retail_customer_form .= '}); 
</script><table id="slidebox2" width="100%">';

		$field_type_array = unserialize(PAYMENT_TYPES);
			
		$type_dd = '';
		foreach ($field_type_array as $id => $title) {
			$type_dd .= '<option '.($title == $adv_info_bu_tbl->payment_method ? 'selected="selected"' : '').'>'.$title.'</option>'.LB; 
		}
		
		$cctype_dd = '';
		$field_type_array = unserialize(CC_TYPES);
		foreach ($field_type_array as $title) {
			$cctype_dd .= '<option '.($title == $adv_info_bu_tbl->credit_card_type ? 'selected="selected"' : '').'>'.$title.'</option>'.LB; 
		}
		$retail_customer_form .= table_form_field('Payment Method:','<select id="payment_method" name="payment_method">'.$type_dd.'</select>');
		$retail_customer_form .= '<tr><td colspan="2">';
		
		$retail_customer_form .= '<table style="display:none" id="check_payment" width="100%">';
		$retail_customer_form .= table_form_field('Routing Number:','<input name="check_routing_num" type="text" size="20" value="'.$adv_info_bu_tbl->check_routing_num.'">');
		$retail_customer_form .= table_form_field('Account Number:','<input name="check_account_num" type="text" size="20" value="'.$adv_info_bu_tbl->check_account_num.'">');
		$retail_customer_form .= table_form_field('Bank Name:','<input name="bank_name" type="text" size="20" value="'.$adv_info_bu_tbl->bank_name.'">');
		
		$retail_customer_form .= table_form_field('Bank State:','<select name="bank_state">'.gen_state_dd($adv_info_bu_tbl->bank_state).'</select>');
		$retail_customer_form .= table_form_field('Drivers License Number:','<input name="drivers_license_num" type="text" size="20" value="'.$adv_info_bu_tbl->drivers_license_num.'">');
		
		$retail_customer_form .= table_form_field('Drivers License State:','<select name="drivers_license_state">'.gen_state_dd($adv_info_bu_tbl->drivers_license_state).'</select>');
		$retail_customer_form .= '</table>';

		$retail_customer_form .= '<table style="display:none" id="credit_card_payment" width="100%">';
		$retail_customer_form .= table_form_field('Credit Card Type:','<select name="credit_card_type">'.$cctype_dd.'</select>');
		$retail_customer_form .= table_form_field('Credit Card Number:','<input name="cc_number" type="text" size="20" value="'.$adv_info_bu_tbl->cc_number.'">');
		$retail_customer_form .= table_form_field('CVV:','<input name="cvv" type="text" size="4" value="'.$adv_info_bu_tbl->cvv.'">');
		
		$set_expiration = explode("/",$adv_info_bu_tbl->cc_exp);
		
		// print exp dd
		$years_dd = '';
		$cur_year = date("Y");
		$fut_year = $cur_year+10;
		while($cur_year <= $fut_year) {
			$years_dd .= '<option '.($set_expiration[1] == $cur_year ? 'selected' : '').'>'.$cur_year.'</option>';
			$cur_year++;
		}

		$month = 1;
		$months_dd = '';
		while($month <= 12) {
			$months_dd .= '<option '.($set_expiration[0] == $month ? 'selected' : '').'>'.$month.'</option>';
			$month++;
		}
		$retail_customer_form .= table_form_field('Credit Card Expiration:','Month:<select name="cc_exp_month">'.$months_dd.'</select> / Year: <select name="cc_exp_year">'.$years_dd.'</select>');
		$retail_customer_form .= '</table></td></tr>';

$retail_customer_form .= '</table></td></tr>';

		$retail_customer_form .= '<tr id="slideboxhead3"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide3" name="1"><font color="red">Expand</font></a></td><td class="form_title_header">Level Settings </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead3").css("cursor","pointer");
jQuery(\'#slidebox3\').hide();
jQuery(\'a.slide3\').text(\'Expand\');
			
jQuery(\'#slideboxhead3\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		if (jQuery(\'a.slide3\').text() == \'Expand\') {
			jQuery(\'a.slide3\').text(\'Collapse\');
		} else {
			jQuery(\'a.slide3\').text(\'Expand\');
		}
		
     jQuery(\'#slidebox3\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox3" width="100%">';

		$retail_customer_form .= table_form_field('<span class="required">*Customer Level:</span>',$this->customer_level_drop_down($adv_info_bu_tbl->customer_level));

		$retail_customer_form .= table_form_field('Renew Account After Initial Period:','<input name="customer_level_renew" type="checkbox" value="1" '.(!empty($adv_info_bu_tbl->customer_level_renew) ? 'checked disabled' : '').'>');
				
		$retail_customer_form .= table_form_field('Customer Level Expiration:','
<script>DateInput(\'customer_level_exp\', true, \'YYYY-MM-DD\',\''.(!empty($adv_info_bu_tbl->customer_level_exp) ? $adv_info_bu_tbl->customer_level_exp : date("Y-m-d")).'\')</script>');
		$retail_customer_form .= table_form_field('Customer Level Renewal Date:','<script>DateInput(\'customer_level_renewal_date\', true, \'YYYY-MM-DD\',\''.(!empty($adv_info_bu_tbl->customer_level_renewal_date) ? $adv_info_bu_tbl->customer_level_renewal_date : date("Y-m-d")).'\')</script>');

$retail_customer_form .= '</table></td></tr>';

		$retail_customer_form .= '<tr id="slideboxhead4"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide10" name="1"><font color="red">Expand</font></a></td><td class="form_title_header">Hours Of Operation </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead4").css("cursor","pointer");
jQuery(\'#slidebox10\').hide();
jQuery(\'a.slide10\').text(\'Expand\');
			
jQuery(\'#slideboxhead4\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		if (jQuery(\'a.slide10\').text() == \'Expand\') {
			jQuery(\'a.slide10\').text(\'Collapse\');
		} else {
			jQuery(\'a.slide10\').text(\'Expand\');
		}
		
     jQuery(\'#slidebox10\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox10" width="100%">';
		
		// builds hours of operation selection
		$hours_list = unserialize(HOURS_SELECT);
		$days_array = unserialize(DAYS_ARRAY);
		if(!is_array($adv_info_bu_tbl->hours_operation) || empty($adv_info_bu_tbl->hours_operation)) {
			// if hours of operation settings do not exist print HOP form
			$hours_of_operation = '<input name="hours_operation[selected][type]" type="radio" value="nohours" /> Do Not Display Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="24hr" /> Open 24 Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="select" /> Hours Select Below<br>';
			$hours_of_operation .= '<table>';
			$hours_of_operation .= '<tr>';
			reset($days_array);
			foreach($days_array as $value) {
				$hours_of_operation .= '<th>'.$value.'</th>';
			}
			$hours_of_operation .= '</tr><tr>';
			reset($days_array);
			reset($hours_list);
			// draw days selection
			foreach($days_array as $day_value) {
				$hours_of_operation .= '<td>';
				$hours_of_operation .= '<select name="hours_operation[selected]['.$day_value.'open]">';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.('9:00 AM' == $value ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= 'to<br>';
				$hours_of_operation .= '<select name="hours_operation[selected]['.$day_value.'close]">';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.('5:00 PM' == $value ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= '</td>';
			}
			$hours_of_operation .= '</tr></table>';
		} else {
			// if HOP values are set load values into form
			if (!is_array($adv_info_bu_tbl->hours_operation)) {
				$hours_operation = unserialize($adv_info_bu_tbl->hours_operation);
			} else {
				$hours_operation = $adv_info_bu_tbl->hours_operation;
			}
			$hours_of_operation = '<input name="hours_operation[selected][type]" type="radio" value="nohours" '.($hours_operation['selected']['type'] == 'nohours' ? 'checked' : '').' /> Do Not Display Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="24hr" '.($hours_operation['selected']['type'] == '24hr' ? 'checked' : '').' /> Open 24 Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="select" '.($hours_operation['selected']['type'] == 'select' ? 'checked' : '').' /> Hours Select Below<br>';
			$hours_of_operation .= '<table>';
			$hours_of_operation .= '<tr>';
			reset($days_array);
			foreach($days_array as $value) {
				$hours_of_operation .= '<th>'.$value.'</th>';
			}
			$hours_of_operation .= '</tr><tr>';
			reset($days_array);
			reset($hours_list);
			// draw days selection
			foreach($days_array as $day_value) {
				$hours_of_operation .= '<td>';
				$hours_of_operation .= '<select name="hours_operation[selected]['.$day_value.'open]">';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.($hours_operation['selected'][$day_value.'open'] == $value ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= 'to<br>';
				$hours_of_operation .= '<select name="hours_operation[selected]['.$day_value.'close]">';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.($hours_operation['selected'][$day_value.'close'] == $value ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= '</td>';
			}
			$hours_of_operation .= '</tr></table>';			
		}
		
		$retail_customer_form .= table_form_field('Hours Of Operation:',$hours_of_operation);

		// build payment method options
		$payment_methods = $adv_pmt_mtds_tbl->get_all();
				
		$payment_method_sel = '';
		foreach($payment_methods as $value) {
			$payment_method_sel .= '<input name="payment_options['.$value['id'].']" type="checkbox" value="1"'.(is_array($adv_info_bu_tbl->payment_options) ? $adv_info_bu_tbl->payment_options[$value['id']] == 1 ? 'checked' : '' : '' ).' />: '.$value['method'].'<br>';
		}
		
$retail_customer_form .= '</table></td></tr>';		

		$retail_customer_form .= '<tr id="slideboxhead5"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide9" name="1"><font color="red">Expand</font></a></td><td class="form_title_header">Payment Options </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead5").css("cursor","pointer");
jQuery(\'#slidebox9\').hide();
jQuery(\'a.slide9\').text(\'Expand\');
			
jQuery(\'#slideboxhead5\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		if (jQuery(\'a.slide9\').text() == \'Expand\') {
			jQuery(\'a.slide9\').text(\'Collapse\');
		} else {
			jQuery(\'a.slide9\').text(\'Expand\');
		}
		
     jQuery(\'#slidebox9\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox9" width="100%">';

		$retail_customer_form .= table_form_field('Payment Options:',$payment_method_sel);

$retail_customer_form .= '</table></td></tr>';
		
		$retail_customer_form .= '<tr id="slideboxhead6"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide5" name="1"><font color="red">Expand</font></a></td><td class="form_title_header">Categories </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead6").css("cursor","pointer");
jQuery(\'#slidebox5\').hide();
jQuery(\'a.slide5\').text(\'Expand\');
			
jQuery(\'#slideboxhead6\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		if (jQuery(\'a.slide5\').text() == \'Expand\') {
			jQuery(\'a.slide5\').text(\'Collapse\');
		} else {
			jQuery(\'a.slide5\').text(\'Expand\');
		}
		
     jQuery(\'#slidebox5\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox5" width="100%">';

		// load category list class
		require(CLASSES_DIR.'sections/category_select.php');
		$category_select_pg = new category_select_pg;
		
		$sql_query = "SELECT
						id,
						category_id
					 FROM
						advertiser_categories_backup
					WHERE
					advertiser_id = '".$adv_info_bu_tbl->id."';";
		
		$rows = $dbh->queryAll($sql_query);
		foreach($rows as $cur_sel) {
			$category_select_array[$cur_sel['category_id']] = 1;
		}
		
		$category_select_pg->selected_array = $category_select_array;

		$retail_customer_form .= table_form_field('<span class="required">*Category:</span>',$category_select_pg->list_categories());

$retail_customer_form .= '</table></td></tr>';		

		$retail_customer_form .= '<tr id="slideboxhead7"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide4" name="1"><font color="red">Expand</font></a></td><td class="form_title_header">Contact Address Information </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead7").css("cursor","pointer");
jQuery(\'#slidebox4\').hide();
jQuery(\'a.slide4\').text(\'Expand\');
			
jQuery(\'#slideboxhead7\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		if (jQuery(\'a.slide4\').text() == \'Expand\') {
			jQuery(\'a.slide4\').text(\'Collapse\');
		} else {
			jQuery(\'a.slide4\').text(\'Expand\');
		}
		
     jQuery(\'#slidebox4\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox4" width="100%">';

		$retail_customer_form .= table_form_field('Hide Address From Listing:','<input name="hide_address" type="checkbox" value="1" '.($adv_info_bu_tbl->hide_address == 1 ? 'checked' : '').' />');
		$retail_customer_form .= table_form_field('<span class="required">*Contact First Name:</span>','<input name="first_name" type="text" size="50" maxlength="100" value="'.$adv_info_bu_tbl->first_name.'">');
		$retail_customer_form .= table_form_field('<span class="required">*Contact Last Name:</span>','<input name="last_name" type="text" size="50" maxlength="100" value="'.$adv_info_bu_tbl->last_name.'">');
		$retail_customer_form .= table_form_field('<span class="required">*Contact Address 1:</span>','<input name="address_1" type="text" size="50" maxlength="120" value="'.$adv_info_bu_tbl->address_1.'">');
		$retail_customer_form .= table_form_field('Contact Address 2:','<input name="address_2" type="text" size="50" maxlength="120" value="'.$adv_info_bu_tbl->address_2.'">');
		$retail_customer_form .= table_form_field('<span class="required">*Contact City:</span>','<input name="city" type="text" size="50" maxlength="100" value="'.$adv_info_bu_tbl->city.'">');
						
		$retail_customer_form .= table_form_field('<span class="required">*Contact State:</span>','<select name="state">'.gen_state_dd($adv_info_bu_tbl->state).'</select>');
		
		$retail_customer_form .= table_form_field('<span class="required">*Contact Zip:</span>','<input name="zip" type="text" size="15" maxlength="15" value="'.$adv_info_bu_tbl->zip.'">');
		$retail_customer_form .= table_form_field('Contact Phone Number:','<input name="phone_number" type="text" size="15" maxlength="15" value="'.$adv_info_bu_tbl->phone_number.'">');
		$retail_customer_form .= table_form_field('Contact Fax Number:','<input name="fax_number" type="text" size="15" maxlength="15" value="'.$adv_info_bu_tbl->fax_number.'">');
		$retail_customer_form .= table_form_field('<span class="required">*Contact Email Address:</span>','<input name="email_address" type="text" size="50" maxlength="160" value="'.$adv_info_bu_tbl->email_address.'">');

$retail_customer_form .= '</table></td></tr>';
		
		$retail_customer_form .= table_span_form_field('<center><input id="advert_id" name="id" type="hidden" value="'.$adv_info_bu_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
		
	return $retail_customer_form;
	}
	
		
	// category drop down menu
	function category_drop_down($selected_id = '') {
			global $dbh;
		
		$parent_drop_down = '<select name="category" id="parent_cat_dd">'.LB;
		
		$sql_query = "SELECT
						id,
						category_name,
						parent_category_id
					 FROM
						categories
					 WHERE
						zip_id is NULL
					 AND
						parent_category_id = 0
					 ORDER BY sort_order ASC, category_name ASC
					 ;";
		$rows = $dbh->queryAll($sql_query);
		
		foreach ($rows as $categories) {
		$ind = '--';
		
		// reset last child id
		$this->last_child_id = '';
		
		// draw child drop down
		$parent_drop_down_child = $this->parent_dd_child_chk($categories['id'],$ind,$selected_id);
		
		// draw parent drop down
		$parent_drop_down_parent = '<option value="'.(!empty($this->last_child_id) ? $this->last_child_id : $categories['id']).'" '.($selected_id == $categories['id'] ? 'selected="selected" ' : '').'>'.$categories['category_name'].'</option>'.LB;
		
		$parent_drop_down .= $parent_drop_down_parent . $parent_drop_down_child;
		}
		
		$parent_drop_down .= '</select>'.LB;
		
	return $parent_drop_down;
	}
	
	// check for child categories
	function parent_dd_child_chk($cid,$ind,$selected_id = '') {
			global $dbh;
			
		$sql_query = "SELECT
						id,
						category_name,
						parent_category_id
					 FROM
						categories
					 WHERE
						zip_id is NULL
					 AND
						parent_category_id = '".$cid."'
					 ORDER BY sort_order ASC, category_name ASC
					 ;";
		$rows = $dbh->queryAll($sql_query);
		
		foreach ($rows as $categories) {
		$parent_drop_down .= '<option value="'.$categories['id'].'" '.($selected_id == $categories['id'] ? 'selected="selected" ' : '').'>'.$ind.' '.$categories['category_name'].'</option>'.LB;
		
		$parent_drop_down .= $this->parent_dd_child_chk($categories['id'],$ind.'--');
		
		$this->last_child_id = $categories['id'];
		}
		
	return $parent_drop_down;
	}
		
		// check form submission values
	function form_check() {
			global $adv_info_bu_tbl;
		
		// required fields array
		$required_fields = array('Company Name'=> $adv_info_bu_tbl->company_name,
								'Username' => $adv_info_bu_tbl->username,
								'Contact First Name' => $adv_info_bu_tbl->first_name,
								'Contact Last Name' => $adv_info_bu_tbl->last_name,
								'Contact Address 1' => $adv_info_bu_tbl->address_1,
								'Contact City' => $adv_info_bu_tbl->city,
								'Contact State' => $adv_info_bu_tbl->state,
								'Contact Zip' => $adv_info_bu_tbl->zip,
								'Contact Email Address' => $adv_info_bu_tbl->email_address
								);
			
		// check error values and write error array					
		foreach($required_fields as $field_name => $output) {

			if (empty($output)) {
				$errors_array[] = $field_name;
			}
		
		}
		
		if (!empty($errors_array)) {
			$error_message = 'You did not supply a value for these fields: ' . implode(', ',$errors_array);
		}
		
		if ($adv_info_bu_tbl->username_check() > 0) {
			$error_message .= '<br>Username has already been assigned to another customer. Please choose another.';
		}
		
	return $error_message;
	}

}

?>