<?PHP

// class for interacting with advertiser_info_backup table
class adv_info_bu_tbl {
	public $id;
	public $approved;
	public $approval_date;
	public $update_approval;
	public $company_name;
	public $customer_description;
	public $longitude;
	public $latitude;
	public $username;
	public $password;
	public $customer_level;
	public $customer_level_exp;
	public $customer_level_renewal_date;
	public $customer_level_renew;
	public $website;
	public $category;
	public $bbb_member;
	public $link_partner;
	public $affiliate_code;
	public $link_affiliate_code;
	public $hours_operation;
	public $products_services;
	public $payment_options;
	public $payment_method;
	public $certificate_levels;
	public $certificate_requirements;
	public $credit_card_type;
	public $cc_number;
	public $cvv;
	public $cc_exp;
	public $check_routing_num;
	public $check_account_num;
	public $bank_name;
	public $bank_state;
	public $drivers_license_num;
	public $drivers_license_state;
	public $hide_address;
	public $first_name;
	public $last_name;
	public $address_1;
	public $address_2;
	public $city;
	public $state;
	public $zip;
	public $phone_number;
	public $fax_number;
	public $email_address;
	public $account_enabled;
	public $image;
	public $date_created;
	public $last_ip;
	public $last_login;
	public $last_session_id;
	public $allow_multiple_logins;
	public $authorization_code;
	public $email_authorized;
	// table name used throughout queries within page
	private $tbl_nme = "advertiser_info_backup";

	public function __construct() {
		$this->reset_vars();
	}
	
	public function reset_vars() {
		$this->id = NULL;
		$this->approved = NULL;
		$this->approval_date = NULL;
		$this->update_approval = NULL;
		$this->company_name = NULL;
		$this->customer_description = NULL;
		$this->longitude = NULL;
		$this->latitude = NULL;
		$this->username = NULL;
		$this->password = NULL;
		$this->customer_level = NULL;
		$this->customer_level_exp = NULL;
		$this->customer_level_renewal_date = NULL;
		$this->customer_level_renew = NULL;
		$this->website = NULL;
		$this->category = NULL;
		$this->bbb_member = NULL;
		$this->link_partner = NULL;
		$this->affiliate_code = NULL;
		$this->hours_operation = NULL;
		$this->link_affiliate_code = NULL;
		$this->products_services = NULL;
		$this->payment_options = NULL;
		$this->payment_method = NULL;
		$this->certificate_levels = NULL;
		$this->certificate_requirements = NULL;
		$this->credit_card_type = NULL;
		$this->cc_number = NULL;
		$this->cvv = NULL;
		$this->cc_exp = NULL;
		$this->bank_name = NULL;
		$this->bank_state = NULL;
		$this->drivers_license_num = NULL;
		$this->drivers_license_state = NULL;
		$this->check_routing_num = NULL;
		$this->check_account_num = NULL;
		$this->hide_address = NULL;
		$this->first_name = NULL;
		$this->last_name = NULL;
		$this->address_1 = NULL;
		$this->address_2 = NULL;
		$this->city = NULL;
		$this->state = NULL;
		$this->zip = NULL;
		$this->phone_number = NULL;
		$this->fax_number = NULL;
		$this->email_address = NULL;
		$this->account_enabled = NULL;
		$this->image = NULL;
		$this->date_created = NULL;
		$this->last_ip = NULL;
		$this->last_login = NULL;
		$this->last_session_id = NULL;
		$this->allow_multiple_logins = 0;
		$this->authorization_code = NULL;
		$this->email_authorized = NULL;
	}

