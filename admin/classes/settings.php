<?PHP

// edit site settings and save to static php file

// class loads and adjusts site settings
class settings {
	
	function load_selected_settings($id) {
			global $dbh;
		
		// pull and populate settings menu
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						settings
					 WHERE
						group_id = ?
					 ;";

		$values = array(
						$id
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$rows = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		if (!empty($rows['rcount'])) {
		
			$sql_query = "SELECT
							name,
							description
						 FROM
							settings_groups
						 WHERE
							id = ?
						 ;";
	
			$values = array(
							$id
							);
			
			$stmt = $dbh->prepare($sql_query);					 
			$result = $stmt->execute($values);
			
			$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
				
			$settings_view = '<div class="settings_title">'.$row['name'].'</div>';
			$settings_view .= '<div class="settings_desc">'.$row['description'].'</div>';
				
			$settings_view .= '<script type="text/javascript" src="js/settings.js"></script>'.LB
							.'<table class="settings_table" align="center">'.LB
							.'<tr><th width="20%">Title</th><th width="30%">Description</th><th width="50%">value</th><tr>'.LB;
			
			// load group settings
			// pull and populate settings menu
			$sql_query = "SELECT
							id,
							title,
							description,
							type,
							value
							
						 FROM
							settings
						 WHERE
							group_id = ?
						 ;";
	
			$values = array(
							$id
							);
			
			$stmt = $dbh->prepare($sql_query);					 
			$result = $stmt->execute($values);
	
			while ($fsettings = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				if ($_GET['sid'] != $fsettings['id']) {
					if(empty($_GET['sid'])) {
						$settings_view .= '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\''.SITE_ADMIN_SSL_URL.'?sect=settings&id='.$_GET['id'].'&sid='.$fsettings['id'].'\'">'.LB;
						$settings_view .= '<td><strong>'.$fsettings['title'].'</strong></td><td>'.$fsettings['description'].'</td><td>'.($fsettings['type'] == 'yndd' ? $fsettings['value'] == 1 ? 'Yes' : 'No' : nl2br(htmlspecialchars($fsettings['value']))).'</td></tr>'.LB;
					}
				} else {
					require(SITE_ADMIN_FUNCTIONS_DIR.'det_form_fld.php');
					$settings_view .= '<tr class="dataTableRowSelect">'.LB;
					$settings_view .= '<td><strong>'.$fsettings['title'].'</strong></td><td>'.$fsettings['description'].'</td><td>'.$this->settings_form($fsettings['value'],$fsettings['type']).'</td></tr>'.LB;
				}
			}
							
			$settings_view .= '</table>'.LB;
		} else {
			
			$settings_view = '<center><strong>No settings were found for the selected id</strong></center>'.LB;
		}
			
	return $settings_view;
	}
	
	
	// draw settings form
	function settings_form($field_value,$type) {
		
		$settings_form = '<center><form action="'.SITE_ADMIN_SSL_URL.'?sect=settings&id='.$_GET['id'].'" method="post">'.LB;
		$settings_form .= input_value($type,'setting_val',$field_value).LB;
		$settings_form .= '<input name="setting_id" value="'.$_GET['sid'].'" type="hidden"><br>'.LB;
		$settings_form .= '<input name="setting_submit" value="1" type="hidden">'.LB;
		$settings_form .= '<input name="Submit" value="Submit" type="submit">'.LB;
		$settings_form .= '</form></center>'.LB;
		
	return $settings_form;
	}
	
	
	// update settings val
	function update_settings() {
			global $dbh;
				
		$sql_query = "UPDATE
						settings
					 SET
						value = ?
					 WHERE
						id = ?
					 ;";
		$update_vals = array($_POST['setting_val'],(int)$_POST['setting_id']);
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
		
		$this->settings_file_update();
		
	}
	
	
	// update static settings file
	function settings_file_update() {
			global $dbh;
		
		$sql_query = "SELECT
						constant,
						value
					 FROM
						settings
					 ;";
		$rows = $dbh->queryAll($sql_query);
		
		$File = INCLUDES_DIR."settings.php";
		$Handle = fopen($File, 'w');
		
		$Data = "<?PHP ".LB;
		fwrite($Handle, $Data);
			
		foreach ($rows as $settings) {
			$Data = 'define("'.$settings['constant'].'","'.str_replace('"','\"',$settings['value']).'");'.LB;
			fwrite($Handle, $Data);
		}
		
		$Data = "?> ".LB;
		fwrite($Handle, $Data);
		
		fclose($Handle);
		
	}
		
}

?>