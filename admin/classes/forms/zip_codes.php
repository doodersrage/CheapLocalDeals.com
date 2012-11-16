<?PHP

// display, add, and edit searchable zip code list

class zip_codes_frm {
	
	// delete zip codes
	function delete() {
	global $dbh;
		foreach($_POST['delete_zip'] as $selected_zips) {
			$stmt = $dbh->prepare("DELETE FROM zip_codes WHERE id = '".$selected_zips."';");
			$stmt->execute();
			$stmt = $dbh->prepare("DELETE FROM url_names WHERE type = 'zip' AND parent_id = '".$selected_zips."';");
			$stmt->execute();
		}
	}

	// load add zip code page
	function edit($message = '') {
		
		$add_zip_codes = open_table_form('Edit Zip Code','edit_zip_code',SITE_ADMIN_SSL_URL.'?sect=zipcodes&mode=editcheck','post',$message);
		$add_zip_codes .= $this->form();
		$add_zip_codes .= close_table_form();
		
		return $add_zip_codes;
	}
	
	// load add zip code page
	function add($message = '') {
		
		$add_zip_codes = open_table_form('Add New Zip Code','add_zip_code',SITE_ADMIN_SSL_URL.'?sect=zipcodes&mode=addcheck','post',$message);
		$add_zip_codes .= $this->form();
		$add_zip_codes .= close_table_form();
		
		return $add_zip_codes;
	}
	
	// generates a list of current csv documents
	function generate_existing_csv_list() {
		
		// open this directory 
		$myDirectory = opendir(SITE_ADMIN_CSV_DIR.'zipcodes/');
		
		// get each entry
		while($entryName = readdir($myDirectory)) {
			$dirArray[] = $entryName;
		}
		
		// close directory
		closedir($myDirectory);
		
		//	count elements in array
		$indexCount	= count($dirArray);
		//Print ("$indexCount files<br>\n");
		
		// sort 'em
		sort($dirArray);
		
		// print 'em
		$directory_list .= "<br><TABLE border=1 align='center' cellpadding=5 cellspacing=0 class=whitelinks>\n";
		$directory_list .= "<TR><TH>Filename</TH><th>Filesize</th><th>action</th></TR>\n";
		// loop through the array of files and print them all
		for($index=0; $index < $indexCount; $index++) {
				if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
				$directory_list .= "<TR><TD><a href=\"".SITE_ADMIN_SSL_URL."csvs/zipcodes/$dirArray[$index]\">$dirArray[$index]</a></td>";
				$directory_list .= "<td>";
				
				$current_file_size = filesize(SITE_ADMIN_CSV_DIR.'zipcodes/'.$dirArray[$index]);
				$current_file_size_k = round(($current_file_size/1024),2);
				$current_file_size_mb = round((($current_file_size/1024)/1024),2);
				if ($current_file_size_k > 1024) $set_file_size = $current_file_size_mb.' MB'; else $set_file_size = $current_file_size_k.' K';
				
				$directory_list .= $set_file_size;
				$directory_list .= "</td>";
				$directory_list .= "<td>";
				$directory_list .= "<a href=\"".SITE_ADMIN_SSL_URL."?sect=zipcodes&mode=download&deletefile=".$dirArray[$index]."\">Delete</a>";
				$directory_list .= "</td>";
				$directory_list .= "</TR>\n";
			}
		}
		$directory_list .= "</TABLE>\n";
		