	// insert selected categories
	public function insert_selected_categories($selected_categories,$advertiser_id) {
		global $dbh;

		$sql_query = "SELECT
						id
					 FROM
						categories
					;";
				
		$stmt1 = $dbh->prepare($sql_query);					 
		$result = $stmt->execute();
		
		while($cur_sel = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			if($selected_categories[$cur_sel['id']] == 1) {
				// check if setting exists within current selected list
				$sql_query = "SELECT
								id
							 FROM
								advertiser_categories_backup
							 WHERE
								advertiser_id = ?
							 AND
								category_id = ?
							 LIMIT 1;";
				
				$values2 = array(
								$advertiser_id,
								$cur_sel['id']
								);

				$stmt2 = $dbh->prepare($sql_query);					 
				$result2 = $stmt2->execute($values2);
				
				$rows_check = $result2->fetchRow(MDB2_FETCHMODE_ASSOC);
		
				// add or update category setting
				if($rows_check['id'] > 0) {
				
					$sql_query = "UPDATE advertiser_categories_backup SET category_id = ? WHERE id = ?;";
							 
					$update_vals3 = array(
										$cur_sel['id'],
										$rows_check['id']
										);
										
					$stmt3 = $dbh->prepare($sql_query);
					$stmt3->execute($update_vals3);
			
				} else {
				
					$sql_query = "INSERT INTO advertiser_categories_backup (category_id,advertiser_id) VALUES (?,?);";
					$update_vals3 = array(
										$cur_sel['id'],
										$advertiser_id
										);
										
					$stmt3 = $dbh->prepare($sql_query);
					$stmt3->execute($update_vals3);
			
				}
			} else {
			
				// remove category selection if not selected
				$sql_query = "DELETE FROM advertiser_categories_backup WHERE category_id = ? AND advertiser_id = ?;";
						 
				$update_vals3 = array(
									$cur_sel['id'],
									$advertiser_id
									);
									
				$stmt3 = $dbh->prepare($sql_query);
				$stmt3->execute($update_vals3);
			}
	
			// clear result set
			$result2->free();
		}

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}

