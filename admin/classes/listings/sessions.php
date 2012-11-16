<?PHP

// modifies and displays existing sessions

// handles and displays pages admin page
class sessions_info_lst {
	
	// delete sessions
	function delete() {
		global $dbh;
		
		foreach($_POST['delete_sessions'] as $selected_pages) {
		  $stmt = $dbh->prepare("DELETE FROM sessions WHERE id = '".$selected_pages."';");
		  $stmt->execute();
		}
	}
	
	// display pages listing
	function listing($message = '') {
		$page_view .= $this->listing_content($message = '');
		$page_view .= close_table_form();
		
	return $page_view;
	}
	
	private function listing_content($message = '') {
			global $dbh, $pgs_tbl;
			
		// sets record limit per page	
		$page_limiter = ADMIN_PER_PAGE_RESULTS; 
		
		// table title array							
		$title_array = array(
							'Session ID',
							'Current Page',
							'Referrer',
							'Time',
							'Geo Location',
							'IP Address',
							'Type',
							'Delete Sessions',
							);

		// gets table boxes count
		$table_boxes_cnt = count($title_array);
		
		// draw listing head
		$pages_listing = open_table_listing_form('Sessions Listing','view_sessions','','post',$message,$table_boxes_cnt);
		
		// prints page links
		if (empty($_POST['search_box'])) {
			$sql_query = "SELECT
							count(*) as rcount
						 FROM
							sessions
						;";
			
			$rowscount = $dbh->queryRow($sql_query);
			
			$row_count = $rowscount['rcount'];
			$page_count = (int)$row_count/$page_limiter;
			
			for($i = 0;$i < $page_count;$i++) {
				$pages_array[] = '<a href="?sect=sessions&mode=view&page_val='.($i*$page_limiter).'">'.($_GET['page_val'] == $i*$page_limiter ? '<font color="red">' : '').($i+1).($_GET['page_val'] == $i*$page_limiter ? '</font>' : '').'</a>';
			}
			
			$pages_links = implode(', ',$pages_array);
			
			$pages_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		}
							
		// print title boxes
		$pages_listing .= draw_table_header($title_array);
		
		$sql_query = "SELECT
						id,
						session_id,
						time,
						cur_page,
						geo_loc,
						ip_address,
						referrer
					 FROM
						sessions";
		
		$sql_query .= "
					ORDER BY
						id ASC ";
					
		if (!empty($_GET['page_val']) && empty($_POST['search_box'])) {
		$sql_query .= "
				LIMIT ".$_GET['page_val'].",".$page_limiter."  ";
		} elseif (empty($_POST['search_box'])) {
		$sql_query .= "
				LIMIT
				".$page_limiter." ";
		}
					
		$sql_query .= ";";
			
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute();
		
		while($pages = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		
			// reset class var
			$class = '';
			$type = $this->spider_check($pages['ip_address']);
			// check for ip spider
			if($type != '') {
				$class = 'spider';
			}
		
			$row_array = array(
								$pages['session_id'],
								'<a target="_blank" href="'.$pages['cur_page'].'">'.$pages['cur_page'].'</a>',
								'<a target="_blank" href="'.$pages['referrer'].'">'.$pages['referrer'].'</a>',
								date('n/j/Y h:i:s A',$pages['time']),
								$pages['geo_loc'],
								$pages['ip_address'],
								$type,
								'<input name="delete_sessions[]" type="checkbox" value="'.$pages['id'].'">',
								);
				
			$pages_listing .= draw_table_contect($row_array,0,'center',$class);
		}
		
		$pages_listing .= draw_table_contect(array('<center>Pages:<br>'.$pages_links.'</center>'),$table_boxes_cnt,'center');
		
		$pages_listing .= draw_table_contect(array('<center><input name="delete_selected" type="hidden" value="1"><input name="submit" type="submit" value="Delete Selected"></center>'),$table_boxes_cnt,'center');
		
	return $pages_listing;
	}
	
	// check for ip spider
	private function spider_check($cur_ip) {
		
		$spider = '';

		if ($handle = opendir(SITE_ADMIN_DIR.'spider_lists/')) {
		
			/* cycle through spider definition files */
			while (false !== ($file = readdir($handle))) {
			
				$rh = fopen(SITE_ADMIN_DIR.'spider_lists/'.$file, 'r');
				while (!feof($rh)) {
					// pull line
					$line = trim(fgets($rh));
					// set ip array
					$spide_ip = explode('.',$line);
					$capt_ip = explode('.',$cur_ip);
					// get ip count
					$quart_cnt = count($spide_ip);
					$new_cnt = 0;
					$found_cnt = 0;
					foreach($spide_ip as $cur_quart) {
						if(!empty($capt_ip[$new_cnt])) if ($capt_ip[$new_cnt] == $cur_quart) $found_cnt++;
						$new_cnt++;
					}
					if($found_cnt == $quart_cnt) $spider = $file;
				}
				fclose($rh);
				
			}
		
			closedir($handle);
		}
		
		// remove file extension
		$spider = explode('.',$spider);
		$spider = $spider[0];
		
	return $spider;
	}
		
}

?>