		return $directory_list;
	}
	
	// uploads a new csv document
	function upload_csv() {
			global $zip_cds_tbl;
		
		// pull uploaded file name
		$csv_file = basename( $_FILES['csv_file']['name']);
		
		// set save path
		$target_path = SITE_ADMIN_CSV_DIR . 'temp/' . $csv_file; 
		move_uploaded_file($_FILES['csv_file']['tmp_name'], $target_path);
		
		try {
			$reader = new Csv_Reader($target_path);
		} catch (Csv_Exception_FileNotFound $e) {
			echo "<p class='error'>File could not be found</p>";
		}
		
		// cycles through uploaded document lines
		while ($cur_row = $reader->getRow()) {
			
			// clean url name
			$url_name = strtolower(preg_replace("/[^a-zA-Z0-9s]/", "-", $cur_row[13]));
				
			$zip_cds_tbl->id = (int)$cur_row[0];
			$zip_cds_tbl->zip = (int)$cur_row[1];
			$zip_cds_tbl->latitude = $cur_row[4];
			$zip_cds_tbl->longitude = $cur_row[5];
			$zip_cds_tbl->url_name = (existing_link_check($url_name,(int)$cur_row[0],'zip') > 0 ? $url_name : '');
			
			// if zip field is now empty update or insert contents
			if (!empty($zip_cds_tbl->zip)) {
				// if zip id is set update existing content
				if (!empty($zip_cds_tbl->id)) {
					if (existing_link_check($cats_tbl->url_name,$cats_tbl->id,'category') > 0) {
						// set url id if found
						$zip_cds_tbl->get_url_id((int)$cur_row[0]);
					}
					$zip_cds_tbl->update();
				// insert new zip
				} else {
					$zip_cds_tbl->url_id = '';
					$zip_cds_tbl->insert();
				}
			}
		}
		
		// remove temp document
		unlink($target_path);
		
	}
	
	// create CSV file for download
	function write_csv() {
			global $dbh;
		
		// maximum number or lines to read per run
		$run_limiter = 3000;

		// save file name and location
		$file = SITE_ADMIN_CSV_DIR.'zipcodes/zipcodes-'.date('m-d-y-G-i-s').'.csv';
		$zip_temp_file = SITE_ADMIN_CSV_DIR.'zipcodes/ziptemp.csv';
		
		// open csv writer class
		$writer = new Csv_Writer($zip_temp_file);
		
		// get total zip codes count
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						zip_codes
					 ;";
		$rows = $dbh->queryRow($sql_query);
		
		// set count to var
		$found_zips = $rows['rcount'];
		
		// set document header array
		$row = array('id', 'zip', 'city', 'state','latitude','longitude','header_title','meta_description','meta_keywords','page_header','page_description','url_name');
		$writer->writeRow($row);
		
		// write header to file
		$fp = fopen($file, 'w');
		
		$cur_row = 0;
		
		// cycle through the next 3000 entries
		for($cur_row = 0; $cur_row <= $found_zips; $cur_row += $run_limiter) {

			$rows = $this->csv_set($cur_row,$run_limiter);
			
			// transfer rows to data var
			$data = $rows;
			
			// write data to document
			$writer->writeRows($data);
			$writer->close();
						
			// start output buffer
			ob_start();
				
				// load template
				require($zip_temp_file);
				
				// capture outpur buffer to variable
				$theData = ob_get_contents();
			
			// close output buffer
			ob_end_clean();
				
			// write buffer to file		
			fwrite($fp, $theData.LB);
			
		}
		
		// close opened file
		fclose($fp);
		
		// delete temp file
		unlink($zip_temp_file);
		
	}
	
	// get current csv set
	function csv_set($cur_row,$run_limiter) {
			global $dbh, $url_nms_tbl;
			
		// get next set of data
		$sql_query = "SELECT
						id,
						zip,
						city,
						state,
						latitude,
						longitude,
						header_title,
						meta_description,
						meta_keywords,
						page_header,
						page_description,
						url_name
					 FROM
						zip_codes
					 LIMIT
						".$cur_row.",".$run_limiter."  
					 ;";

		$rows = $dbh->queryAll($sql_query);
		
		// clear export array
		$export_array = array();
		
		// runs through selected array and replaces url_name value
		foreach($rows as $id => $value) {
			$export_array[$id]['id'] = $value['id'];
			$export_array[$id]['zip'] = $value['zip'];
			$export_array[$id]['city'] = $value['city'];
			$export_array[$id]['state'] = $value['state'];
			$export_array[$id]['latitude'] = $value['latitude'];
			$export_array[$id]['longitude'] = $value['longitude'];
			$export_array[$id]['header_title'] = $value['header_title'];
			$export_array[$id]['meta_description'] = $value['meta_description'];
			$export_array[$id]['meta_keywords'] = $value['meta_keywords'];
			$export_array[$id]['page_header'] = $value['page_header'];
			$export_array[$id]['page_description'] = $value['page_description'];
			
			// check for assigned url name then post value
			$url_nms_tbl->reset_vars();
			$url_nms_tbl->get_db_vars($value['url_name']);
			
			// check for url name settings
			if ($url_nms_tbl->id != '') {
				$export_array[$id]['url_name'] = $url_nms_tbl->url_name;			
			} else {
				$export_array[$id]['url_name'] = '';			
			}
			
		}
		

	return $export_array;
	}
	
	// draw zip codes edit form
	function form() {
		global $zip_cds_tbl, $url_nms_tbl;
		
		$zip_codes_form = table_form_header('* indicates required field');
		$zip_codes_form = table_form_field('<span class="required">*Zip:</span>','<input name="zip" type="text" size="5" maxlength="5" value="'.$zip_cds_tbl->zip.'">');
		$zip_codes_form .= table_form_field('Latitude:','<input name="latitude" type="text" size="12" maxlength="12" value="'.$zip_cds_tbl->latitude.'" disabled > -- Will automatically be populated on submission.');
		$zip_codes_form .= table_form_field('Longitude:','<input name="longitude" type="text" size="12" maxlength="12" value="'.$zip_cds_tbl->longitude.'" disabled > -- Will automatically be populated on submission.');
				
		$zip_codes_form .= table_form_header('Header/Meta Data');
		
		// query url data
		$url_nms_tbl->get_db_vars($zip_cds_tbl->url_name);

		$zip_codes_form .= table_form_field('URL Name:','<script language="javascript">
function change_link_name(field_info){

var field_name = "#"+field_info;

jQuery(field_name).val(jQuery(field_name).val().replace(/[^a-zA-Z0-9_-]+/, "-")); 
jQuery(field_name).val(jQuery(field_name).val().replace(" ", "-")); 
}
</script>
<input type="hidden" name="url_id" value="'.$zip_cds_tbl->url_name.'">
<input name="url_name" id="url_name" type="text" size="60" value="'.$url_nms_tbl->url_name.'" onKeyUp="change_link_name(\'url_name\')">');
		
		$zip_codes_form .= table_span_form_field('<center><input name="id" type="hidden" value="'.$zip_cds_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
		
		return $zip_codes_form;
	}
		
	// check form submission values
	function form_check() {
			global $zip_cds_tbl;
		
		// required fields array
		$required_fields = array(
								'Zip' => $zip_cds_tbl->zip,
								);
			
		// check error values and write error array					
		foreach($required_fields as $field_name => $output) {

			if (empty($output)) {
				$errors_array[] = $field_name;
			}
		
		}
		
		// print errors
		if (!empty($errors_array)) {
			$error_message = 'You did not supply a value for these fields: ' . implode(', ',$errors_array);
		}
		
		// check for existing search friendly link
		if (existing_link_check($zip_cds_tbl->url_name,$zip_cds_tbl->id,'zip') > 0) {
			$error_message .= '<center>Same URL Name already exists within database. Please enter another.</center>'.LB;
		}
		
		return $error_message;
	}

}

?>