	// insert new advertiser
	public function insert() {
		global $dbh;
				
		$this->set_long_lat();
		
		$today = date("Y-m-d H:i:s", time()); 			
		$sql_query = "INSERT INTO
						".$this->tbl_nme."
					 (
					 	id,
						approved,
						approval_date,
						update_approval,
						company_name,
						customer_description,
						longitude,
						latitude,
						username,
						password,
						customer_level,
						customer_level_exp,
						customer_level_renewal_date,
						customer_level_renew,
						website,
						category,
						bbb_member,
						link_partner,
						affiliate_code,
						hours_operation,
						link_affiliate_code,
						products_services,
						payment_options,
						payment_method,
						certificate_levels,
						certificate_requirements,
						credit_card_type,
						cc_number,
						cvv,
						cc_exp,
						check_account_type,
						check_routing_num,
						check_account_num,
						bank_name,
						bank_state,
						drivers_license_num,
						drivers_license_state,
						hide_address,
						first_name,
						last_name,
						address_1,
						address_2,
						city,
						state,
						zip,
						phone_number,
						fax_number,
						email_address,
						account_enabled,
						image,
						date_created,
						allow_multiple_logins,
						authorization_code
					 )
					 VALUES
					 (
					 	 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?,
						 ?
					 );
					 ";
				 
		$update_vals = array(
							$this->id,
							$this->approved,
							$this->approval_date,
							$this->update_approval,
							$this->company_name,
							$this->customer_description,
							$this->longitude,
							$this->latitude,
							$this->username,
							$this->password,
							$this->customer_level,
							$this->customer_level_exp,
							$this->customer_level_renewal_date,
							$this->customer_level_renew,
							$this->website,
							$this->category,
							$this->bbb_member,
							$this->link_partner,
							$this->affiliate_code,
							$this->hours_operation,
							$this->link_affiliate_code,
							$this->products_services,
							$this->payment_options,
							$this->payment_method,
							$this->certificate_levels,
							$this->certificate_requirements,
							$this->credit_card_type,
							$this->cc_number,
							$this->cvv,
							$this->cc_exp,
							$this->check_account_type,
							$this->check_routing_num,
							$this->check_account_num,
							$this->bank_name,
							$this->bank_state,
							$this->drivers_license_num,
							$this->drivers_license_state,
							$this->hide_address,
							$this->first_name,
							$this->last_name,
							$this->address_1,
							$this->address_2,
							$this->city,
							$this->state,
							$this->zip,
							$this->phone_number,
							$this->fax_number,
							$this->email_address,
							$this->account_enabled,
							$this->image,
							$today,
							$this->allow_multiple_logins,
							$this->authorization_code
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// update existing advertiser_info_backup
	public function update() {
			global $dbh;
		
		$this->set_long_lat();
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						approved = ?,
						approval_date = ?,
						update_approval =?,
						company_name = ?,
						customer_description = ?,
						longitude = ?,
						latitude = ?,
						username = ?,
						password = ?,
						customer_level = ?,
						customer_level_exp = ?,
						customer_level_renewal_date = ?,
						customer_level_renew = ?,
						website = ?,
						category = ?,
						bbb_member = ?,
						link_partner = ?,
						affiliate_code = ?,
						hours_operation = ?,
						link_affiliate_code = ?,
						products_services = ?,
						payment_options = ?,
						payment_method = ?,
						certificate_levels = ?,
						certificate_requirements = ?,
						credit_card_type = ?,
						cc_number = ?,
						cvv = ?,
						cc_exp = ?,
						check_routing_num = ?,
						check_account_num = ?,
						bank_name = ?,
						bank_state = ?,
						drivers_license_num = ?,
						drivers_license_state = ?,
						hide_address = ?,
						first_name = ?,
						last_name = ?,
						address_1 = ?,
						address_2 = ?,
						city = ?,
						state = ?,
						zip = ?,
						phone_number = ?,
						fax_number = ?,
						email_address = ?,
						account_enabled = ?,
						image = ?,
						allow_multiple_logins = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->approved,
							$this->approval_date,
							$this->update_approval,
							$this->company_name,
							$this->customer_description,
							$this->longitude,
							$this->latitude,
							$this->username,
							$this->password,
							$this->customer_level,
							$this->customer_level_exp,
							$this->customer_level_renewal_date,
							$this->customer_level_renew,
							$this->website,
							$this->category,
							$this->bbb_member,
							$this->link_partner,
							$this->affiliate_code,
							$this->hours_operation,
							$this->link_affiliate_code,
							$this->products_services,
							$this->payment_options,
							$this->payment_method,
							$this->certificate_levels,
							$this->certificate_requirements,
							$this->credit_card_type,
							$this->cc_number,
							$this->cvv,
							$this->cc_exp,
							$this->check_routing_num,
							$this->check_account_num,
							$this->bank_name,
							$this->bank_state,
							$this->drivers_license_num,
							$this->drivers_license_state,
							$this->hide_address,
							$this->first_name,
							$this->last_name,
							$this->address_1,
							$this->address_2,
							$this->city,
							$this->state,
							$this->zip,
							$this->phone_number,
							$this->fax_number,
							$this->email_address,
							$this->account_enabled,
							$this->image,
							$this->allow_multiple_logins,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// update existing advertiser_info_backup
	public function update_advertiser_info_backup_address() {
			global $dbh;
				
		$this->set_long_lat();
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						first_name = ?,
						last_name = ?,
						address_1 = ?,
						address_2 = ?,
						city = ?,
						state = ?,
						zip = ?,
						phone_number = ?,
						fax_number = ?,
						email_address = ?,
						longitude = ?,
						latitude = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->first_name,
							$this->last_name,
							$this->address_1,
							$this->address_2,
							$this->city,
							$this->state,
							$this->zip,
							$this->phone_number,
							$this->fax_number,
							$this->email_address,
							$this->longitude,
							$this->latitude,
							$this->id
							);

		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// update existing advertiser_info_backup
	public function update_backup_business() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						hide_address =?,
						company_name = ?,
						customer_description = ?,
						website = ?,
						category = ?,
						link_affiliate_code = ?,
						hours_operation = ?,
						link_affiliate_code = ?,
						products_services = ?,
						payment_options = ?,
						image = ?,
						allow_multiple_logins = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->hide_address,
							$this->company_name,
							$this->customer_description,
							$this->website,
							$this->category,
							$this->link_affiliate_code,
							$this->hours_operation,
							$this->link_affiliate_code,
							$this->products_services,
							$this->payment_options,
							$this->image,
							$this->allow_multiple_logins,
							$this->id
							);

		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// update existing advertiser level
	public function update_backup_level() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						customer_level = ?,
						customer_level_exp = ?,
						customer_level_renewal_date = ?,
						bbb_member = ?,
						link_partner = ?
						
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->customer_level,
							$this->customer_level_exp,
							$this->customer_level_renewal_date,
							$this->bbb_member,
							$this->link_partner,
							$this->id
							);

		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// update advertiser enable status
	public function update_enable_status() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						account_enabled = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->account_enabled,
							$this->id
							);

		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// update advertiser renewal status
	public function update_renewal_status() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						customer_level_renew = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->customer_level_renew,
							$this->id
							);

		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// update existing advertiser payment info
	public function update_backup_payment_info() {
			global $dbh;
		
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						payment_method = ?,
						credit_card_type = ?,
						cc_number = ?,
						cvv = ?,
						cc_exp = ?,
						check_routing_num = ?,
						bank_name = ?,
						bank_state = ?,
						drivers_license_num = ?,
						drivers_license_state = ?
					 WHERE
						id = ?
					 ;";
				 
		$update_vals = array(
							$this->payment_method,
							$this->credit_card_type,
							$this->cc_number,
							$this->cvv,
							$this->cc_exp,
							$this->check_routing_num,
							$this->check_account_num,
							$this->bank_name,
							$this->bank_state,
							$this->drivers_license_num,
							$this->drivers_license_state,
							$this->id
							);

		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
	}

	// check for an existing advertiser_info_backup password
	public function pull_existing_password($cust_id) {
			global $dbh;
	
		$sql_query = "SELECT
						password
					 FROM
						".$this->tbl_nme."
					 WHERE
						id = ?
					  LIMIT 1;";

		$values = array(
						$cust_id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
			
	return $row['password'];
	}

	// write post vars to class variables
	public function get_post_vars() {
		
		$this->id = $_POST['id'];
		
		$this->approved = $_POST['approved'];
		if ($_POST['approved'] == 1 && !empty($_POST['approval_date'])) {
			$this->approval_date = $_POST['approval_date'];
		} elseif ($_POST['approved'] == 1) {
			$this->approval_date = date("Y-m-d");
		}
		
		$this->update_approval = $_POST['update_approval'];
		
		$this->company_name = $_POST['company_name'];
		$this->customer_description = $_POST['customer_description'];
		$this->longitude = $_POST['longitude'];
		$this->latitude = $_POST['latitude'];
		$this->username = $_POST['username'];
		$this->password = (!empty($_POST['password']) ? encrypt_password($_POST['password']) : $this->pull_existing_password($_POST['id']));
		$this->customer_level = $_POST['customer_level'];
		$this->customer_level_exp = $_POST['customer_level_exp'];
		$this->customer_level_renewal_date = $_POST['customer_level_renewal_date'];
		$this->customer_level_renew = $_POST['customer_level_renew'];
		$this->website = $_POST['website'];
		$this->category = $_POST['category'];
		$this->bbb_member = $_POST['bbb_member'];
		$this->hours_operation = serialize($_POST['hours_operation']);
		$this->products_services = $_POST['products_services'];
		$this->payment_options = serialize($_POST['payment_options']);
		$this->payment_method = $_POST['payment_method'];
		$this->link_partner = $_POST['link_partner'];
		$this->allow_multiple_logins = $_POST['allow_multiple_logins'];
		
		// build certificate requirements array
		if (is_array($_POST['certificate_levels'])) {
			foreach($_POST['certificate_levels'] as $id => $value) {
				$_POST['certificate_requirements'][$id]['type'] = $_POST['requirements'][$id];
				$_POST['certificate_requirements'][$id]['value'] = $_POST['requirement_text'][$id][$_POST['requirements'][$id]];
			}
		}
		$this->certificate_requirements = serialize($_POST['certificate_requirements']);
		
		// handles selected certificate levels
		$this->certificate_levels = serialize($_POST['certificate_levels']);
		
		$this->cc_number = $_POST['cc_number'];
		$this->credit_card_type = $_POST['credit_card_type'];
		$this->cvv = $_POST['cvv'];

		$this->check_routing_num = $_POST['check_routing_num'];
		$this->check_account_num = $_POST['check_account_num'];
		$this->bank_name = $_POST['bank_name'];
		$this->bank_state = $_POST['bank_state'];
		$this->drivers_license_num = $_POST['drivers_license_num'];
		$this->drivers_license_state = $_POST['drivers_license_state'];
		
		$this->cc_exp = (!empty($_POST['cc_exp_month']) && !empty($_POST['cc_exp_month']) ? $_POST['cc_exp_month'] . "/" . $_POST['cc_exp_year'] : $_POST['cc_exp']);
		
		if (empty($_POST['affiliate_code'])) {
		$this->affiliate_code = $this->generate_affiliate_code();
		} else {
		$this->affiliate_code = $_POST['affiliate_code'];
		}
		
		$this->link_affiliate_code = $_POST['link_affiliate_code'];
		
		$this->hide_address = $_POST['hide_address'];
		$this->first_name = $_POST['first_name'];
		$this->last_name = $_POST['last_name'];
		$this->address_1 = $_POST['address_1'];
		$this->address_2 = $_POST['address_2'];
		$this->city = $_POST['city'];
		$this->state = $_POST['state'];
		$this->zip = $_POST['zip'];
		$this->phone_number = $_POST['phone_number'];
		$this->fax_number = $_POST['fax_number'];
		$this->email_address = $_POST['email_address'];
		
		$this->account_enabled = $_POST['account_enabled'];
		
		// upload new image
		$target_path = CUSTOMER_IMAGES_DIRECTORY . md5($_POST['username']) . "-" . basename( $_FILES['image']['name']); 
		if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
			$this->image = md5($_POST['username']) . "-" . basename( $_FILES['image']['name']);
		} else {
			$this->image = $_POST['old_image'];
		}
		
	}
	
	// get vars from database
	public function get_db_vars($id) {
			global $dbh;
		
		$sql_query = "SELECT
							id,
							approved,
							approval_date,
							update_approval,
							company_name,
							customer_description,
							longitude,
							latitude,
							username,
							password,
							customer_level,
							customer_level_exp,
							customer_level_renewal_date,
							website,
							category,
							bbb_member,
							link_partner,
							affiliate_code,
							hours_operation,
							link_affiliate_code,
							products_services,
							payment_options,
							payment_method,
							certificate_levels,
							certificate_requirements,
							cc_number,
							credit_card_type,
							cvv,
							cc_exp,
							check_routing_num,
							check_account_num,
							bank_name,
							bank_state,
							drivers_license_num,
							drivers_license_state,
							hide_address,
							first_name,
							last_name,
							address_1,
							address_2,
							city,
							state,
							zip,
							phone_number,
							fax_number,
							email_address,
							account_enabled,
							image,
							date_created,
							last_ip,
							last_login,
							last_session_id,
							allow_multiple_logins,
							authorization_code,
							email_authorized
						 FROM
							".$this->tbl_nme."
						 WHERE
							id = ?
						  LIMIT 1;";

		$values = array(
						$id
						);
		
		$row = db_memc_str($sql_query,$values);
		
		if(!empty($row)){
		  $this->id = $row['id'];
		  $this->approved = $row['approved'];
		  $this->approval_date = $row['approval_date'];
		  $this->update_approval = $row['update_approval'];
		  $this->company_name = $row['company_name'];
		  $this->customer_description = strip_tags($row['customer_description']);
		  $this->longitude = $row['longitude'];
		  $this->latitude = $row['latitude'];
		  $this->username = $row['username'];
		  $this->password = $row['password'];
		  $this->hours_operation = unserialize($row['hours_operation']);
		  $this->customer_level = $row['customer_level'];
		  $this->customer_level_exp = $row['customer_level_exp'];
		  $this->customer_level_renewal_date = $row['customer_level_renewal_date'];
		  $this->website = $row['website'];
		  $this->category = $row['category'];
		  $this->bbb_member = $row['bbb_member'];
		  $this->link_partner = $row['link_partner'];
		  $this->affiliate_code = $row['affiliate_code'];
		  $this->link_affiliate_code = $row['link_affiliate_code'];
		  $this->products_services = strip_tags($row['products_services']);
		  $this->payment_options = unserialize($row['payment_options']);
		  $this->payment_method = $row['payment_method'];
		  $this->certificate_levels = unserialize($row['certificate_levels']);
		  $this->certificate_requirements = unserialize($row['certificate_requirements']);
		  $this->credit_card_type = $row['credit_card_type'];
		  $this->cc_number = $row['cc_number'];
		  $this->cvv = $row['cvv'];
		  $this->cc_exp = $row['cc_exp'];
		  $this->bank_name = $row['bank_name'];
		  $this->bank_state = $row['bank_state'];
		  $this->drivers_license_num = $row['drivers_license_num'];
		  $this->drivers_license_state = $row['drivers_license_state'];
		  $this->check_routing_num = $row['check_routing_num'];
		  $this->check_account_num = $row['check_account_num'];
		  $this->hide_address = $row['hide_address'];
		  $this->first_name = $row['first_name'];
		  $this->last_name = $row['last_name'];
		  $this->address_1 = $row['address_1'];
		  $this->address_2 = $row['address_2'];
		  $this->city = $row['city'];
		  $this->state = $row['state'];
		  $this->zip = $row['zip'];
		  $this->phone_number = $row['phone_number'];
		  $this->fax_number = $row['fax_number'];
		  $this->email_address = $row['email_address'];
		  $this->account_enabled = $row['account_enabled'];
		  $this->image = $row['image'];
		  $this->last_ip = $row['last_ip'];
		  $this->last_login = $row['last_login'];
		  $this->last_session_id = $row['last_session_id'];
		  $this->allow_multiple_logins = $row['allow_multiple_logins'];
		  $this->authorization_code = $row['authorization_code'];
		  $this->email_authorized = $row['email_authorized'];
		} else {
		  $this->reset_vars();
		}	
	}
	
	// update customer password
	public function change_password_check() {
			global $dbh;
			
		$new_pass = $this->password;
			
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						password = ?
					 WHERE
						id = ?";
		$update_vals = array(
							encrypt_password($this->password),
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);					 
		$stmt->execute($update_vals);
		
		$this->get_db_vars($this->id);
		
		// add account password change email
		$message = new Mail_mime();
		$html = '<p>Your password has been changed to: '.$new_pass.'</p>';
		
		//$message->setTXTBody($text);
		$message->setHTMLBody($html);
		$body = $message->get();
		$extraheaders = array("From"=>SITE_FROM_ADDRESS, "Subject"=>SITE_NAME_VAL." Password Change ".date("m-d-Y"));
		$headers = $message->headers($extraheaders);
		
		$mail = Mail::factory("mail");
		$mail->send($this->email_address, $headers, $body);
	}
	
	// update certificate settings
	public function update_certificate_settings() {
			global $dbh;
			
		// build certificate requirements array
		if (is_array($_POST['certificate_levels'])) {
			foreach($_POST['certificate_levels'] as $id => $value) {
				$_POST['certificate_requirements'][$id]['type'] = $_POST['requirements'][$id];
				$_POST['certificate_requirements'][$id]['value'] = $_POST['requirement_text'][$id][$_POST['requirements'][$id]];
			}
		}
		$this->certificate_requirements = serialize($_POST['certificate_requirements']);

		// handles selected certificate levels
		$this->certificate_levels = serialize($_POST['certificate_levels']);
			
		$sql_query = "UPDATE
						".$this->tbl_nme."
					 SET
						certificate_levels = ?,
						certificate_requirements = ?,
						update_approval = 0
					 WHERE
						id = ?";
		$update_vals = array(
							$this->certificate_levels,
							$this->certificate_requirements,
							$this->id
							);
							
		$stmt = $dbh->prepare($sql_query);							
		$stmt->execute($update_vals);
	}
	
	// user login check
	public function user_forget_password_check() {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						email_address
					 FROM
						".$this->tbl_nme."
					 WHERE
						username = ? AND
						email_address = ? AND
						account_enabled = 1
					  LIMIT 1;";
					 
		$values = array(
						$_POST['username'],
						$_POST['email']
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		if ($row['id'] > 0) {
		
			$new_pass = rand(11111111,99999999);
		
			$sql_query = "UPDATE
							".$this->tbl_nme."
						 SET
							password = ?
						 WHERE
							id = ?";
			$update_vals = array(
								encrypt_password($new_pass),
								$row['id']
								);
			
			$stmt = $dbh->prepare($sql_query);					 
			$stmt->execute($update_vals);
			
			// add account password change email
			$message = new Mail_mime();
			$html = '<p>Your password has been changed to: '.$new_pass.'</p>';
			
			//$message->setTXTBody($text);
			$message->setHTMLBody($html);
			$body = $message->get();
			$extraheaders = array("From"=>SITE_FROM_ADDRESS, "Subject"=>SITE_NAME_VAL." Password Change ".date("m-d-Y"));
			$headers = $message->headers($extraheaders);
			
			$mail = Mail::factory("mail");
			$mail->send($row['email_address'], $headers, $body);
			
		}
		
	return $row['id'];
	}
	
	// user login check
	public function user_login_check() {
			global $dbh;
		
		$sql_query = "SELECT
						id,
						approved,
						customer_level,
						allow_multiple_logins
					 FROM
						".$this->tbl_nme."
					 WHERE
						username = ? AND
						password = ? AND
						account_enabled = 1
					  LIMIT 1;";
					 
		$values = array(
						$_POST['username'],
						encrypt_password($_POST['password'])
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		if ($row['id'] > 0) {
			$sql_query = "UPDATE
							".$this->tbl_nme."
						 SET
							last_ip = ?,
							last_login = ?,
							last_session_id = ?
						 WHERE
							id = ?";
			$update_vals = array(
								$_SERVER['REMOTE_ADDR'],
								date("Y-m-d"),
								session_id(),
								$row['id']
								);
			$stmt = $dbh->prepare($sql_query);					 
			$stmt->execute($update_vals);
			
			$_SESSION['advertiser_logged_in'] = 1;
			$_SESSION['advertiser_id'] = $row['id'];
			$_SESSION['approved'] = $row['approved'];
			$_SESSION['customer_level'] = $row['customer_level'];
			$_SESSION['allow_multiple_logins'] = $row['allow_multiple_logins'];
			$this->get_db_vars($row['id']);
		}

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	}
	
	// user login session check
	public function user_login_session_check() {
			global $dbh;
		
		// check if multiple logins are allowed
		if (empty($_SESSION['allow_multiple_logins'])) {
			$sql_query = "SELECT
							count(*) as rcount
						 FROM
							".$this->tbl_nme."
						 WHERE
							last_session_id = ? 
						 AND
							id = ?
						 AND
							last_ip = ?
						  LIMIT 1;";
						 
			$values = array(
							session_id(),
							$_SESSION['advertiser_id'],
							$_SERVER['REMOTE_ADDR']
							);
			
			$stmt = $dbh->prepare($sql_query);					 
			$result = $stmt->execute($values);
			
			$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
	
			$check_val = $row['rcount'];
	
			// clear result set
			$result->free();
		
			// reset DB conn
			db_check_conn();
		} else {
			$check_val = 1;
		}
	return $check_val;
	}
	
	// check for existing username
	public function username_check() {
		global $dbh;
		
		$values = array();
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE username = ? ";
				 
		if (!empty($_POST['id'])) {
			$sql_query .= "AND id <> ? ";
			$values[] = $_POST['id'];
		}
				 
		$sql_query .= " LIMIT 1;";
					 
		$values[] = $_POST['username'];
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);

		$row_count = $row['rcount'];

		// clear result set
		$result->free();
		
		// reset DB conn
		db_check_conn();
	
	return $row_count;
	}
	
	// generates customer affiliate code
	public function generate_affiliate_code() {
			global $dbh;
		
		$cur_affiliate_code = rand(11111111,99999999);
		
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						".$this->tbl_nme."
					 WHERE affiliate_code = ? 
					  LIMIT 1;";
	
		$values = array(
						$cur_affiliate_code
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$row_count = $row['rcount'];
		
		if (empty($row_count)) {
			$aff_code = $cur_affiliate_code;
		} else {
			$this->generate_affiliate_code();
		}

		// clear result set
		$result->free();
		
	return $aff_code;
	}
		
	// added to get long-lat data
	private function set_long_lat() {
		
		$obj_google=new googleRequest;
		
		$obj_google->address = $this->address_1;
		$obj_google->city = $this->city;
		$obj_google->zip = $this->zip;
		
		$obj_google->gKey=GOOGLE_MAPS_API_KEY;
		$latlng=$obj_google->GetRequest();
		//var_dump($latlng);  
		$this->longitude = $latlng[1];
		$this->latitude = $latlng[0];
		
	}

}

?>