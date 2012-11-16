<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

echo '<table border="0" cellpadding="4" class="advertiser_form">
  <tr>
    <td width="150" valign="top"><div id="alt_message_area"></div><div id="select_alt_area">';
	
        $location_dd = '';
        $sql_query = "SELECT
						id,
						location_name
					 FROM
						advertiser_alt_locations
					 WHERE
                     	advertiser_id = ? ;";
		$values = array(
						$_SESSION['advertiser_id']
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$query = $stmt->execute($values);

		echo '<select name="alt_select_id" size="12" id="alt_select_id">';	
		  while($rows = $query->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			  echo '<option value="'.$rows['id'].'">'.$rows['location_name'].'</option>';
		  }
		echo '</select>';

echo '</div>
      <input type="button" name="Edit" id="alt_select_loc" value="Edit" onclick="alt_select_loc()"><input type="button" name="New" id="new_alt_loc" value="New" onclick="new_alt_loc()"><input name="Delete" type="button" value="Delete" id="alt_select_delete" onclick="alt_select_delete()" /><input name="alt_advert_id" id="alt_advert_id" type="hidden" value="'. $_SESSION['advertiser_id'] . '" /></td>
    <td id="alt_loc_form_area"></td>
  </tr>
</table>';
?>